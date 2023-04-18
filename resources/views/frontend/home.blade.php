<?php
/**
 * @var \Adminx\Common\Models\Site $site
 * @var \Adminx\Common\Models\Page $homePage
 */
?>
@extends('adminx-frontend::layout.base', [
    'site' => $site ?? new \Adminx\Common\Models\Site(),
    'page' => $homePage ?? new \Adminx\Common\Models\Page(['is_home' => true])
])

@section('content')
    {{--Todo: slide--}}
    {{--Home Content--}}
    {!! $homePage->html !!}
@endsection
