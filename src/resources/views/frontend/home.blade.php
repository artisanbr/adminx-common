<?php
/**
 * @var \ArtisanBR\Adminx\Common\App\Models\Site $site
 * @var \ArtisanBR\Adminx\Common\App\Models\Page $homePage
 */
?>
@extends('adminx-frontend::layout.base', [
    'site' => $site ?? new \ArtisanBR\Adminx\Common\App\Models\Site(),
    'page' => $homePage ?? new \ArtisanBR\Adminx\Common\App\Models\Page(['is_home' => true])
])

@section('content')
    {{--Todo: slide--}}
    {{--Home Content--}}
    {!! $homePage->html !!}
@endsection
