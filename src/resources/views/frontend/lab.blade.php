<?php
/**
 * @var \ArtisanBR\Adminx\Common\App\Models\Widgeteable $widgeteable
 * @var \ArtisanBR\Adminx\Common\App\Models\Site        $site
 * @var \ArtisanBR\Adminx\Common\App\Models\Page        $page
 */
?>
@extends('adminx-frontend::layout.base', [
    'site' => $site ?? new \ArtisanBR\Adminx\Common\App\Models\Site(),
    'page' => $page ?? new \ArtisanBR\Adminx\Common\App\Models\Page(['is_home' => false])
])

@section('content')
    {!! $page->buildedHtml() !!}
@endsection
