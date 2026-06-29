<?php

namespace App\Http\Controllers\Api;

use App\Models\Certificate;

class CertificateController extends BaseCrudController
{
    protected string $modelClass = Certificate::class;
    protected array $imageFields = ['image_url' => 'certificates'];

    protected function storeRules(): array
    {
        return [
            'title'       => 'required|string',
            'title_en'    => 'nullable|string',
            'title_az'    => 'nullable|string',
            'image_url'   => 'required',
            'order_index' => 'nullable|integer',
        ];
    }

    protected function updateRules(): array
    {
        return [
            'title'       => 'sometimes|string',
            'title_en'    => 'nullable|string',
            'title_az'    => 'nullable|string',
            'image_url'   => 'nullable',
            'order_index' => 'nullable|integer',
        ];
    }
}
