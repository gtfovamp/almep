<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * Generic admin CRUD controller. Subclasses configure model, fields, columns.
 * Renders shared Blade views: admin.crud.index & admin.crud.form.
 */
abstract class AdminCrudController extends Controller
{
    /** @var class-string<Model> */
    protected string $modelClass;
    protected string $routeBase;   // e.g. 'partners' -> /admin/partners
    protected string $active;      // sidebar key
    protected string $titleSingular;
    protected string $titlePlural;
    protected string $orderBy = 'order_index';
    protected string $orderDir = 'asc';
    protected array $with = [];

    /** Columns for the list table: ['field' => 'Label'] or ['field' => ['label'=>..,'type'=>'image|bool|text']] */
    abstract protected function columns(): array;

    /** Form fields config. See README of structure in form.blade.php */
    abstract protected function fields(): array;

    protected function service(): ImageUploadService { return app(ImageUploadService::class); }

    protected function viewData(): array
    {
        return [
            'routeBase'     => $this->routeBase,
            'active'        => $this->active,
            'titleSingular' => $this->titleSingular,
            'titlePlural'   => $this->titlePlural,
            'columns'       => $this->columns(),
            'fields'        => $this->fields(),
        ];
    }

    public function index()
    {
        $q = ($this->modelClass)::query();
        if ($this->with) $q->with($this->with);
        if ($this->orderBy) $q->orderBy($this->orderBy, $this->orderDir);
        $items = $q->get();
        return view('admin.crud.index', array_merge($this->viewData(), compact('items')));
    }

    public function create()
    {
        $item = new ($this->modelClass);
        return view('admin.crud.form', array_merge($this->viewData(), ['item' => $item, 'mode' => 'create']));
    }

    public function store(Request $request)
    {
        $data = $this->validateAndPrepare($request, null);
        $item = ($this->modelClass)::create($data);
        return redirect(url('/admin/' . $this->routeBase))->with('success', $this->titleSingular . ' создан(а).');
    }

    public function edit(int $id)
    {
        $item = ($this->modelClass)::findOrFail($id);
        return view('admin.crud.form', array_merge($this->viewData(), ['item' => $item, 'mode' => 'edit']));
    }

    public function update(Request $request, int $id)
    {
        $item = ($this->modelClass)::findOrFail($id);
        $data = $this->validateAndPrepare($request, $item);
        $item->update($data);
        return redirect(url('/admin/' . $this->routeBase))->with('success', $this->titleSingular . ' обновлён(а).');
    }

    public function destroy(int $id)
    {
        $item = ($this->modelClass)::findOrFail($id);
        // remove owned image files
        foreach ($this->fields() as $f) {
            if (($f['type'] ?? '') === 'image') {
                $this->service()->deleteByRelativePath($item->{$f['name']} ?? null);
            }
        }
        $item->delete();
        return back()->with('success', $this->titleSingular . ' удалён(а).');
    }

    /** Build validation rules from fields config + handle images, translations, json. */
    protected function validateAndPrepare(Request $request, ?Model $existing): array
    {
        $rules = [];
        $names = [];

        foreach ($this->fields() as $f) {
            $type = $f['type'] ?? 'text';
            $req  = ($f['required'] ?? false) && ! $existing ? 'required' : 'nullable';

            if ($type === 'trans') {
                foreach (['', '_en', '_az'] as $suf) {
                    $n = $f['name'] . $suf;
                    $names[] = $n;
                    $rules[$n] = ($suf === '' ? $req : 'nullable') . '|string';
                }
            } elseif ($type === 'image') {
                $names[] = $f['name'];
                $rules[$f['name']] = 'nullable'; // file or url handled below
                $rules[$f['name'] . '_file'] = 'nullable|file|max:20480';
            } elseif ($type === 'number') {
                $names[] = $f['name']; $rules[$f['name']] = 'nullable|integer';
            } elseif ($type === 'checkbox') {
                $names[] = $f['name']; $rules[$f['name']] = 'nullable|boolean';
            } elseif ($type === 'select') {
                $names[] = $f['name']; $rules[$f['name']] = $req . '|' . ($f['rule'] ?? 'nullable');
            } elseif ($type === 'datetime') {
                $names[] = $f['name']; $rules[$f['name']] = $req . '|date';
            } elseif ($type === 'json') {
                $names[] = $f['name']; $rules[$f['name']] = $req . '|string';
            } else { // text, textarea
                $names[] = $f['name']; $rules[$f['name']] = $req . '|string';
            }
        }

        $validated = $request->validate($rules);
        $data = [];

        foreach ($this->fields() as $f) {
            $type = $f['type'] ?? 'text';
            $n = $f['name'];

            if ($type === 'trans') {
                foreach (['', '_en', '_az'] as $suf) {
                    $key = $n . $suf;
                    if ($request->has($key)) $data[$key] = $request->input($key);
                }
            } elseif ($type === 'image') {
                $file = $request->file($n . '_file');
                $url  = $request->input($n);
                $resolved = $this->service()->resolve($file, is_string($url) ? $url : null, $f['folder'] ?? 'misc');
                if ($resolved !== null) {
                    if ($existing && $existing->{$n} && $existing->{$n} !== $resolved) {
                        $this->service()->deleteByRelativePath($existing->{$n});
                    }
                    $data[$n] = $resolved;
                }
            } elseif ($type === 'checkbox') {
                $data[$n] = $request->boolean($n) ? 1 : 0;
            } elseif ($type === 'json') {
                $raw = $request->input($n);
                if ($raw !== null && $raw !== '') {
                    $dec = json_decode($raw, true);
                    $data[$n] = json_last_error() === JSON_ERROR_NONE ? $dec : $raw;
                } elseif (! $existing) {
                    $data[$n] = [];
                }
            } elseif ($type === 'number') {
                if ($request->has($n)) $data[$n] = (int) $request->input($n, 0);
            } else {
                if ($request->has($n)) $data[$n] = $request->input($n);
            }
        }

        // auto order_index for new records that have the column but no value
        if (! $existing && in_array('order_index', $names) && empty($data['order_index'])) {
            $max = ($this->modelClass)::max('order_index');
            $data['order_index'] = (int) $max + 1;
        }

        return $data;
    }
}
