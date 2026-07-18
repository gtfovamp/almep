@extends('layouts.app')

@php $title = 'Almep Trading'; @endphp

@section('content')
<main class="site-main hdr--home">
    <div style="position: relative;">
        @include('partials.header', ['t' => $t, 'lang' => $lang])
    </div>
    @includeIf('partials.hero', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.about', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.portfolio', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.services', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.missions', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.products', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.whyus', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.partners', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.news', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.blog', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.reviews', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.consultation', ['t'=>$t,'lang'=>$lang])
    @includeIf('partials.address', ['t'=>$t,'lang'=>$lang])
    @include('partials.footer', ['t' => $t, 'lang' => $lang])
</main>
@endsection

@push('styles')
<style>    /* ── Слой адаптивной безопасности (общий для всех страниц) ── */
    .site-main { display: flex; flex-direction: column; min-height: 100vh; overflow-x: clip; }
    .site-main > section { flex: 0 0 auto; }
    .site-main > section:first-of-type { flex: 1 0 auto; }
    .site-main img, .site-main iframe, .site-main video { max-width: 100%; }
    .site-main *, .site-main *::before, .site-main *::after { box-sizing: border-box; }</style>
@endpush