<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\Category;
use App\Models\Subcategory;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductWebController extends AdminCrudController
{
    protected string $modelClass = Product::class;
    protected string $routeBase = 'products';
    protected string $active = 'products';
    protected string $titleSingular = 'Товар';
    protected string $titlePlural = 'Товары';
    protected array $with = ['subcategory'];

    protected function columns(): array
    {
        return [
            'article'  => 'Артикул',
            'name'     => 'Название',
            'subcategory.name' => 'Подкатегория',
            'in_stock' => ['label' => 'В наличии', 'type' => 'bool'],
            'order_index' => 'Порядок',
        ];
    }
    protected function fields(): array { return []; }

    private function formData($item): array
    {
        $categories = Category::orderBy('order_index')->pluck('name', 'id')->toArray();
        $subs = Subcategory::orderBy('name')->get(['id','name','category_id']);
        $subsByCategory = [];
        foreach ($subs as $s) {
            $subsByCategory[(string) $s->category_id][] = ['id' => $s->id, 'name' => $s->name];
        }
        // derive category_id from current subcategory if present
        if ($item->subcategory_id && !$item->getAttribute('category_id')) {
            $sub = Subcategory::find($item->subcategory_id);
            $item->category_id = $sub->category_id ?? null;
        }
        return compact('categories', 'subsByCategory');
    }

    public function create()
    {
        $item = new Product();
        return view('admin.products.form', array_merge([
            'mode' => 'create', 'item' => $item,
            'imagesData' => [], 'specsData' => [],
        ], $this->formData($item)));
    }

    public function edit($id)
    {
        $item = Product::with(['images', 'specifications'])->findOrFail($id);
        return view('admin.products.form', array_merge([
            'mode' => 'edit', 'item' => $item,
            'imagesData' => $item->images->map(fn($i) => ['id'=>$i->id, 'url'=>$i->url ?: $i->image_url, 'is_primary'=>$i->is_primary])->values(),
            'specsData'  => $item->specifications->map(fn($s) => [
                'key'=>$s->key,'key_en'=>$s->key_en,'key_az'=>$s->key_az,
                'value'=>$s->value,'value_en'=>$s->value_en,'value_az'=>$s->value_az,
            ])->values(),
        ], $this->formData($item)));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $product = Product::create($data);
        $this->syncImages($product, $request);
        $this->syncSpecs($product, $request);
        return redirect('/admin/products')->with('success', 'Товар создан');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $this->validateProduct($request);
        $product->update($data);
        $this->syncImages($product, $request);
        $this->syncSpecs($product, $request);
        return redirect('/admin/products')->with('success', 'Товар обновлён');
    }

    private function validateProduct(Request $request): array
    {
        $v = $request->validate([
            'article'         => 'required|string|max:255',
            'name'            => 'required|string|max:255',
            'name_en'         => 'nullable|string|max:255',
            'name_az'         => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'description_en'  => 'nullable|string',
            'description_az'  => 'nullable|string',
            'category_id'     => 'nullable|integer|exists:categories,id',
            'subcategory_id'  => 'nullable|integer|exists:subcategories,id',
            'order_index'     => 'nullable|integer',
        ]);
        $v['in_stock'] = $request->boolean('in_stock') ? 1 : 0;
        if (empty($v['order_index'])) {
            $v['order_index'] = (int) Product::max('order_index') + 1;
        }
        unset($v['category_id']); // products table stores only subcategory_id
        return $v;
    }

    private function syncImages(Product $product, Request $request): void
    {
        $incoming = json_decode($request->input('images_json', '[]'), true) ?: [];
        $keepUrls = array_filter(array_column($incoming, 'url'));

        // delete removed images (and their files)
        foreach ($product->images as $existing) {
            $url = $existing->url ?: $existing->image_url;
            if (!in_array($url, $keepUrls, true)) {
                $existing->delete();
            }
        }
        $existingByUrl = $product->images()->get()->keyBy(fn($i) => $i->url ?: $i->image_url);

        foreach ($incoming as $row) {
            $url = $row['url'] ?? null;
            if (!$url) continue;
            $attrs = [
                'url'         => $url,
                'image_url'   => $url,
                'is_primary'  => !empty($row['is_primary']) ? 1 : 0,
                'order_index' => (int) ($row['order_index'] ?? 0),
            ];
            if (isset($existingByUrl[$url])) {
                $existingByUrl[$url]->update($attrs);
            } else {
                $product->images()->create($attrs);
            }
        }
    }

    private function syncSpecs(Product $product, Request $request): void
    {
        $incoming = json_decode($request->input('specs_json', '[]'), true) ?: [];
        $product->specifications()->delete();
        foreach ($incoming as $row) {
            if (empty($row['key']) && empty($row['value'])) continue;
            $product->specifications()->create([
                'key'         => $row['key'] ?? '',
                'key_en'      => $row['key_en'] ?? '',
                'key_az'      => $row['key_az'] ?? '',
                'value'       => $row['value'] ?? '',
                'value_en'    => $row['value_en'] ?? '',
                'value_az'    => $row['value_az'] ?? '',
                'order_index' => (int) ($row['order_index'] ?? 0),
            ]);
        }
    }
}
