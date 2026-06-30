<?php

namespace App\Http\Controllers\Admin;

use App\Models\Testimonial;

class TestimonialWebController extends AdminCrudController
{
    protected string $modelClass = Testimonial::class;
    protected string $routeBase = 'testimonials';
    protected string $active = 'testimonials';
    protected string $titleSingular = 'Отзыв';
    protected string $titlePlural = 'Отзывы';

    protected function columns(): array
    {
        return [
            'name'     => 'Имя',
            'text'     => 'Текст',
            'approved' => ['label' => 'Одобрен', 'type' => 'bool'],
            'order_index' => 'Порядок',
        ];
    }
    protected function fields(): array
    {
        return [
            ['name'=>'name','label'=>'Имя','type'=>'trans','input'=>'text','required'=>true],
            ['name'=>'text','label'=>'Текст отзыва','type'=>'trans','input'=>'textarea','required'=>true],
            ['name'=>'approved','label'=>'Одобрен (показывать на сайте)','type'=>'checkbox'],
            ['name'=>'order_index','label'=>'Порядок','type'=>'number'],
        ];
    }
}
