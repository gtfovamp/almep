<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Subcategory;
use App\Models\ProductType;

class ProductWebController extends AdminCrudController
{
    protected string $modelClass = Product::class;
    protected string $routeBase = 'products';
    protected string $active = 'products';
    protected string $titleSingular = 'Товар';
    protected string $titlePlural = 'Товары';
    protected array $with = ['subcategory', 'productType'];

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
    protected function fields(): array
    {
        $subs  = Subcategory::orderBy('name')->pluck('name', 'id')->toArray();
        $types = ProductType::orderBy('name')->pluck('name', 'id')->toArray();
        return [
            ['name'=>'article','label'=>'Артикул','type'=>'text','required'=>true],
            ['name'=>'name','label'=>'Название','type'=>'trans','input'=>'text','required'=>true],
            ['name'=>'description','label'=>'Описание','type'=>'trans','input'=>'textarea'],
            ['name'=>'subcategory_id','label'=>'Подкатегория','type'=>'select','options'=>$subs,'rule'=>'integer|exists:subcategories,id'],
            ['name'=>'product_type_id','label'=>'Тип товара','type'=>'select','options'=>$types,'rule'=>'integer|exists:product_types,id'],
            ['name'=>'in_stock','label'=>'В наличии','type'=>'checkbox'],
            ['name'=>'order_index','label'=>'Порядок','type'=>'number'],
        ];
    }
}
