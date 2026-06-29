<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;

class CategoryController extends BaseCrudController
{
    protected string $modelClass = Category::class;
    protected array $with = ['subcategories'];
    protected array $imageFields = ['icon_url' => 'categories'];

    protected function storeRules(): array
    {
        return [
            'name'            => 'required|string',
            'name_en'         => 'required|string',
            'name_az'         => 'required|string',
            'order_index'     => 'nullable|integer',
            'description'     => 'nullable|string',
            'description_en'  => 'nullable|string',
            'description_az'  => 'nullable|string',
            'icon_url'        => 'nullable',
        ];
    }

    protected function updateRules(): array
    {
        return [
            'name'            => 'sometimes|string',
            'name_en'         => 'sometimes|string',
            'name_az'         => 'sometimes|string',
            'order_index'     => 'nullable|integer',
            'description'     => 'nullable|string',
            'description_en'  => 'nullable|string',
            'description_az'  => 'nullable|string',
            'icon_url'        => 'nullable',
        ];
    }
}
