<?php
/**
 * @var \Adminx\Common\Models\Sites\Site $site
 * @var \Adminx\Common\Models\Pages\Page $homePage
 */
?>
@extends('adminx-frontend::layout.base', [
    'site' => $site ?? new \Adminx\Common\Models\Sites\Site(),
    'page' => $homePage ?? new \Adminx\Common\Models\Pages\Page(['is_home' => true])
])

@section('content')
    {{--Todo: slide--}}
    {{--Home Content--}}
    {!! $homePage->html !!}
@endsection
