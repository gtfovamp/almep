<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseCrudController
{
    protected string $modelClass = Product::class;
    protected array $with = ['subcategory', 'productType', 'images', 'specifications'];

    protected function applyFilters($query, Request $request): void
    {
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', (int) $request->input('subcategory_id'));
        }
        if ($request->filled('product_type_id')) {
            $query->where('product_type_id', (int) $request->input('product_type_id'));
        }
        if ($request->has('in_stock') && $request->input('in_stock') !== '') {
            $query->where('in_stock', $request->boolean('in_stock') ? 1 : 0);
        }
        if ($request->filled('category_id')) {
            $catId = (int) $request->input('category_id');
            $query->whereHas('subcategory', fn($q) => $q->where('category_id', $catId));
        }
        if ($request->filled('search')) {
            $s = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('name_en', 'like', $s)
                  ->orWhere('name_az', 'like', $s)
                  ->orWhere('article', 'like', $s);
            });
        }
    }

    protected function storeRules(): array
    {
        return [
            'subcategory_id'  => 'nullable|integer|exists:subcategories,id',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'article'         => 'required|string|unique:products,article',
            'name'            => 'required|string',
            'name_en'         => 'required|string',
            'name_az'         => 'required|string',
            'in_stock'        => 'nullable|boolean',
            'order_index'     => 'nullable|integer',
            'description'     => 'nullable|string',
            'description_en'  => 'nullable|string',
            'description_az'  => 'nullable|string',
        ];
    }

    public function update(Request $request, int $id)
    {
        $item = Product::findOrFail($id);
        $data = $request->validate([
            'subcategory_id'  => 'nullable|integer|exists:subcategories,id',
            'product_type_id' => 'nullable|integer|exists:product_types,id',
            'article'         => 'sometimes|string|unique:products,article,' . $id,
            'name'            => 'sometimes|string',
            'name_en'         => 'sometimes|string',
            'name_az'         => 'sometimes|string',
            'in_stock'        => 'nullable|boolean',
            'order_index'     => 'nullable|integer',
            'description'     => 'nullable|string',
            'description_en'  => 'nullable|string',
            'description_az'  => 'nullable|string',
        ]);
        $item->update($data);
        return response()->json($this->baseQuery()->find($item->id));
    }
}
