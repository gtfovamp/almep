<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductType;

class ProductTypeController extends BaseCrudController
{
    protected string $modelClass = ProductType::class;
    protected string $orderBy = 'id';

    protected function storeRules(): array
    {
        return [
            'name'    => 'required|string',
            'name_en' => 'required|string',
            'name_az' => 'required|string',
        ];
    }

    protected function updateRules(): array
    {
        return [
            'name'    => 'sometimes|string',
            'name_en' => 'sometimes|string',
            'name_az' => 'sometimes|string',
        ];
    }
}
