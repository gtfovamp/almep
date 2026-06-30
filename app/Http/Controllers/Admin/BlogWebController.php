<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;

class BlogWebController extends AdminCrudController
{
    protected string $modelClass = Blog::class;
    protected string $routeBase = 'blog';
    protected string $active = 'blog';
    protected string $titleSingular = 'Статья';
    protected string $titlePlural = 'Блог';
    protected string $orderBy = 'published_at';
    protected string $orderDir = 'desc';

    protected function columns(): array
    {
        return [
            'cover_image_url' => ['label' => 'Обложка', 'type' => 'image'],
            'title'           => 'Заголовок',
            'published_at'    => ['label' => 'Опубликовано', 'type' => 'date'],
        ];
    }
    protected function fields(): array
    {
        return [
            ['name'=>'title','label'=>'Заголовок','type'=>'trans','input'=>'text','required'=>true],
            ['name'=>'cover_image_url','label'=>'Обложка','type'=>'image','folder'=>'blog','required'=>true],
            ['name'=>'published_at','label'=>'Дата публикации','type'=>'datetime','required'=>true],
            ['name'=>'blocks','label'=>'Контент (блоки)','type'=>'json','required'=>true],
            ['name'=>'order_index','label'=>'Порядок','type'=>'number'],
        ];
    }
}
