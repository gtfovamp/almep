<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Product;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\News;
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
            ->paginate(6) // было 12 → при 7 проектах пагинация не появлялась; 6 = сетка 3×2
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
        $data['news'] = News::query()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(7)
            ->withQueryString();

        return view('pages.news', $data);
    }

    /**
     * Страница одной новости. {id} — числовой ID, см. whereNumber в routes/site.php.
     * Порядок сортировки "других новостей" — тот же, что и на странице списка
     * (published_at desc, затем id desc), чтобы блок "Другие новости" визуально
     * соответствовал тому, что человек увидел бы в самом списке.
     */
    public function newsShow(string $lang, int $id)
    {
        $data = $this->shared($lang);

        $item = News::query()->findOrFail($id);

        $related = News::query()
            ->where('id', '!=', $id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $data['item'] = $item;
        $data['related'] = $related;

        return view('pages.news-show', $data);
    }

    public function contacts(string $lang)
    {
        return view('pages.contacts', $this->shared($lang));
    }

    public function index(string $lang)
    {
        return view('pages.index', $this->shared($lang));
    }

    public function blog(string $lang)
    {
        $data = $this->shared($lang);
        $data['blog'] = Blog::query()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(7)
            ->withQueryString();

        return view('pages.blog', $data);
    }

    public function blogShow(string $lang, int $id)
    {
        $data = $this->shared($lang);

        $item = Blog::query()->findOrFail($id);

        $related = Blog::query()
            ->where('id', '!=', $id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $data['item'] = $item;
        $data['related'] = $related;

        return view('pages.blog-show', $data);
    }

    

    


    /**
     * Каталог: сетка категорий ($mode='categories') или подкатегорий ($mode='subcategories').
     * /{lang}/catalog            -> все категории
     * /{lang}/catalog/{category} -> подкатегории выбранной категории
     */
    public function catalog(string $lang, ?int $category = null)
    {
        $data = $this->shared($lang);

        if ($category) {
            $cat = Category::findOrFail($category);
            $items = Subcategory::where('category_id', $cat->id)
                ->withCount('products')
                ->orderBy('order_index')->orderBy('id')
                ->get();
            $data['mode']     = 'subcategories';
            $data['category'] = $cat;
            $data['items']    = $items;
        } else {
            $cats = Category::orderBy('order_index')->orderBy('id')->get();
            // Кол-во товаров в категории = сумма по её подкатегориям
            foreach ($cats as $c) {
                $c->all_products_count = Product::whereIn(
                    'subcategory_id',
                    Subcategory::where('category_id', $c->id)->pluck('id')
                )->count();
            }
            $data['mode']  = 'categories';
            $data['items'] = $cats;
        }

        return view('pages.catalog', $data);
    }

    public function products(\Illuminate\Http\Request $request, string $lang)
    {
        $data = $this->shared($lang);

        $subId  = $request->query('subcategory');
        $typeId = $request->query('type');
        $sort   = $request->query('sort', 'default');

        // Без подкатегории — показываем каталог (категории)
        if (!$subId) {
            return redirect('/'.$lang.'/catalog');
        }

        $subcategory = Subcategory::with('category')->find($subId);

        $query = Product::query()->with(['images', 'subcategory.category']);
        $query->where('subcategory_id', $subId);
        if ($typeId) $query->where('product_type_id', $typeId);

        if ($sort === 'name') $query->orderBy('name');
        else $query->orderBy('order_index')->orderBy('id');

        $data['subcategory'] = $subcategory;
        $data['products']    = $query->paginate(12)->withQueryString();

        return view('pages.products', $data);
    }

    public function product(string $lang, int $id)
    {
        $data = $this->shared($lang);

        $item = Product::with(['images', 'specifications', 'subcategory.category', 'productType'])
            ->findOrFail($id);

        $related = Product::query()->with('images')
            ->where('subcategory_id', $item->subcategory_id)
            ->where('id', '!=', $item->id)
            ->orderBy('order_index')->limit(4)->get();

        $data['item']    = $item;
        $data['related'] = $related;

        return view('pages.product', $data);
    }

}
