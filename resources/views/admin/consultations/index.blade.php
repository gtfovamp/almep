@extends('admin.layout', ['active' => 'consultations'])
@section('title', 'Заявки')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-slate-900">Заявки на консультацию</h1>
        <p class="mt-1 text-sm text-slate-500">Всего: {{ $items->total() }}</p>
    </div>
</div>

@if($items->isEmpty())
    <div class="rounded-xl border-2 border-dashed border-slate-200 py-16 text-center text-sm text-slate-400">Заявок пока нет.</div>
@else
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
    @foreach($items as $c)
    <div class="rounded-xl border border-slate-200 bg-white p-5 transition hover:shadow-md">
        <div class="mb-3 flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">
                    {{ mb_strtoupper(mb_substr($c->name ?: '?', 0, 1)) }}
                </div>
                <div>
                    <div class="font-semibold text-slate-900">{{ $c->name ?: '—' }}</div>
                    <div class="text-xs text-slate-400">{{ $c->created_at ? \Illuminate\Support\Carbon::parse($c->created_at)->format('d.m.Y H:i') : '' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ url('/admin/consultations/' . $c->id) }}" onsubmit="return confirm('Удалить заявку?')">
                @csrf @method('DELETE')
                <button class="rounded-lg p-1.5 text-slate-300 transition hover:bg-red-50 hover:text-red-600" title="Удалить">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
            </form>
        </div>
        <div class="space-y-2 text-sm">
            @if($c->email)
            <a href="mailto:{{ $c->email }}" class="flex items-center gap-2 text-slate-600 transition hover:text-brand-600">
                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg>
                {{ $c->email }}
            </a>
            @endif
            @if($c->phone)
            <a href="tel:{{ $c->phone }}" class="flex items-center gap-2 text-slate-600 transition hover:text-brand-600">
                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                {{ $c->phone }}
            </a>
            @endif
        </div>
        @if($c->message)
        <div class="mt-3 rounded-lg bg-slate-50 p-3 text-sm leading-relaxed text-slate-600">{{ $c->message }}</div>
        @endif
    </div>
    @endforeach
</div>
<div class="mt-6">{{ $items->links() }}</div>
@endif
@endsection
