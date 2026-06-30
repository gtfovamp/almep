@extends('admin.layout', ['active' => $active])
@section('title', ($mode === 'create' ? 'Новая запись' : 'Редактирование') . ' — ' . $titleSingular)

@section('content')
<div class="mb-6">
    <a href="{{ url('/admin/' . $routeBase) }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-800">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Назад к списку
    </a>
</div>

<form method="POST" enctype="multipart/form-data"
      action="{{ $mode === 'create' ? url('/admin/' . $routeBase) : url('/admin/' . $routeBase . '/' . $item->id) }}"
      class="max-w-3xl space-y-6">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <div class="rounded-xl border border-slate-200 bg-white p-6 sm:p-8 space-y-6">
        @foreach($fields as $f)
            @php
                $type = $f['type'] ?? 'text';
                $name = $f['name'];
                $label = $f['label'] ?? $name;
                $required = $f['required'] ?? false;
            @endphp

            {{-- Translatable group (ru / en / az) --}}
            @if($type === 'trans')
                <div x-data="{ tab: 'ru' }">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
                    <div class="mb-3 inline-flex rounded-lg bg-slate-100 p-1 text-xs font-medium">
                        @foreach(['ru'=>'RU','en'=>'EN','az'=>'AZ'] as $code => $lbl)
                            <button type="button" @click="tab='{{ $code }}'"
                                    :class="tab==='{{ $code }}' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500'"
                                    class="rounded-md px-4 py-1.5 transition">{{ $lbl }}</button>
                        @endforeach
                    </div>
                    @foreach(['ru'=>'','en'=>'_en','az'=>'_az'] as $code => $suf)
                        @php $fname = $name . $suf; $input = $f['input'] ?? 'text'; @endphp
                        <div x-show="tab==='{{ $code }}'" x-cloak>
                            @if($input === 'textarea')
                                <textarea name="{{ $fname }}" rows="4"
                                          class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">{{ old($fname, $item->$fname) }}</textarea>
                            @else
                                <input name="{{ $fname }}" type="text" value="{{ old($fname, $item->$fname) }}"
                                       class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                            @endif
                        </div>
                    @endforeach
                </div>

            {{-- Image upload (file or url) --}}
            @elseif($type === 'image')
                <div x-data="{ preview: '{{ old($name, $item->$name) }}' }">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
                    <div class="flex items-start gap-4">
                        <div class="flex h-24 w-24 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border border-dashed border-slate-300 bg-slate-50">
                            <template x-if="preview"><img :src="preview" class="h-full w-full object-cover"></template>
                            <template x-if="!preview">
                                <svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15l-5-5L5 21M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><circle cx="8.5" cy="8.5" r="1.5"/></svg>
                            </template>
                        </div>
                        <div class="flex-1 space-y-2">
                            <input type="file" name="{{ $name }}_file" accept="image/*"
                                   @change="const f=$event.target.files[0]; if(f) preview=URL.createObjectURL(f)"
                                   class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100">
                            <p class="text-xs text-slate-400">или вставьте ссылку:</p>
                            <input name="{{ $name }}" type="text" value="{{ old($name, $item->$name) }}" placeholder="https://..."
                                   @input="preview=$event.target.value"
                                   class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                        </div>
                    </div>
                </div>

            {{-- Select --}}
            @elseif($type === 'select')
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
                    <select name="{{ $name }}"
                            class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                        <option value="">— не выбрано —</option>
                        @foreach(($f['options'] ?? []) as $val => $lbl)
                            <option value="{{ $val }}" @selected((string) old($name, $item->$name) === (string) $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

            {{-- Checkbox --}}
            @elseif($type === 'checkbox')
                <label class="flex cursor-pointer items-center gap-3">
                    <input type="hidden" name="{{ $name }}" value="0">
                    <input type="checkbox" name="{{ $name }}" value="1" @checked(old($name, $item->$name))
                           class="h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                    <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
                </label>

            {{-- Number --}}
            @elseif($type === 'number')
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">{{ $label }}</label>
                    <input name="{{ $name }}" type="number" value="{{ old($name, $item->$name) }}"
                           class="w-full max-w-xs rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                    @if(!empty($f['hint']))<p class="mt-1 text-xs text-slate-400">{{ $f['hint'] }}</p>@endif
                </div>

            {{-- Datetime --}}
            @elseif($type === 'datetime')
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
                    @php $dt = old($name, $item->$name); $dtVal = $dt ? \Illuminate\Support\Carbon::parse($dt)->format('Y-m-d\TH:i') : ''; @endphp
                    <input name="{{ $name }}" type="datetime-local" value="{{ $dtVal }}"
                           class="w-full max-w-xs rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                </div>

            {{-- JSON (blocks) --}}
            @elseif($type === 'json')
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">{{ $label }}</label>
                    @php $jv = old($name, is_array($item->$name) ? json_encode($item->$name, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : $item->$name); @endphp
                    <textarea name="{{ $name }}" rows="10"
                              class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 font-mono text-xs outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">{{ $jv }}</textarea>
                    <p class="mt-1 text-xs text-slate-400">Формат JSON (блоки контента).</p>
                </div>

            {{-- Plain text / textarea --}}
            @else
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
                    @if($type === 'textarea')
                        <textarea name="{{ $name }}" rows="4"
                                  class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">{{ old($name, $item->$name) }}</textarea>
                    @else
                        <input name="{{ $name }}" type="text" value="{{ old($name, $item->$name) }}"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">
            {{ $mode === 'create' ? 'Создать' : 'Сохранить' }}
        </button>
        <a href="{{ url('/admin/' . $routeBase) }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Отмена</a>
    </div>
</form>
@endsection
