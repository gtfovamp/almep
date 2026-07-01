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
}
