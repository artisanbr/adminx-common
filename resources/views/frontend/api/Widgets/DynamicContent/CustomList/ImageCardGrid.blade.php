<?php
/***
 * @var \Adminx\Common\Models\Pages\Page                                                                                $page
 * @var \Adminx\Common\Models\SiteWidget                                                                                $widgeteable
 * @var \Adminx\Common\Models\CustomLists\CustomListHtml                                                                $customList
 * @var \Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemHtml                                            $listItem
 * @var \Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemHtml[]|\Illuminate\Database\Eloquent\Collection $customListItems
 */
?>
@extends('adminx-common::layouts.api.ajax-view')
{{--@php
    $customList = $widgeteable->source->data;
    $page = $customList->page;
@endphp--}}
@if($customListItems->count())
    <div
            class="image-card-grid image-card-grid-{{ $widgeteable->public_id }} widget-module widget-module-{{ $widgeteable->public_id }} row justify-content-center">
        {{--Left--}}
        @foreach($customListItems as $listItem)
            <div class="col-lg-4 col-sm-6">
                <div class="image-card-grid-box">
                    <div class="image-card-grid-thumb">
                        <div class="thumb bg-img-c"
                             style="background-image: url('{{ $listItem->data->image->file->url }}');"></div>
                    </div>
                    <div class="image-card-grid-desc text-center">
                        <h4><a href="{{ $customList->itemUrl($listItem) ?? '#' }}">{{ $listItem->title }}</a></h4>
                        <p>{!! $listItem->description ?? '' !!}</p>
                        <a href="#" class="image-card-grid-link">
                            <i class="fa-solid fa-long-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endif
@push('css')
    <style>
        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box {
            position: relative;
            height: 415px;
            margin-bottom: 90px;
        }

        @media (max-width: 767px) {
            .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box {
                height: 320px;
            }
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box .image-card-grid-thumb {
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box .image-card-grid-thumb .thumb {
            -webkit-transition: all 0.3s ease-out 0s;
            -moz-transition: all 0.3s ease-out 0s;
            -ms-transition: all 0.3s ease-out 0s;
            -o-transition: all 0.3s ease-out 0s;
            transition: all 0.3s ease-out 0s;
            height: 100%;
            width: 100%;
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box .image-card-grid-desc {
            position: absolute;
            left: 30px;
            right: 30px;
            bottom: -55px;
            padding: 35px 15px 25px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0px 10px 32px 0px rgba(197, 197, 197, 0.4);
            z-index: 2;
            color: #333333;
            line-height: 1.2;
            -webkit-transition: all 0.3s ease-out 0s;
            -moz-transition: all 0.3s ease-out 0s;
            -ms-transition: all 0.3s ease-out 0s;
            -o-transition: all 0.3s ease-out 0s;
            transition: all 0.3s ease-out 0s;
        }

        @media (max-width: 767px) {
            .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box .image-card-grid-desc {
                left: 15px;
                right: 15px;
                font-size: 15px;
            }
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box .image-card-grid-desc h4 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            -webkit-transition: all 0.3s ease-out 0s;
            -moz-transition: all 0.3s ease-out 0s;
            -ms-transition: all 0.3s ease-out 0s;
            -o-transition: all 0.3s ease-out 0s;
            transition: all 0.3s ease-out 0s;
        }

        @media (max-width: 767px) {
            .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box .image-card-grid-desc h4 {
                font-size: 20px;
            }
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box .image-card-grid-desc .image-card-grid-link {
            font-size: 24px;
            margin-top: 10px;
            color: #333333;
            -webkit-transition: all 0.3s ease-out 0s;
            -moz-transition: all 0.3s ease-out 0s;
            -ms-transition: all 0.3s ease-out 0s;
            -o-transition: all 0.3s ease-out 0s;
            transition: all 0.3s ease-out 0s;
            line-height: 1;
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box:hover .image-card-grid-desc {
            box-shadow: none;
            background-color: #333;
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box:hover .image-card-grid-desc,
        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box:hover .image-card-grid-desc h4 a,
        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box:hover .image-card-grid-desc .image-card-grid-link {
            color: #fff;
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box:hover .image-card-grid-thumb .thumb {
            transform: scale(1.1);
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.hover-style {
            margin-bottom: 30px;
            height: 370px;
            overflow: hidden;
        }

        @media (max-width: 767px) {
            .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.hover-style {
                height: 300px;
            }
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.hover-style .image-card-grid-thumb {
            position: relative;
        }

        @media (max-width: 575px) {
            .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.hover-style .image-card-grid-thumb .thumb {
                background-position: 0 5%;
            }
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.hover-style .image-card-grid-thumb::before {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            content: "";
            background-color: #333333;
            opacity: 0;
            visibility: hidden;
            -webkit-transition: all 0.3s ease-out 0s;
            -moz-transition: all 0.3s ease-out 0s;
            -ms-transition: all 0.3s ease-out 0s;
            -o-transition: all 0.3s ease-out 0s;
            transition: all 0.3s ease-out 0s;
            z-index: 1;
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.hover-style .image-card-grid-desc {
            bottom: -10px;
            visibility: hidden;
            opacity: 0;
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.hover-style:hover .image-card-grid-desc {
            visibility: visible;
            opacity: 1;
            bottom: 0;
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.hover-style:hover .image-card-grid-thumb::before {
            opacity: 0.45;
            visibility: visible;
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.wide-box .image-card-grid-desc {
            left: 65px;
            right: 65px;
        }

        @media (max-width: 991px) {
            .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.wide-box .image-card-grid-desc {
                left: 30px;
                right: 30px;
            }
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.wide-long-box {
            height: 770px;
        }

        @media (max-width: 767px) {
            .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.wide-long-box {
                height: 630px;
            }
        }

        .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.wide-long-box .image-card-grid-desc {
            left: 65px;
            right: 65px;
        }

        @media (max-width: 991px) {
            .image-card-grid-{{ $widgeteable->public_id }} .image-card-grid-box.wide-long-box .image-card-grid-desc {
                left: 30px;
                right: 30px;
            }
        }
    </style>
@endpush
{{--@push('js')
    <script>
        $(function () {
            //Todo: slick
            /*$('#widget-{{ $widgeteable->public_id }} .testimonials-carousel').slick({
                dots: false,
                arrows: false,
                infinite: false,
                speed: 300,
                slidesToShow: 2,
                slidesToScroll: 1,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            infinite: true,
                            dots: false
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 2
                        }
                    },

                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }

                ]

            });*/
        });
    </script>
@endpush--}}
