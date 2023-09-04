<?php
/**
 * @var \Adminx\Common\Models\Pages\Page $page
 */
?>
@extends('common-frontend::layout.partials.content', compact('page'))

@section('content')
    @if(!$page->is_home && ($page->config->breadcrumb ? $page->config->breadcrumb->enable : $site->theme->config->breadcrumb->enable))
        <x-frontend::breadcrumb :page="$page"/>
    @endif
    {{--Home Content--}}
    {!! $page->html !!}
@endsection
