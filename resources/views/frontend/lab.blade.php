<?php
/**
 * @var \Adminx\Common\Models\Widgets\SiteWidget $widgeteable
 * @var \Adminx\Common\Models\Site       $site
 * @var \Adminx\Common\Models\Pages\Page $page
 */
?>
@extends('adminx-frontend::layout.base', [
    'site' => $site ?? new \Adminx\Common\Models\Site(),
    'page' => $page ?? new \Adminx\Common\Models\Pages\Page(['is_home' => false])
])

@section('content')
    {!! $page->buildedHtml() !!}
@endsection
