<?php
/**
 * @var \Adminx\Common\Models\Widgeteable $widgeteable
 * @var \Adminx\Common\Models\Site        $site
 * @var \Adminx\Common\Models\Page        $page
 */
?>
@extends('adminx-frontend::layout.base', [
    'site' => $site ?? new \Adminx\Common\Models\Site(),
    'page' => $page ?? new \Adminx\Common\Models\Page(['is_home' => false])
])

@section('content')
    {!! $page->buildedHtml() !!}
@endsection
