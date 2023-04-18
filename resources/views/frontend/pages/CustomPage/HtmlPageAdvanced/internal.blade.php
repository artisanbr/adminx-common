<?php
/***
 * @var \Adminx\Common\Models\Site                                                                                                        $site
 * @var \Adminx\Common\Models\Page                                                                                                        $page
 * @var \Adminx\Common\Models\Bases\CustomListItemBase|\Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemHtml $currentItem
 */
?>
@extends('adminx-frontend::layout.partials.content', compact('page'))

@section('content')

    @if(!$page->is_home && ($page->config->breadcrumb ? $page->config->breadcrumb->enable : $site->theme->config->breadcrumb->enable))
        <x-frontend::breadcrumb :page="$page" :append="[$currentItem->title]"
                                :bg-image="$currentItem->data->image->file->url ?? null"/>
    @endif

    {{--Home Content--}}
    {!! $page->buildedInternalHtml($currentItem) !!}

@endsection
