<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

abstract class ArticleCrudController extends AdminCrudController
{
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

    // Used only for validation rules (the actual UI is the custom block editor).
    protected function fields(): array
    {
        return [
            ['name'=>'title','label'=>'Заголовок','type'=>'trans','input'=>'text','required'=>true],
            ['name'=>'cover_image_url','label'=>'Обложка','type'=>'image','folder'=>$this->routeBase,'required'=>true],
            ['name'=>'published_at','label'=>'Дата публикации','type'=>'datetime','required'=>true],
            ['name'=>'blocks','label'=>'Контент','type'=>'json'],
            ['name'=>'order_index','label'=>'Порядок','type'=>'number'],
        ];
    }

    protected function decodeBlocks($item): array
    {
        $raw = $item->blocks ?? [];
        if (is_string($raw)) { $raw = json_decode($raw, true) ?: []; }
        return is_array($raw) ? $raw : [];
    }

    public function create()
    {
        $model = $this->modelClass;
        $item = new $model();
        return view('admin.articles.form', [
            'mode' => 'create', 'item' => $item,
            'active' => $this->active, 'routeBase' => $this->routeBase,
            'titleSingular' => $this->titleSingular, 'titlePlural' => $this->titlePlural,
            'blocksData' => [],
        ]);
    }

    public function edit($id)
    {
        $item = ($this->modelClass)::findOrFail($id);
        return view('admin.articles.form', [
            'mode' => 'edit', 'item' => $item,
            'active' => $this->active, 'routeBase' => $this->routeBase,
            'titleSingular' => $this->titleSingular, 'titlePlural' => $this->titlePlural,
            'blocksData' => $this->decodeBlocks($item),
        ]);
    }
}
