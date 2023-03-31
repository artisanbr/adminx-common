<?php
/***
 * @var \ArtisanBR\Adminx\Common\App\Models\Widgeteable                                    $widgeteable
 * @var \ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListHtml                     $customList
 * @var \ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItemHtml $listItem
 */
?>
@extends('adminx-common::layouts.api.ajax-view')

@php
    $customList = \ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomList::findAndMount($widgeteable->source_ids->first());
@endphp
@if($customList->items()->count())
    <div
            class="tab-buttons image-html-tabs image-html-tabs-{{ $widgeteable->public_id }} widget-module widget-module-{{ $widgeteable->public_id }}">
        {{--Tabs--}}
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            @foreach($customList->items()->take(10)->get() as $listItem)
                <a @class([
        'active' => $loop->first
]) id="nav-{{ $widgeteable->public_id }}" data-toggle="tab"
                   href=".image-html-tabs-{{ $widgeteable->public_id }} #tab-{{ $listItem->public_id }}" role="tab">
                    {{ $listItem->title }}
                </a>
            @endforeach
        </div>

        <div class="tab-content" id="nav-tabContent">
            @foreach($customList->items()->take(10)->get() as $listItem)
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


    {{--<div class="tab-buttons">
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="active" id="nav-mission" data-toggle="tab" href="#tab-mission" role="tab">Our Mission &
                Vision</a>
            <a id="nav-history" data-toggle="tab" href="#tab-history" role="tab">Company History</a>
            <a id="nav-business" data-toggle="tab" href="#tab-business" role="tab">Business Goals</a>
            <a id="nav-team" data-toggle="tab" href="#tab-team" role="tab">Team Member</a>
        </div>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="tab-mission" role="tabpanel">
                <div class="tab-text-block left-image with-left-circle">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-lg-6 col-md-10">
                            <div class="block-image">
                                <img src="assets/img/tab-block.jpg" alt="Image">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-10">
                            <div class="block-text">
                                <h2 class="title">Professional Business Guidance Agency</h2>
                                <p>
                                    Sedut perspiciatis unde omnis iste natus error sit voluptat em accusantium
                                    doloremque laudantium, totam raperiaeaque ipsa quae ab illo inventore
                                    veritatis et quasi
                                </p>
                                <ul>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        There are many variations of passages of LoreIpsum available, but the
                                        majority have suffered
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        It uses a dictionary of over 200 Latin wor combined with a handful of
                                        model sentence structure
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        Richard McClintock, a Latin profe hampden-sydney College in Virginia,
                                        looked up one more
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-history" role="tabpanel">
                <div class="tab-text-block right-image with-right-circle">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-lg-6 col-md-10 order-2 order-lg-1">
                            <div class="block-text">
                                <h2 class="title">Professional Business Guidance Agency</h2>
                                <p>
                                    Sedut perspiciatis unde omnis iste natus error sit voluptat em accusantium
                                    doloremque laudantium, totam raperiaeaque ipsa quae ab illo inventore
                                    veritatis et quasi
                                </p>
                                <ul>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        There are many variations of passages of LoreIpsum available, but the
                                        majority have suffered
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        It uses a dictionary of over 200 Latin wor combined with a handful of
                                        model sentence structure
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        Richard McClintock, a Latin profe hampden-sydney College in Virginia,
                                        looked up one more
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-10 order-1 order-lg-2">
                            <div class="block-image">
                                <img src="assets/img/tab-block.jpg" alt="Image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-business" role="tabpanel">
                <div class="tab-text-block left-image with-left-circle">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-lg-6 col-md-10">
                            <div class="block-image">
                                <img src="assets/img/tab-block.jpg" alt="Image">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-10">
                            <div class="block-text">
                                <h2 class="title">Professional Business Guidance Agency</h2>
                                <p>
                                    Sedut perspiciatis unde omnis iste natus error sit voluptat em accusantium
                                    doloremque laudantium, totam raperiaeaque ipsa quae ab illo inventore
                                    veritatis et quasi
                                </p>
                                <ul>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        There are many variations of passages of LoreIpsum available, but the
                                        majority have suffered
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        It uses a dictionary of over 200 Latin wor combined with a handful of
                                        model sentence structure
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        Richard McClintock, a Latin profe hampden-sydney College in Virginia,
                                        looked up one more
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-team" role="tabpanel">
                <div class="tab-text-block right-image with-right-circle">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-lg-6 col-md-10 order-2 order-lg-1">
                            <div class="block-text">
                                <h2 class="title">Professional Business Guidance Agency</h2>
                                <p>
                                    Sedut perspiciatis unde omnis iste natus error sit voluptat em accusantium
                                    doloremque laudantium, totam raperiaeaque ipsa quae ab illo inventore
                                    veritatis et quasi
                                </p>
                                <ul>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        There are many variations of passages of LoreIpsum available, but the
                                        majority have suffered
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        It uses a dictionary of over 200 Latin wor combined with a handful of
                                        model sentence structure
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        Richard McClintock, a Latin profe hampden-sydney College in Virginia,
                                        looked up one more
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-10 order-1 order-lg-2">
                            <div class="block-image">
                                <img src="assets/img/tab-block.jpg" alt="Image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>--}}

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
