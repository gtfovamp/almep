<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Category, Subcategory, ProductType, Blog, News,
    Certificate, Partner, Portfolio, Testimonial, Consultation};

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            ['key'=>'products','label'=>'Товары','count'=>Product::count(),'url'=>url('/admin/products'),'color'=>'indigo','icon'=>'M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0'],
            ['key'=>'categories','label'=>'Категории','count'=>Category::count(),'url'=>url('/admin/categories'),'color'=>'violet','icon'=>'M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z'],
            ['key'=>'subcategories','label'=>'Подкатегории','count'=>Subcategory::count(),'url'=>url('/admin/subcategories'),'color'=>'sky','icon'=>'M3 3h7v7H3zM14 3h7v7h-7z'],
            ['key'=>'portfolio','label'=>'Портфолио','count'=>Portfolio::count(),'url'=>url('/admin/portfolio'),'color'=>'fuchsia','icon'=>'M3 3h18v18H3zM3 9h18M9 21V9'],
            ['key'=>'partners','label'=>'Партнёры','count'=>Partner::count(),'url'=>url('/admin/partners'),'color'=>'blue','icon'=>'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 7a4 4 0 1 0 0 8 4 4 0 0 0 0-8z'],
            ['key'=>'news','label'=>'Новости','count'=>News::count(),'url'=>url('/admin/news'),'color'=>'emerald','icon'=>'M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16'],
            ['key'=>'blog','label'=>'Статьи блога','count'=>Blog::count(),'url'=>url('/admin/blog'),'color'=>'amber','icon'=>'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'],
            ['key'=>'certificates','label'=>'Сертификаты','count'=>Certificate::count(),'url'=>url('/admin/certificates'),'color'=>'rose','icon'=>'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'],
            ['key'=>'testimonials','label'=>'Отзывы','count'=>Testimonial::count(),'url'=>url('/admin/testimonials'),'color'=>'teal','icon'=>'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z'],
            ['key'=>'consultations','label'=>'Заявки','count'=>Consultation::count(),'url'=>url('/admin/consultations'),'color'=>'orange','icon'=>'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6'],
        ];

        $pendingReviews = Testimonial::where('approved', 0)->count();
        $recentConsult  = Consultation::orderByDesc('created_at')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'pendingReviews', 'recentConsult'));
    }
}
