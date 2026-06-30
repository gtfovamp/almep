<?php

namespace App\Http\Controllers\Admin;

use App\Models\Certificate;

class CertificateWebController extends AdminCrudController
{
    protected string $modelClass = Certificate::class;
    protected string $routeBase = 'certificates';
    protected string $active = 'certificates';
    protected string $titleSingular = 'Сертификат';
    protected string $titlePlural = 'Сертификаты';

    protected function columns(): array
    {
        return [
            'image_url' => ['label' => 'Изображение', 'type' => 'image'],
            'title'     => 'Название',
            'order_index' => 'Порядок',
        ];
    }
    protected function fields(): array
    {
        return [
            ['name'=>'title','label'=>'Название','type'=>'trans','input'=>'text','required'=>true],
            ['name'=>'image_url','label'=>'Изображение','type'=>'image','folder'=>'certificates','required'=>true],
            ['name'=>'order_index','label'=>'Порядок','type'=>'number'],
        ];
    }
}
