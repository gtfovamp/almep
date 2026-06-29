<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * Reusable CRUD controller.
 * Subclasses configure: model class, validation rules, image fields, upload folder, relations.
 */
abstract class BaseCrudController extends Controller
{
    /** @var class-string<Model> */
    protected string $modelClass;

    /** Default ordering column. */
    protected string $orderBy = 'order_index';
    protected string $orderDir = 'asc';

    /** Relations eager-loaded on index/show. */
    protected array $with = [];

    /** Image fields => upload sub-folder. e.g. ['image_url' => 'partners'] */
    protected array $imageFields = [];

    /** Validation rules for store. */
    protected function storeRules(): array { return []; }

    /** Validation rules for update (usually same but with 'sometimes'). */
    protected function updateRules(): array { return $this->storeRules(); }

    protected function service(): ImageUploadService
    {
        return app(ImageUploadService::class);
    }

    protected function baseQuery()
    {
        $q = ($this->modelClass)::query();
        if (! empty($this->with)) $q->with($this->with);
        return $q;
    }

    public function index(Request $request)
    {
        $q = $this->baseQuery();
        $this->applyFilters($q, $request);
        if ($this->orderBy) $q->orderBy($this->orderBy, $this->orderDir);

        if ($request->boolean('paginate')) {
            return response()->json($q->paginate((int) $request->input('per_page', 20)));
        }
        return response()->json($q->get());
    }

    public function show(int $id)
    {
        $item = $this->baseQuery()->findOrFail($id);
        return response()->json($item);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->storeRules());
        $data = $this->handleImages($request, $data);
        $data = $this->beforeSave($data, $request, null);
        $item = ($this->modelClass)::create($data);
        return response()->json($this->baseQuery()->find($item->getKey()), 201);
    }

    public function update(Request $request, int $id)
    {
        $item = ($this->modelClass)::findOrFail($id);
        $data = $request->validate($this->updateRules());
        $data = $this->handleImages($request, $data, $item);
        $data = $this->beforeSave($data, $request, $item);
        $item->update($data);
        return response()->json($this->baseQuery()->find($item->getKey()));
    }

    public function destroy(int $id)
    {
        $item = ($this->modelClass)::findOrFail($id);
        // delete owned image files
        foreach (array_keys($this->imageFields) as $field) {
            $this->service()->deleteByRelativePath($item->{$field} ?? null);
        }
        $item->delete();
        return response()->json(['message' => 'Удалено.']);
    }

    /** POST /reorder  body: { items: [{id, order_index}, ...] } */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items'               => 'required|array',
            'items.*.id'          => 'required|integer',
            'items.*.order_index' => 'required|integer',
        ]);
        foreach ($data['items'] as $row) {
            ($this->modelClass)::where('id', $row['id'])->update(['order_index' => $row['order_index']]);
        }
        return response()->json(['message' => 'Порядок обновлён.']);
    }

    /** Handle multipart file OR *_url string for each configured image field. */
    protected function handleImages(Request $request, array $data, ?Model $existing = null): array
    {
        foreach ($this->imageFields as $field => $folder) {
            $file = $request->file($field);
            $url  = $request->input($field);
            $resolved = $this->service()->resolve($file, is_string($url) ? $url : null, $folder);
            if ($resolved !== null) {
                // delete old file if replaced
                if ($existing && $existing->{$field} && $existing->{$field} !== $resolved) {
                    $this->service()->deleteByRelativePath($existing->{$field});
                }
                $data[$field] = $resolved;
            } else {
                // nothing provided — don't overwrite on update
                unset($data[$field]);
            }
        }
        return $data;
    }

    /** Hook for subclasses (e.g. json encode, defaults). */
    protected function beforeSave(array $data, Request $request, ?Model $existing): array
    {
        return $data;
    }

    /** Hook for subclasses to add query filters. */
    protected function applyFilters($query, Request $request): void {}
}
