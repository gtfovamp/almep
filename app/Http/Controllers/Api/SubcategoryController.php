<?php

namespace App\Http\Controllers\Api;

use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends BaseCrudController
{
    protected string $modelClass = Subcategory::class;
    protected array $with = ['category'];
    protected array $imageFields = ['image_url' => 'subcategories'];

    protected function applyFilters($query, Request $request): void
    {
        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }
    }

    protected function storeRules(): array
    {
        return [
            'category_id'     => 'nullable|integer|exists:categories,id',
            'name'            => 'required|string',
            'name_en'         => 'required|string',
            'name_az'         => 'required|string',
            'order_index'     => 'nullable|integer',
            'image_url'       => 'nullable',
            'description'     => 'nullable|string',
            'description_en'  => 'nullable|string',
            'description_az'  => 'nullable|string',
        ];
    }

    protected function updateRules(): array
    {
        return array_map(fn($r) => str_replace('required', 'sometimes', $r), $this->storeRules());
    }
}
