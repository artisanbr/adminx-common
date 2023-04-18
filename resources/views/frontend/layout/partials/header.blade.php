<?php
/**
 * @var \Adminx\Common\Models\Site $site
 * @var \Adminx\Common\Models\Page $page
 */
?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{--Tags--}}
    @{!! Meta::toHtml() !!}
    <!-- Assets -->
    {!! Meta::toHtml() !!}
    {{--Site CSS--}}
    {!! $site->theme->css_html ?? '' !!}
    {{--Page CSS--}}
    @{!! $page->css_html ?? '' !!}
    {{--@@yield('css-includes')
    @@stack('css-includes')
    @@stack('css')
    @@yield('css')--}}

    <link rel="preload" as="image" href="{{ appAsset(config('adminx.app.provider.logo')) }}" />

    {!! $site->theme->js->head_js_html ?? '' !!}
    @{!! $page->js->head_js_html ?? '' !!}
    {{--@@stack('head-js')
    @@yield('head-js')--}}

</head>
<body id="page-@{{ $page->is_home ? 'home' : $page->slug }}" class="page-@{{ $page->is_home ?? false ? 'home' : $page->slug }} page-@{{ $page->public_id }}">
{!! $site->theme->js->after_body_js_html ?? '' !!}
@{!! $page->js->after_body_js_html ?? '' !!}
{{--@@stack('body-js')
@@yield('body-js')--}}

{{--Main--}}
{{--<div id="root">--}}
{{--Header--}}
{!! $site->theme->header_twig_html ?? '' !!}
