@extends('admin.layout', ['active' => $active])
@section('title', ($mode === 'create' ? 'Новая запись' : 'Редактирование') . ' — ' . $titleSingular)

@section('content')
<div class="mb-6">
    <a href="{{ url('/admin/' . $routeBase) }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-800">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Назад к списку
    </a>
</div>

@if($errors->any())
<div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
    <ul class="list-inside list-disc space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" enctype="multipart/form-data"
      x-data="articleEditor(@js($blocksData))"
      @submit="syncBlocks"
      action="{{ $mode === 'create' ? url('/admin/' . $routeBase) : url('/admin/' . $routeBase . '/' . $item->id) }}"
      class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <div class="space-y-6 lg:col-span-2">
        {{-- Title + global language switch --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Заголовок статьи</h2>
                <div class="inline-flex rounded-lg bg-slate-100 p-1 text-xs font-semibold">
                    @foreach(['ru'=>'RU','en'=>'EN','az'=>'AZ'] as $c => $l)
                        <button type="button" @click="lang='{{ $c }}'" :class="lang==='{{ $c }}' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="rounded-md px-3 py-1 transition">{{ $l }}</button>
                    @endforeach
                </div>
            </div>
            @foreach(['ru'=>'','en'=>'_en','az'=>'_az'] as $c => $suf)
                <div x-show="lang==='{{ $c }}'" x-cloak>
                    <input name="title{{ $suf }}" type="text" value="{{ old('title'.$suf, $item->{'title'.$suf}) }}"
                           placeholder="Введите заголовок ({{ strtoupper($c) }})"
                           class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-lg font-semibold outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                </div>
            @endforeach
            <p class="mt-2 text-xs text-slate-400">Переключатель языка вверху действует на весь редактор — и на заголовок, и на текст блоков.</p>
        </div>

        {{-- BLOCK CONSTRUCTOR --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="mb-2 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Конструктор контента</h2>
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500" x-text="blocks.length + ' блок.'"></span>
            </div>
            <p class="mb-4 flex items-start gap-2 rounded-lg bg-blue-50 px-3 py-2 text-xs leading-relaxed text-blue-700">
                <svg class="mt-0.5 h-4 w-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                Собирайте статью из блоков. Текст переводится по языкам, изображения общие. Перетаскивайте блоки за <b>⠿</b> или используйте стрелки.
            </p>

            <div class="space-y-3">
                <template x-for="(block, idx) in blocks" :key="block._id">
                    <div class="group relative rounded-xl border border-slate-200 bg-slate-50/60 p-4 transition hover:border-slate-300"
                         draggable="true" @dragstart="dragIdx=idx" @dragover.prevent @drop="moveTo(idx)" :class="dragIdx===idx ? 'opacity-40' : ''">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 rounded-md bg-white px-2 py-1 text-xs font-semibold text-slate-500 ring-1 ring-slate-200">
                                <span class="cursor-grab select-none text-slate-400" title="Перетащить">⠿</span>
                                <span x-text="blockLabel(block.type)"></span>
                            </span>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="move(idx,-1)" :disabled="idx===0" class="rounded p-1 text-slate-400 transition hover:bg-white hover:text-slate-700 disabled:opacity-30"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 15l-6-6-6 6"/></svg></button>
                                <button type="button" @click="move(idx,1)" :disabled="idx===blocks.length-1" class="rounded p-1 text-slate-400 transition hover:bg-white hover:text-slate-700 disabled:opacity-30"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
                                <button type="button" @click="remove(idx)" class="rounded p-1 text-slate-400 transition hover:bg-red-50 hover:text-red-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                            </div>
                        </div>

                        <template x-if="block.type==='heading'">
                            <div><template x-for="lc in ['ru','en','az']" :key="lc">
                                <input x-show="lang===lc" type="text" x-model="block[lc]" :placeholder="'Заголовок раздела (' + lc.toUpperCase() + ')'" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-lg font-bold outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                            </template></div>
                        </template>
                        <template x-if="block.type==='text'">
                            <div><template x-for="lc in ['ru','en','az']" :key="lc">
                                <textarea x-show="lang===lc" x-model="block[lc]" rows="4" :placeholder="'Текст абзаца (' + lc.toUpperCase() + ')'" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm leading-relaxed outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"></textarea>
                            </template></div>
                        </template>
                        <template x-if="block.type==='quote'">
                            <div><template x-for="lc in ['ru','en','az']" :key="lc">
                                <textarea x-show="lang===lc" x-model="block[lc]" rows="2" :placeholder="'Цитата (' + lc.toUpperCase() + ')'" class="w-full rounded-lg border-l-4 border-brand-400 bg-brand-50/50 px-3 py-2 text-sm italic outline-none focus:ring-2 focus:ring-brand-500/20"></textarea>
                            </template></div>
                        </template>
                        <template x-if="block.type==='list'">
                            <div><template x-for="lc in ['ru','en','az']" :key="lc">
                                <textarea x-show="lang===lc" x-model="block[lc]" rows="4" :placeholder="'По одному пункту на строку (' + lc.toUpperCase() + ')'" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"></textarea>
                            </template></div>
                        </template>
                        <template x-if="block.type==='image'">
                            <div class="flex items-start gap-4">
                                <div class="flex h-28 w-40 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border border-dashed border-slate-300 bg-white">
                                    <template x-if="block.url"><img :src="block.url" class="h-full w-full object-cover"></template>
                                    <template x-if="!block.url"><svg class="h-8 w-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15l-5-5L5 21"/><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/></svg></template>
                                </div>
                                <div class="flex-1 space-y-2">
                                    <input type="file" accept="image/*" @change="uploadBlockImage($event, idx)" class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100">
                                    <input type="text" x-model="block.url" placeholder="или ссылка https://..." class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                                    <template x-for="lc in ['ru','en','az']" :key="lc">
                                        <input x-show="lang===lc" type="text" x-model="block['caption_'+lc]" :placeholder="'Подпись (' + lc.toUpperCase() + ')'" class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-xs outline-none focus:border-brand-500">
                                    </template>
                                    <p class="text-xs text-brand-500" x-show="uploadingIdx===idx">Загрузка…</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <div x-show="blocks.length===0" class="rounded-xl border-2 border-dashed border-slate-200 py-10 text-center text-sm text-slate-400">Пока нет блоков. Добавьте первый 👇</div>

            <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                <template x-for="t in blockTypes" :key="t.type">
                    <button type="button" @click="add(t.type)" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg><span x-text="t.label"></span>
                    </button>
                </template>
            </div>
            <input type="hidden" name="blocks" x-ref="blocksInput">
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="space-y-6">
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-bold text-slate-900">Публикация</h2>
            <label class="mb-1.5 block text-xs font-medium text-slate-500">Дата и время</label>
            @php $dt = old('published_at', $item->published_at); $dtv = $dt ? \Illuminate\Support\Carbon::parse($dt)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'); @endphp
            <input name="published_at" type="datetime-local" value="{{ $dtv }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            <label class="mb-1.5 mt-4 block text-xs font-medium text-slate-500">Порядок сортировки</label>
            <input name="order_index" type="number" value="{{ old('order_index', $item->order_index) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6" x-data="{ cover: '{{ old('cover_image_url', $item->cover_image_url) }}' }">
            <h2 class="mb-4 text-sm font-bold text-slate-900">Обложка</h2>
            <div class="mb-3 flex aspect-video items-center justify-center overflow-hidden rounded-lg border border-dashed border-slate-300 bg-slate-50">
                <template x-if="cover"><img :src="cover" class="h-full w-full object-cover"></template>
                <template x-if="!cover"><svg class="h-10 w-10 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15l-5-5L5 21"/><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/></svg></template>
            </div>
            <input type="file" name="cover_image_url_file" accept="image/*" @change="const f=$event.target.files[0]; if(f) cover=URL.createObjectURL(f)" class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-brand-600 hover:file:bg-brand-100">
            <input type="text" name="cover_image_url" value="{{ old('cover_image_url', $item->cover_image_url) }}" placeholder="или ссылка https://..." @input="cover=$event.target.value" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            <p class="mt-2 text-xs text-slate-400">Рекомендуется 1200×630px</p>
        </div>
        <div class="flex flex-col gap-2">
            <button type="submit" class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">{{ $mode === 'create' ? 'Опубликовать' : 'Сохранить изменения' }}</button>
            <a href="{{ url('/admin/' . $routeBase) }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-center text-sm font-medium text-slate-600 transition hover:bg-slate-50">Отмена</a>
        </div>
    </div>
</form>

<script>
function articleEditor(initial) {
    const L = ['ru','en','az'];
    const pick = (o, keys) => { for (const k of keys) if (o && o[k] != null) return o[k]; return ''; };
    function normalize(raw) {
        // tolerant loader: supports {type, content:{ru,en,az}} | {type, text:{..}} | {type, ru,en,az} | image variants
        return (raw || []).map(b => {
            const t = b.type || (b.url || b.src || b.image_url ? 'image' : 'text');
            const o = { _id: crypto.randomUUID(), type: t };
            if (t === 'image') {
                o.url = pick(b, ['url','src','image_url']);
                const cap = b.caption || {};
                L.forEach(l => o['caption_'+l] = (typeof cap === 'object' ? (cap[l]||'') : '') || b['caption_'+l] || '');
            } else {
                const c = b.content || b.text || b;
                L.forEach(l => o[l] = (c && typeof c === 'object' ? (c[l] || '') : (l==='ru' && typeof c==='string' ? c : '')) || b[l] || '');
            }
            return o;
        });
    }
    return {
        lang: 'ru',
        blocks: normalize(initial),
        dragIdx: null, uploadingIdx: null,
        blockTypes: [
            { type: 'heading', label: 'Заголовок' },
            { type: 'text',    label: 'Абзац' },
            { type: 'image',   label: 'Изображение' },
            { type: 'list',    label: 'Список' },
            { type: 'quote',   label: 'Цитата' },
        ],
        blockLabel(t){ return (this.blockTypes.find(x=>x.type===t)||{}).label || t; },
        add(type){
            const b = { _id: crypto.randomUUID(), type };
            if (type==='image'){ b.url=''; L.forEach(l=>b['caption_'+l]=''); }
            else L.forEach(l=>b[l]='');
            this.blocks.push(b);
        },
        remove(i){ this.blocks.splice(i,1); },
        move(i,d){ const j=i+d; if(j<0||j>=this.blocks.length) return; [this.blocks[i],this.blocks[j]]=[this.blocks[j],this.blocks[i]]; },
        moveTo(t){ if(this.dragIdx===null||this.dragIdx===t) return; const [m]=this.blocks.splice(this.dragIdx,1); this.blocks.splice(t,0,m); this.dragIdx=null; },
        async uploadBlockImage(e, idx){
            const file = e.target.files[0]; if(!file) return;
            this.uploadingIdx = idx; this.blocks[idx].url = URL.createObjectURL(file);
            try {
                const fd = new FormData(); fd.append('file', file); fd.append('folder', '{{ $routeBase }}');
                const res = await fetch('{{ url('/admin/upload-image') }}', { method:'POST', headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body: fd });
                const data = await res.json(); if(data.url) this.blocks[idx].url = data.url;
            } catch(err){ console.error(err); }
            this.uploadingIdx = null;
        },
        syncBlocks(){
            // canonical output: text-like -> {type, content:{ru,en,az}}; image -> {type:'image', url, caption:{ru,en,az}}
            const out = this.blocks.map(b => {
                if (b.type==='image') return { type:'image', url:b.url, caption:{ ru:b.caption_ru||'', en:b.caption_en||'', az:b.caption_az||'' } };
                return { type:b.type, content:{ ru:b.ru||'', en:b.en||'', az:b.az||'' } };
            });
            this.$refs.blocksInput.value = JSON.stringify(out);
        },
    };
}
</script>
@endsection
