<?php
/***
 * @var \Adminx\Common\Models\SiteWidget                                                                                $widgeteable
 * @var \Adminx\Common\Models\CustomLists\CustomListHtml                                                                $customList
 * @var \Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemHtml                                            $listItem
 * @var \Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemHtml[]|\Illuminate\Database\Eloquent\Collection $customListItems
 */
?>
@extends('adminx-common::layouts.api.ajax-view')

@if($customListItems->count())
    <div
            class="tab-buttons image-html-tabs image-html-tabs-{{ $widgeteable->public_id }} widget-module widget-module-{{ $widgeteable->public_id }}">
        {{--Tabs--}}
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            @foreach($customListItems as $listItem)
                <a @class([
        'active' => $loop->first
]) id="nav-{{ $widgeteable->public_id }}" data-toggle="tab"
                   href=".image-html-tabs-{{ $widgeteable->public_id }} #tab-{{ $listItem->public_id }}" role="tab">
                    {{ $listItem->title }}
                </a>
            @endforeach
        </div>

        <div class="tab-content" id="nav-tabContent">
            @foreach($customListItems as $listItem)
                <div @class([
        'tab-pane fade',
        'show active' => $loop->first
]) id="tab-{{ $listItem->public_id }}" role="tabpanel">
                    <div class="row align-items-center justify-content-center">
                        @if($listItem->data->image)
                            <div @class(['col-lg-6 col-md-10', 'order-1' => $loop->odd])>
                                <div class="block-image">
                                    <img src="{{ $listItem->data->image->url }}" alt="Image">
                                </div>
                            </div>
                        @endif
                        <div @class(['col-lg-6 col-md-10', 'order-0 pe-5 pr-5' => $loop->odd, 'ps-5 pl-5' => !$loop->odd]) >
                            {!! $listItem->data->html !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

@endif
@push('css')
    <style>
        .widget-module-{{ $widgeteable->public_id }} .nav-tabs {
            border: none;
            justify-content: center;
        }

        @media (max-width: 767px) {
            .widget-module-{{ $widgeteable->public_id }} .nav-tabs {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                grid-gap: 10px;
            }
        }

        @media (max-width: 399px) {
            .widget-module-{{ $widgeteable->public_id }} .nav-tabs {
                display: grid;
                grid-template-columns: repeat(1, 1fr);
                grid-gap: 10px;
            }
        }

        .widget-module-{{ $widgeteable->public_id }} .nav-tabs a {
            font-size: 18px;
            font-weight: 700;
            font-family: "Source Sans Pro", sans-serif;
            color: #333333;
            background-color: #f5f5f5;
            text-transform: uppercase;
            padding: 10px 40px;
            margin: 0 10px;
        }

        @media (max-width: 1199px) {
            .widget-module-{{ $widgeteable->public_id }} .nav-tabs a {
                font-size: 16px;
                padding: 10px 30px;
                margin: 0 5px;
            }
        }

        @media (max-width: 991px) {
            .widget-module-{{ $widgeteable->public_id }} .nav-tabs a {
                margin-bottom: 10px;
            }
        }

        @media (max-width: 767px) {
            .widget-module-{{ $widgeteable->public_id }} .nav-tabs a {
                font-size: 15px;
                padding: 8px 10px;
                margin: 0;
                display: block;
                text-align: center;
            }
        }

        @media (max-width: 399px) {
            .widget-module-{{ $widgeteable->public_id }} .nav-tabs a {
                text-align: left;
            }
        }

        .widget-module-{{ $widgeteable->public_id }} .nav-tabs a:hover, .widget-module-{{ $widgeteable->public_id }} .nav-tabs a.active {
            background-color: #333;
            color: #fff;
        }

        .widget-module-{{ $widgeteable->public_id }} .tab-content {
            padding-top: 60px;
        }

        @media (max-width: 991px) {
            .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block .block-text {
                margin-top: 50px;
            }
        }

        .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block .block-text .title {
            font-size: 50px;
            margin-bottom: 30px;
        }

        @media (max-width: 1199px) {
            .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block .block-text .title {
                font-size: 42px;
            }
        }

        @media (max-width: 767px) {
            .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block .block-text .title {
                font-size: 34px;
            }
        }

        @media (max-width: 575px) {
            .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block .block-text .title {
                font-size: 28px;
            }
        }

        .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block .block-text ul li {
            padding-left: 70px;
            position: relative;
            margin-top: 30px;
        }

        .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block .block-text ul li i {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            line-height: 50px;
            text-align: center;
            color: #333;
            border: 2px solid #333;
            border-radius: 50%;
        }

        .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block.left-image .block-text {
            padding-left: 50px;
        }

        @media (max-width: 1199px) {
            .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block.left-image .block-text {
                padding-left: 30px;
            }
        }

        @media (max-width: 991px) {
            .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block.left-image .block-text {
                padding-left: 0;
            }
        }

        .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block.right-image .block-text {
            padding-right: 50px;
        }

        @media (max-width: 1199px) {
            .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block.right-image .block-text {
                padding-right: 30px;
            }
        }

        @media (max-width: 991px) {
            .widget-module-{{ $widgeteable->public_id }} .tab-content .tab-text-block.right-image .block-text {
                padding-right: 0;
            }
        }
    </style>
@endpush
