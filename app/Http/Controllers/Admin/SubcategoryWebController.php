<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subcategory;
use App\Models\Category;

class SubcategoryWebController extends AdminCrudController
{
    protected string $modelClass = Subcategory::class;
    protected string $routeBase = 'subcategories';
    protected string $active = 'subcategories';
    protected string $titleSingular = 'Подкатегория';
    protected string $titlePlural = 'Подкатегории';
    protected array $with = ['category'];

    protected function columns(): array
    {
        return [
            'image_url' => ['label' => 'Фото', 'type' => 'image'],
            'name'      => 'Название',
            'category.name' => 'Категория',
            'order_index' => 'Порядок',
        ];
    }
    protected function fields(): array
    {
        $cats = Category::orderBy('order_index')->pluck('name', 'id')->toArray();
        return [
            ['name'=>'category_id','label'=>'Категория','type'=>'select','options'=>$cats,'required'=>true,'rule'=>'integer|exists:categories,id'],
            ['name'=>'name','label'=>'Название','type'=>'trans','input'=>'text','required'=>true],
            ['name'=>'description','label'=>'Описание','type'=>'trans','input'=>'textarea'],
            ['name'=>'image_url','label'=>'Изображение','type'=>'image','folder'=>'subcategories'],
            ['name'=>'order_index','label'=>'Порядок','type'=>'number'],
        ];
    }
}
