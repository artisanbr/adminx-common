<?php
/***
 * @var \Adminx\Common\Models\Sites\Site                                                                                                        $site
 * @var \Adminx\Common\Models\Pages\Page                                                                                                        $page
 * @var \Adminx\Common\Models\CustomLists\Abstract\CustomListItemBase|\Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemHtml $currentItem
 */
?>
@extends('common-frontend::layout.partials.content', compact('page'))

@section('content')

    @if(!$page->is_home && ($page->config->breadcrumb ? $page->config->breadcrumb->enable : $site->theme->config->breadcrumb->enable))
        <x-frontend::breadcrumb :page="$page" :append="[$currentItem->title]"
                                :bg-image="$currentItem->data->image_url ?? null"/>
    @endif

    {{--Home Content--}}
    {!! $page->buildedInternalHtml($currentItem) !!}

@endsection
