<?php
/**
 * @var \Adminx\Common\Models\Pages\Page $page
 */

use Illuminate\Support\Collection;

?>
@props([
    'page' => new \Adminx\Common\Models\Pages\Page(),
    'append' => [],
    'prepend' => [],
    'between' => [],
    'bgImage' => null,
])
@php

    $breadcrumbConfig = $page->config->breadcrumb ?? $page->site->theme->config->breadcrumb;

    //Breadcrumb Itens - Start with prepends
    $bcItems = Collection::wrap($prepend);

    //add Home
    $bcItems = $bcItems->merge(['/' => 'Home']);

    if($breadcrumbConfig->default_items ?? false && $breadcrumbConfig->default_items->filter()->count()){
        $bcItems = $bcItems->merge($breadcrumbConfig->default_items->filter()->pluck('title', 'url'));
    }

    //Add between
    $bcItems = $bcItems->merge(Collection::wrap($between)->toArray());

    //Add Page
    if ($page->id ?? false) {
        $bcItems = $bcItems->merge(count($append) ? [$page->url => $page->title] : [$page->title]);
    }

    //Add append
    $bcItems = $bcItems->merge(Collection::wrap($append)->toArray())->filter();

    $bctitle = $bcItems->last();

    $bgImage = $bgImage ?? $breadcrumbConfig->background->file->url ?? false;

    $bcStyle = '';

    if($breadcrumbConfig->height ?? false){
           $bcStyle .= "height: {$breadcrumbConfig->height}px; ";
        }
    if($bgImage) {
           $bcStyle .= "background-image: url('{$bgImage}'); ";
        }
@endphp
<div id="breadcrumb-area"
     class="lazy breadcrumb-area breadcrumb-section breadcrumb-height site-breadcrumb-{{ $page->site->public_id }} page-breadcumb-{{ $page->public_id }}"
     data-src="{{ $bgImage ?? '' }}" style="{{ $bcStyle }} background-repeat: no-repeat; background-size: cover;">
    <div class="container h-100">
        <div class="row h-100">
            <div class="col-lg-12 d-flex align-items-center">
                <div class="breadcrumb-content breadcrumb-text d-flex align-items-start flex-column w-100">
                    @if($breadcrumbConfig->show_title)
                        <h1 id="breadcrumb-heading" class="breadcrumb-heading page-title">{{ $bcItems->last() }}</h1>
                    @endif
                    @if($breadcrumbConfig->show_navigation)
                        <nav id="breadcrumb" aria-label="breadcrumb"
                             style="{!! $breadcrumbConfig->separator->css() ?? '' !!}">
                            <ol class="breadcrumb">
                                @foreach($bcItems as $url => $title)
                                    <li class="breadcrumb-item flex-row {{ $loop->last ? 'active' : '' }}" {{ $loop->last ? 'aria-current=page' : '' }}>
                                        <a {{ is_string($url) && !empty($url) ? "href={$url}" : 'role=button' }}>{!! $title !!}</a>
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@push('css')
    <style>
        @if($breadcrumbConfig->height ?? false)
        #breadcrumb-area.breadcrumb-area {
            height: {{ $breadcrumbConfig->height }}px;
        }

        @endif

        @if($bgImage)
        #breadcrumb-area.breadcrumb-area {
            background-image: url('{{ $bgImage }}');
            background-repeat: no-repeat;
            background-size: cover;
        }
        @endif
    </style>
@endpush
