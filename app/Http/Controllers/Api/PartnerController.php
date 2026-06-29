<?php

namespace App\Http\Controllers\Api;

use App\Models\Partner;

class PartnerController extends BaseCrudController
{
    protected string $modelClass = Partner::class;
    protected array $imageFields = ['image_url' => 'partners'];

    protected function storeRules(): array
    {
        return [
            'name'           => 'required|string',
            'name_en'        => 'nullable|string',
            'name_az'        => 'nullable|string',
            'description'    => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_az' => 'nullable|string',
            'image_url'      => 'required',
            'order_index'    => 'nullable|integer',
        ];
    }

    protected function updateRules(): array
    {
        $r = $this->storeRules();
        $r['name'] = 'sometimes|string';
        $r['image_url'] = 'nullable';
        return $r;
    }
}
