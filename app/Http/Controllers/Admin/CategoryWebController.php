<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;

class CategoryWebController extends AdminCrudController
{
    protected string $modelClass = Category::class;
    protected string $routeBase = 'categories';
    protected string $active = 'categories';
    protected string $titleSingular = 'Категория';
    protected string $titlePlural = 'Категории';

    protected function columns(): array
    {
        return [
            'icon_url' => ['label' => 'Иконка', 'type' => 'image'],
            'name'     => 'Название',
            'order_index' => 'Порядок',
        ];
    }
    protected function fields(): array
    {
        return [
            ['name'=>'name','label'=>'Название','type'=>'trans','input'=>'text','required'=>true],
            ['name'=>'description','label'=>'Описание','type'=>'trans','input'=>'textarea'],
            ['name'=>'icon_url','label'=>'Иконка','type'=>'image','folder'=>'categories'],
            ['name'=>'order_index','label'=>'Порядок','type'=>'number'],
        ];
    }
}
