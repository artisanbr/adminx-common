<?php
/**
 * @var \Adminx\Common\Models\Pages\Page $page
 */

?>
{{--@@push('css')
    @{!! $page->css_html ?? '' !!}
@@endpush
@@push('head-js')
    @{!! $page->js->head_js_html ?? '' !!}
@@endpush
@@push('body-js')
    @{!! $page->js->after_body_js_html ?? '' !!}
@@endpush--}}
<main class="main-content">
    {{--Content--}}
    @yield('content')
</main>
{{--@@push('js')
    @{!! $page->js_html ?? '' !!}
@@endpush--}}
