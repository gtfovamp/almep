<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductType;

class ProductTypeWebController extends AdminCrudController
{
    protected string $modelClass = ProductType::class;
    protected string $routeBase = 'product-types';
    protected string $active = 'product-types';
    protected string $titleSingular = 'Тип товара';
    protected string $titlePlural = 'Типы товаров';
    protected string $orderBy = 'id';

    protected function columns(): array
    {
        return ['name' => 'Название (RU)', 'name_en' => 'EN', 'name_az' => 'AZ'];
    }
    protected function fields(): array
    {
        return [
            ['name'=>'name','label'=>'Название','type'=>'trans','input'=>'text','required'=>true],
        ];
    }
}
