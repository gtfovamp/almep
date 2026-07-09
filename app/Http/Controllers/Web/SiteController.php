<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Subcategory;
use App\Support\SiteI18n;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    protected function shared(string $lang): array
    {
        $t = SiteI18n::get($lang);

        $categories = Category::query()->orderBy('order_index')->get();
        $subcategories = Subcategory::query()->orderBy('order_index')->get();

        return [
            't' => $t,
            'lang' => $lang,
            'catalogData' => [
                'categories' => $categories,
                'subcategories' => $subcategories,
            ],
        ];
    }

    public function certificates(Request $request, string $lang)
    {
        $data = $this->shared($lang);

        $data['certificates'] = Certificate::query()
            ->orderBy('order_index')
            ->get();

        return view('pages.certificates', $data);
    }

    public function structure(string $lang)
    {
        return view('pages.structure', $this->shared($lang));
    }

    public function about(string $lang)
    {
        return view('pages.about', $this->shared($lang));
    }

    public function services(string $lang)
    {
        return view('pages.services', $this->shared($lang));
    }

    public function partners(string $lang)
    {
        $data = $this->shared($lang);
        $data['partners'] = \App\Models\Partner::query()
            ->orderBy('order_index')
            ->get();

        return view('pages.partners', $data);
    }

    public function portfolio(string $lang)
    {
        $data = $this->shared($lang);
        $data['portfolioItems'] = \App\Models\Portfolio::query()
            ->orderBy('order_index')
            ->paginate(12)
            ->withQueryString();

        return view('pages.portfolio', $data);
    }

    public function reviews(string $lang)
    {
        $data = $this->shared($lang);
        $data['reviews'] = \App\Models\Testimonial::query()
            ->where('approved', 1)
            ->orderByDesc('id')
            ->paginate(6)
            ->withQueryString();

        return view('pages.reviews', $data);
    }

    public function news(string $lang)
    {
        $data = $this->shared($lang);
        $data['news'] = \App\Models\News::query()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(7)
            ->withQueryString();

        return view('pages.news', $data);
    }

    public function contacts(string $lang)
    {
        return view('pages.contacts', $this->shared($lang));
    }
    public function index(string $lang)
    {
        return view('pages.index', $this->shared($lang));
    }
}
