<?php
/**
 * @var \Adminx\Common\Models\Sites\Site|null $site
 * @var \Adminx\Common\Models\Pages\Page|null $page
 */

if (!isset($site)) {
    $site = FrontendSite::current();
}

if(!isset($page)){
    $page = FrontendPage::current();
}

$includeData = [
    'site'        => $site,
    'page' => $page
];

?>
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{--Tags--}}
    {!! Meta::toHtml() !!}
    {{--Custom CSS--}}
    {!! $site->theme->css_html ?? '' !!}
    {!! $page->css_html ?? '' !!}
    @yield('css-includes')
    @stack('css-includes')
    @stack('styles')
    @yield('styles')
    @stack('css')
    @yield('css')

    {!! $site->theme->js->head_js_html ?? '' !!}
    {!! $page->assets->js->head_html ?? '' !!}
    {!! $page->assets->head_script->html ?? '' !!}
</head>
<body id="page-{{ $page->is_home ? 'home' : $page->slug }}"
      class="page-{{ $page->is_home ?? false ? 'home' : $page->slug }} page-{{ $page->public_id }}">
{!! $site->theme->js->after_body_js_html ?? '' !!}
{!! $page->assets->js->before_body_html ?? '' !!}
@stack('body-js')
@yield('body-js')

{{--Main--}}
<div id="root">
    {{--Header--}}
    @include('common-frontend::layout.header', $includeData)

    <main class="main-content">
        {{--Content--}}
        @yield('content')
    </main>
    {{--Footer--}}
    @include('common-frontend::layout.footer', $includeData)
</div>
{{--Scripts--}}
{!! Meta::footer()->toHtml() !!}
{!! $page->assets->js->after_body_html ?? '' !!}

<script>
    moment.locale("pt-br");

    // Ajax calls should always have the CSRF token attached to them, otherwise they won't work
    $.ajaxSetup({
        // force ajax call on all browsers
        cache: false,
        // Enables cross domain requests
        crossDomain: true,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        }
    });

</script>

@include('common-frontend::layout.inc.alerts', $includeData)

@stack('scripts')
@stack('js')
@stack('js-includes')
@yield('js-includes')
</body>
</html>
