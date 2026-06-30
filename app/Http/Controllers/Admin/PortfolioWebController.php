<?php

namespace App\Http\Controllers\Admin;

use App\Models\Portfolio;

class PortfolioWebController extends AdminCrudController
{
    protected string $modelClass = Portfolio::class;
    protected string $routeBase = 'portfolio';
    protected string $active = 'portfolio';
    protected string $titleSingular = 'Проект';
    protected string $titlePlural = 'Портфолио';

    protected function columns(): array
    {
        return [
            'image_url' => ['label' => 'Фото', 'type' => 'image'],
            'title'     => 'Название',
            'year'      => 'Год',
            'order_index' => 'Порядок',
        ];
    }
    protected function fields(): array
    {
        return [
            ['name'=>'title','label'=>'Название','type'=>'trans','input'=>'text','required'=>true],
            ['name'=>'description','label'=>'Описание','type'=>'trans','input'=>'textarea'],
            ['name'=>'year','label'=>'Год','type'=>'text','required'=>true],
            ['name'=>'image_url','label'=>'Изображение','type'=>'image','folder'=>'portfolio','required'=>true],
            ['name'=>'order_index','label'=>'Порядок','type'=>'number'],
        ];
    }
}
