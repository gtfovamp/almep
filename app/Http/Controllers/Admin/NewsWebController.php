<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;

class NewsWebController extends ArticleCrudController
{
    protected string $modelClass = News::class;
    protected string $routeBase = 'news';
    protected string $active = 'news';
    protected string $titleSingular = 'Новость';
    protected string $titlePlural = 'Новости';
}
