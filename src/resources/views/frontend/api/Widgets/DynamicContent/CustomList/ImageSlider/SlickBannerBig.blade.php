<?php
/***
 * @var \ArtisanBR\Adminx\Common\App\Models\Widgeteable                                                       $widgeteable
 * @var \ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListImageSlider                                 $customList
 * @var \ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItemImageSlider             $listItem
 * @var \ArtisanBR\Adminx\Common\App\Models\CustomLists\Generic\CustomListItemDatas\Sliders\SliderDataButtons $button
 *                                                                                                                   @var \ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItemImageSlider[]|\Illuminate\Database\Eloquent\Collection $customListItems
 */
?>
@extends('adminx-common::layouts.api.ajax-view')

{{--@php
    $customList = \ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomList::findAndMount($widgeteable->source_ids->first());
@endphp--}}
@if($customListItems->count())
    <section
            class="banner-section banner-section-{{ $widgeteable->public_id }} widget-module widget-module-{{ $widgeteable->public_id }}">
        {{--Left--}}
        <div class="banner-slider" id="bannerSlider-{{ $widgeteable->public_id }}">
            @foreach($customListItems as $listItem)
                @push('css')
                    <link rel="preload" as="image" href="{{ $listItem->data->image->file->url }}" />
                @endpush
                <div class="single-banner" style="background-image: url({{ $listItem->data->image->file->url }});">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-10">
                                <div class="banner-content text-center">
                                    <div data-animation="fadeInDown" data-delay="0.8s">{!! $listItem->title !!}</div>
                                    <p data-animation="fadeInUp" data-delay="1s">
                                        {!! $listItem->data->description !!}
                                    </p>
                                    <ul class="btn-wrap">
                                        @foreach($listItem->data->buttons as $button)
                                            <li data-animation="fadeInLeft" data-delay="1.2s">
                                                {{--<a href="{{ $button->url }}" {{ $button->external ? 'target="_blank"' : '' }} class="main-btn main-btn-4">{{ $button->content }}</a>--}}
                                                {!! $button->html !!}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </section>
@endif
@push('css')
    <style>
        .banner-section.banner-section-{{ $widgeteable->public_id }} .single-banner {
            padding-bottom: 220px;
            padding-top: 365px;
        }

        @media (max-width: 767px) {
            .banner-section.banner-section-{{ $widgeteable->public_id }} .single-banner {
                padding-top: 220px;
                padding-bottom: 150px;
            }
        }

        .banner-section.banner-section-{{ $widgeteable->public_id }} .single-banner::before {
            background: radial-gradient(circle, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.9) 88%);
            background: -webkit-radial-gradient(circle, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.9) 88%);
            opacity: 1;
        }

        .banner-section.banner-section-{{ $widgeteable->public_id }} .single-banner::after {
            position: absolute;
            right: 0;
            bottom: 0;
            z-index: -1;
            content: "";
            width: 100%;
            height: 100%;
            background-repeat: no-repeat;
            background-position: right bottom;
        }

        @media (max-width: 575px) {
            .banner-section.banner-section-{{ $widgeteable->public_id }} .single-banner::after {
                background-size: 300px;
            }
        }

        @media (max-width: 991px) {
            .banner-section.banner-section-{{ $widgeteable->public_id }} .single-banner p br {
                display: none;
            }
        }

        .banner-section.banner-section-{{ $widgeteable->public_id }} .slick-arrow {
            visibility: visible;
        }
    </style>
@endpush
@php
    $jsModuleName = "widgetJsModule_{$widgeteable->public_id}";
@endphp
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {

            const {{ $jsModuleName }} = function () {

                const initBanner = function () {

                    let banner = $('#bannerSlider-{{ $widgeteable->public_id }}');
                    let bannerFirst = banner.find('.single-banner:first-child');

                    banner.on('init', function (e, slick) {
                        let firstAnimatingElements = bannerFirst.find(
                            '[data-animation]'
                        );
                        slideanimate(firstAnimatingElements);
                    });

                    banner.on('beforeChange', function (
                        e,
                        slick,
                        currentSlide,
                        nextSlide
                    ) {
                        let animatingElements = $(
                            'div.slick-slide[data-slick-index="' + nextSlide + '"]'
                        ).find('[data-animation]');
                        slideanimate(animatingElements);
                    });

                    banner.slick({
                        infinite: true,
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        autoplay: false,
                        autoplaySpeed: 5000,
                        speed: 500,
                        arrows: true,
                        fade: false,
                        dots: false,
                        swipe: true,
                        adaptiveHeight: true,
                        nextArrow: '<button class="slick-arrow next-arrow"><i class="fa-solid fa-long-arrow-right"></i></button>',
                        prevArrow: '<button class="slick-arrow prev-arrow"><i class="fa-solid fa-long-arrow-left"></i></button>',
                        responsive: [{
                            breakpoint: 768,
                            settings: {
                                arrows: false
                            }
                        }],
                    });
                };


                const slideanimate = function (elements) {
                    var animationEndEvents =
                        'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
                    elements.each(function () {
                        var $this = $(this);
                        var animationDelay = $this.data('delay');
                        var animationType = 'animated ' + $this.data('animation');
                        $this.css({
                            'animation-delay': animationDelay,
                            '-webkit-animation-delay': animationDelay,
                        });
                        $this
                            .addClass(animationType)
                            .one(animationEndEvents, function () {
                                $this.removeClass(animationType);
                            });
                    });
                }

                return {
                    init: function () {
                        initBanner();
                    }
                }
            }();

            {{ $jsModuleName }}.init();
        });
    </script>
@endpush
