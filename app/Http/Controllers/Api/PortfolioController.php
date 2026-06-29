<?php

namespace App\Http\Controllers\Api;

use App\Models\Portfolio;

class PortfolioController extends BaseCrudController
{
    protected string $modelClass = Portfolio::class;
    protected array $imageFields = ['image_url' => 'portfolio'];

    protected function storeRules(): array
    {
        return [
            'title'          => 'required|string',
            'title_en'       => 'nullable|string',
            'title_az'       => 'nullable|string',
            'year'           => 'required|string',
            'image_url'      => 'required',
            'description'    => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_az' => 'nullable|string',
            'order_index'    => 'nullable|integer',
        ];
    }

    protected function updateRules(): array
    {
        $r = $this->storeRules();
        $r['title'] = 'sometimes|string';
        $r['year'] = 'sometimes|string';
        $r['image_url'] = 'nullable';
        return $r;
    }
}
