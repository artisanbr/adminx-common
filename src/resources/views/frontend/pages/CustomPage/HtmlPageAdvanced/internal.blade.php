<?php
/***
 * @var \ArtisanBR\Adminx\Common\App\Models\Site                                                                                                        $site
 * @var \ArtisanBR\Adminx\Common\App\Models\Page                                                                                                        $page
 * @var \ArtisanBR\Adminx\Common\App\Models\Bases\CustomListItemBase|\ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItemHtml $currentItem
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
