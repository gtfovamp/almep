@extends('admin.layout', ['active' => 'products'])
@section('title', ($mode === 'create' ? 'Новый товар' : 'Редактирование товара'))

@section('content')
<div class="mb-6">
    <a href="{{ url('/admin/products') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition hover:text-slate-800">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Назад к товарам
    </a>
</div>

@if($errors->any())
<div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><ul class="list-inside list-disc space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" enctype="multipart/form-data"
      x-data="productForm(@js($subsByCategory), {{ $item->category_id ?? 'null' }}, {{ $item->subcategory_id ?? 'null' }}, @js($imagesData), @js($specsData))"
      @submit="sync"
      action="{{ $mode === 'create' ? url('/admin/products') : url('/admin/products/' . $item->id) }}"
      class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <div class="space-y-6 lg:col-span-2">
        {{-- Basic info --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Основное</h2>
                <div class="inline-flex rounded-lg bg-slate-100 p-1 text-xs font-semibold">
                    @foreach(['ru'=>'RU','en'=>'EN','az'=>'AZ'] as $c=>$l)
                        <button type="button" @click="lang='{{ $c }}'" :class="lang==='{{ $c }}' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500'" class="rounded-md px-3 py-1 transition">{{ $l }}</button>
                    @endforeach
                </div>
            </div>

            <label class="mb-1.5 block text-xs font-medium text-slate-500">Артикул *</label>
            <input name="article" type="text" value="{{ old('article', $item->article) }}" class="mb-4 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">

            @foreach(['ru'=>'','en'=>'_en','az'=>'_az'] as $c=>$suf)
            <div x-show="lang==='{{ $c }}'" x-cloak>
                <label class="mb-1.5 block text-xs font-medium text-slate-500">Название ({{ strtoupper($c) }}) {!! $c==='ru'?'*':'' !!}</label>
                <input name="name{{ $suf }}" type="text" value="{{ old('name'.$suf, $item->{'name'.$suf}) }}" class="mb-4 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                <label class="mb-1.5 block text-xs font-medium text-slate-500">Описание ({{ strtoupper($c) }})</label>
                <textarea name="description{{ $suf }}" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">{{ old('description'.$suf, $item->{'description'.$suf}) }}</textarea>
            </div>
            @endforeach
        </div>

        {{-- IMAGE GALLERY --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="mb-1 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Галерея изображений</h2>
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500" x-text="images.length + ' фото'"></span>
            </div>
            <p class="mb-4 text-xs text-slate-400">Перетаскивайте для сортировки. Нажмите ★, чтобы сделать фото главным.</p>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <template x-for="(img, idx) in images" :key="img._id">
                    <div class="group relative aspect-square overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                         draggable="true" @dragstart="dragIdx=idx" @dragover.prevent @drop="moveImg(idx)" :class="dragIdx===idx?'opacity-40':''">
                        <img :src="img.url" class="h-full w-full object-cover">
                        <div class="absolute inset-x-0 top-0 flex items-center justify-between p-1.5">
                            <button type="button" @click="setPrimary(idx)" :class="img.is_primary ? 'bg-amber-400 text-white' : 'bg-white/90 text-slate-400'" class="rounded-md p-1 shadow transition hover:scale-110" title="Главное фото">
                                <svg class="h-3.5 w-3.5" :fill="img.is_primary?'currentColor':'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </button>
                            <button type="button" @click="removeImg(idx)" class="rounded-md bg-white/90 p-1 text-slate-400 shadow transition hover:scale-110 hover:text-red-600" title="Удалить">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div x-show="img.is_primary" class="absolute bottom-0 inset-x-0 bg-amber-400 py-0.5 text-center text-[10px] font-bold text-white">ГЛАВНОЕ</div>
                    </div>
                </template>

                {{-- uploader tile --}}
                <label class="flex aspect-square cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-300 text-slate-400 transition hover:border-brand-400 hover:bg-brand-50 hover:text-brand-500">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    <span class="text-xs font-medium">Добавить фото</span>
                    <input type="file" accept="image/*" multiple class="hidden" @change="uploadImages($event)">
                </label>
            </div>
            <p class="mt-3 text-xs text-brand-500" x-show="uploading" x-cloak>Загрузка изображений…</p>
            <input type="hidden" name="images_json" x-ref="imagesInput">
        </div>

        {{-- SPECIFICATIONS --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="mb-1 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Характеристики</h2>
                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500" x-text="specs.length + ' шт.'"></span>
            </div>
            <p class="mb-4 text-xs text-slate-400">Например: «Материал» → «Нержавеющая сталь». Языки переключаются вверху страницы.</p>

            <div class="space-y-2">
                <div class="hidden grid-cols-12 gap-2 px-1 text-xs font-medium text-slate-400 sm:grid">
                    <div class="col-span-5">Параметр</div><div class="col-span-6">Значение</div><div class="col-span-1"></div>
                </div>
                <template x-for="(sp, idx) in specs" :key="sp._id">
                    <div class="grid grid-cols-12 items-start gap-2">
                        <template x-for="lc in ['ru','en','az']" :key="'k'+lc">
                            <input x-show="lang===lc" type="text" x-model="sp['key_'+lc]" :placeholder="'Параметр (' + lc.toUpperCase() + ')'" class="col-span-5 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                        </template>
                        <template x-for="lc in ['ru','en','az']" :key="'v'+lc">
                            <input x-show="lang===lc" type="text" x-model="sp['value_'+lc]" :placeholder="'Значение (' + lc.toUpperCase() + ')'" class="col-span-6 rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                        </template>
                        <button type="button" @click="removeSpec(idx)" class="col-span-1 flex h-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </div>
                </template>
            </div>
            <button type="button" @click="addSpec()" class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg> Добавить характеристику
            </button>
            <input type="hidden" name="specs_json" x-ref="specsInput">
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="space-y-6">
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-bold text-slate-900">Категория</h2>
            <label class="mb-1.5 block text-xs font-medium text-slate-500">Категория</label>
            <select x-model="categoryId" @change="onCategoryChange" name="category_id" class="mb-4 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                <option value="">— не выбрана —</option>
                @foreach($categories as $cid => $cname)
                    <option value="{{ $cid }}">{{ $cname }}</option>
                @endforeach
            </select>
            <label class="mb-1.5 block text-xs font-medium text-slate-500">Подкатегория</label>
            <select x-model="subcategoryId" name="subcategory_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                <option value="">— не выбрана —</option>
                <template x-for="s in availableSubs" :key="s.id"><option :value="s.id" x-text="s.name"></option></template>
            </select>
            <p x-show="categoryId && availableSubs.length===0" class="mt-1.5 text-xs text-amber-600" x-cloak>В этой категории пока нет подкатегорий.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="mb-4 text-sm font-bold text-slate-900">Параметры</h2>
            <label class="flex cursor-pointer items-center gap-3">
                <input type="checkbox" name="in_stock" value="1" {{ old('in_stock', $item->in_stock) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-slate-700">В наличии</span>
            </label>
            <label class="mb-1.5 mt-4 block text-xs font-medium text-slate-500">Порядок сортировки</label>
            <input name="order_index" type="number" value="{{ old('order_index', $item->order_index) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
        </div>

        <div class="flex flex-col gap-2">
            <button type="submit" class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">{{ $mode === 'create' ? 'Создать товар' : 'Сохранить изменения' }}</button>
            <a href="{{ url('/admin/products') }}" class="rounded-lg border border-slate-300 px-6 py-2.5 text-center text-sm font-medium text-slate-600 transition hover:bg-slate-50">Отмена</a>
        </div>
    </div>
</form>

<script>
function productForm(subsByCategory, initCat, initSub, initImages, initSpecs) {
    return {
        lang: 'ru',
        categoryId: initCat ? String(initCat) : '',
        subcategoryId: initSub ? String(initSub) : '',
        subsByCategory: subsByCategory || {},
        images: (initImages||[]).map(x => ({ _id: crypto.randomUUID(), url:x.url||x.image_url||'', is_primary: !!x.is_primary, id: x.id||null })),
        specs: (initSpecs||[]).map(x => ({ _id: crypto.randomUUID(), key_ru:x.key||'', key_en:x.key_en||'', key_az:x.key_az||'', value_ru:x.value||'', value_en:x.value_en||'', value_az:x.value_az||'' })),
        dragIdx: null, uploading: false,
        get availableSubs(){ return this.subsByCategory[this.categoryId] || []; },
        onCategoryChange(){ if(!this.availableSubs.some(s=>String(s.id)===String(this.subcategoryId))) this.subcategoryId=''; },
        // gallery
        setPrimary(i){ this.images.forEach((im,j)=>im.is_primary = j===i); },
        removeImg(i){ const wasP=this.images[i].is_primary; this.images.splice(i,1); if(wasP&&this.images.length) this.images[0].is_primary=true; },
        moveImg(t){ if(this.dragIdx===null||this.dragIdx===t) return; const [m]=this.images.splice(this.dragIdx,1); this.images.splice(t,0,m); this.dragIdx=null; },
        async uploadImages(e){
            const fls = Array.from(e.target.files||[]); if(!fls.length) return;
            this.uploading = true;
            for(const file of fls){
                const tmp = { _id: crypto.randomUUID(), url: URL.createObjectURL(file), is_primary: this.images.length===0, id:null };
                this.images.push(tmp);
                try {
                    const fd=new FormData(); fd.append('file',file); fd.append('folder','products');
                    const res=await fetch('{{ url('/admin/upload-image') }}',{ method:'POST', headers:{ 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }, body:fd });
                    const data=await res.json(); if(data.url) tmp.url=data.url;
                } catch(err){ console.error(err); }
            }
            this.uploading=false; e.target.value='';
        },
        // specs
        addSpec(){ this.specs.push({ _id:crypto.randomUUID(), key_ru:'',key_en:'',key_az:'',value_ru:'',value_en:'',value_az:'' }); },
        removeSpec(i){ this.specs.splice(i,1); },
        sync(){
            this.$refs.imagesInput.value = JSON.stringify(this.images.map((im,idx)=>({ url:im.url, is_primary:im.is_primary?1:0, order_index:idx })));
            this.$refs.specsInput.value = JSON.stringify(this.specs.map((sp,idx)=>({ key:sp.key_ru, key_en:sp.key_en, key_az:sp.key_az, value:sp.value_ru, value_en:sp.value_en, value_az:sp.value_az, order_index:idx })));
        },
    };
}
</script>
@endsection
