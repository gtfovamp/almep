<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;

class BlogWebController extends ArticleCrudController
{
    protected string $modelClass = Blog::class;
    protected string $routeBase = 'blog';
    protected string $active = 'blog';
    protected string $titleSingular = 'Статья';
    protected string $titlePlural = 'Блог';
}
