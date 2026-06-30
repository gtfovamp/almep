<?php

namespace App\Http\Controllers\Admin;

use App\Models\Partner;

class PartnerWebController extends AdminCrudController
{
    protected string $modelClass = Partner::class;
    protected string $routeBase = 'partners';
    protected string $active = 'partners';
    protected string $titleSingular = 'Партнёр';
    protected string $titlePlural = 'Партнёры';

    protected function columns(): array
    {
        return [
            'image_url' => ['label' => 'Лого', 'type' => 'image'],
            'name'      => 'Название',
            'order_index' => 'Порядок',
        ];
    }
    protected function fields(): array
    {
        return [
            ['name'=>'name','label'=>'Название','type'=>'trans','input'=>'text','required'=>true],
            ['name'=>'description','label'=>'Описание','type'=>'trans','input'=>'textarea'],
            ['name'=>'image_url','label'=>'Логотип','type'=>'image','folder'=>'partners','required'=>true],
            ['name'=>'order_index','label'=>'Порядок сортировки','type'=>'number','hint'=>'Меньше = выше в списке'],
        ];
    }
}
