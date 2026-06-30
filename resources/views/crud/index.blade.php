@extends('admin.layout', ['active' => $active])
@section('title', $titlePlural)

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-slate-500">Всего записей: <span class="font-semibold text-slate-700">{{ $items->count() }}</span></p>
        @if($routeBase !== 'consultations')
            <a href="{{ url('/admin/' . $routeBase . '/create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                Добавить
            </a>
        @endif
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-semibold">ID</th>
                        @foreach($columns as $field => $col)
                            <th class="px-5 py-3 font-semibold">{{ is_array($col) ? $col['label'] : $col }}</th>
                        @endforeach
                        <th class="px-5 py-3 text-right font-semibold">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-3 text-slate-400">#{{ $item->id }}</td>
                            @foreach($columns as $field => $col)
                                @php $type = is_array($col) ? ($col['type'] ?? 'text') : 'text'; $val = data_get($item, $field); @endphp
                                <td class="px-5 py-3">
                                    @if($type === 'image')
                                        @if($val)
                                            <img src="{{ $val }}" alt="" class="h-10 w-10 rounded-lg object-cover ring-1 ring-slate-200">
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    @elseif($type === 'bool')
                                        @if($val)
                                            <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Да</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500">Нет</span>
                                        @endif
                                    @elseif($type === 'date')
                                        <span class="text-slate-600">{{ $val ? \Illuminate\Support\Carbon::parse($val)->format('d.m.Y H:i') : '—' }}</span>
                                    @else
                                        <span class="text-slate-700">{{ \Illuminate\Support\Str::limit(strip_tags((string) $val), 60) ?: '—' }}</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @if($routeBase !== 'consultations')
                                        <a href="{{ url('/admin/' . $routeBase . '/' . $item->id . '/edit') }}"
                                           class="rounded-lg p-2 text-slate-400 transition hover:bg-brand-50 hover:text-brand-600" title="Редактировать">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ url('/admin/' . $routeBase . '/' . $item->id) }}" onsubmit="return confirm('Удалить запись?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600" title="Удалить">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($columns) + 2 }}" class="px-5 py-16 text-center text-slate-400">Записей пока нет</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
