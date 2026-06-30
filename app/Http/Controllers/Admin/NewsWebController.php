<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;

class NewsWebController extends AdminCrudController
{
    protected string $modelClass = News::class;
    protected string $routeBase = 'news';
    protected string $active = 'news';
    protected string $titleSingular = 'Новость';
    protected string $titlePlural = 'Новости';
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
            ['name'=>'cover_image_url','label'=>'Обложка','type'=>'image','folder'=>'news','required'=>true],
            ['name'=>'published_at','label'=>'Дата публикации','type'=>'datetime','required'=>true],
            ['name'=>'blocks','label'=>'Контент (блоки)','type'=>'json','required'=>true],
            ['name'=>'order_index','label'=>'Порядок','type'=>'number'],
        ];
    }
}
