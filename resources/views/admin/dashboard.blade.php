@extends('admin.layout', ['active' => 'dashboard'])
@section('title', 'Дашборд')

@section('content')
    {{-- Pending reviews alert --}}
    @if($pendingReviews > 0)
        <a href="{{ url('/admin/testimonials') }}" class="mb-6 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800 transition hover:bg-amber-100">
            <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0zM12 9v4M12 17h.01"/></svg>
            <span><strong>{{ $pendingReviews }}</strong> {{ $pendingReviews == 1 ? 'отзыв ожидает' : 'отзывов ожидают' }} модерации — нажмите, чтобы просмотреть.</span>
        </a>
    @endif

    {{-- Stats grid --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        @foreach($stats as $s)
            <a href="{{ $s['url'] }}"
               class="group rounded-xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-{{ $s['color'] }}-300 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-{{ $s['color'] }}-100 text-{{ $s['color'] }}-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $s['icon'] }}"/></svg>
                    </div>
                    <svg class="h-5 w-5 text-slate-300 transition group-hover:text-{{ $s['color'] }}-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
                <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $s['count'] }}</p>
                <p class="mt-1 text-sm font-medium text-slate-500">{{ $s['label'] }}</p>
            </a>
        @endforeach
    </div>

    {{-- Quick actions + recent consultations --}}
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-6 lg:col-span-2">
            <h2 class="mb-4 text-base font-bold text-slate-900">Быстрые действия</h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach([
                    ['Добавить товар', url('/admin/products/create')],
                    ['Добавить портфолио', url('/admin/portfolio/create')],
                    ['Добавить партнёра', url('/admin/partners/create')],
                    ['Добавить новость', url('/admin/news/create')],
                    ['Добавить статью', url('/admin/blog/create')],
                    ['Добавить сертификат', url('/admin/certificates/create')],
                ] as [$label, $href])
                    <a href="{{ $href }}" class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="mb-4 text-base font-bold text-slate-900">Последние заявки</h2>
            @forelse($recentConsult as $c)
                <div class="flex items-start gap-3 border-b border-slate-100 py-3 last:border-0">
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 text-xs font-semibold text-orange-600">{{ mb_strtoupper(mb_substr($c->name,0,1)) }}</div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $c->name }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $c->phone }}</p>
                        <p class="text-xs text-slate-400">{{ optional($c->created_at)->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-slate-400">Пока нет заявок</p>
            @endforelse
            <a href="{{ url('/admin/consultations') }}" class="mt-4 block text-center text-sm font-medium text-brand-600 hover:text-brand-700">Все заявки →</a>
        </div>
    </div>
@endsection
