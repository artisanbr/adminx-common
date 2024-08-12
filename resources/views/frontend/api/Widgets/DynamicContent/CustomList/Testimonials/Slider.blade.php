<?php
/***
 * @var \Adminx\Common\Models\Widgets\SiteWidget                                                                                        $widget
 * @var \Adminx\Common\Models\CustomLists\CustomListTestimonials                                                                $customList
 * @var \Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemTestimonials                                            $listItem
 * @var \Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemTestimonials[]|\Illuminate\Database\Eloquent\Collection $customListItems
 */
?>
@extends('common::layouts.api.ajax-view')

@if($customListItems->count())
    <div class="testimonials-slide testimonials-slide-{{ $widget->public_id }} widget-module widget-module-{{ $widget->public_id }}">
        {{--Left--}}
        @foreach($customListItems as $listItem)
            <div class="testimonials-item" data-thumb="{{ $listItem->image_url }}">
                <div class="testimonials-author-image author-img">
                    <img src="{{ $listItem->image_url }}" alt="Image">
                </div>
                <div class="content">
                    <div class="d-flex">
                        <span class="quote-top"><i class="fa-solid fa-quote-left"></i></span>
                    </div>
                    {!! $listItem->content !!}
                    <div class="d-flex justify-content-end">
                        <span class="quote-bottom"><i class="fa-solid fa-quote-right"></i></span>
                    </div>
                </div>
                <div class="author-name">
                    <h4>{{ $listItem->title }}</h4>
                    <span>{{ $listItem->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @endforeach


    </div>

    <div class="testimonials-dots testimonials-dots-{{ $widget->public_id }}"></div>

@endif
@push('css')
    <style>

        .testimonials-slide-{{ $widget->public_id }} .testimonials-item {
            font-size: 24px;
            line-height: 1.583;
            text-align: center;
        }

        @media (max-width: 991px) {
            .testimonials-slide-{{ $widget->public_id }} .testimonials-item {
                font-size: 20px;
            }
        }

        @media (max-width: 575px) {
            .testimonials-slide-{{ $widget->public_id }} .testimonials-item {
                font-size: 18px;
            }
        }

        .testimonials-slide-{{ $widget->public_id }} .testimonials-item .quote-top,
        .testimonials-slide-{{ $widget->public_id }} .testimonials-item .quote-bottom {
            color: #333;
            font-size: 16px;
            position: relative;
        }

        .testimonials-slide-{{ $widget->public_id }} .testimonials-item .quote-top {
            margin-right: 10px;
            top: -5px;
        }

        .testimonials-slide-{{ $widget->public_id }} .testimonials-item .quote-bottom {
            margin-left: 10px;
            bottom: -5px;
        }

        .testimonials-slide-{{ $widget->public_id }} .testimonials-item .author-img {
            margin-bottom: 50px;
        }

        .testimonials-slide-{{ $widget->public_id }} .testimonials-item .author-img img {
            border-radius: 15px;
            height: 100px;
            width: 100px;
            box-shadow: 0px 10px 30px 0px rgba(20, 33, 43, 0.32);
        }

        .testimonials-slide-{{ $widget->public_id }} .testimonials-item .author-name {
            margin-top: 40px;
        }

        .testimonials-slide-{{ $widget->public_id }} .testimonials-item .author-name h4 {
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -1px;
        }

        .testimonials-slide-{{ $widget->public_id }} .testimonials-item .author-name span {
            font-weight: 600;
            color: #333;
            font-size: 16px;
        }

        .testimonials-slide-{{ $widget->public_id }} .slick-arrow {
            position: absolute;
            left: -18%;
            top: 50%;
            font-size: 50px;
            line-height: 1;
            background-color: transparent;
            color: #333333;
            z-index: 2;
            opacity: 0.3;
            -webkit-transition: all 0.3s ease-out 0s;
            -moz-transition: all 0.3s ease-out 0s;
            -ms-transition: all 0.3s ease-out 0s;
            -o-transition: all 0.3s ease-out 0s;
            transition: all 0.3s ease-out 0s;
        }

        @media (max-width: 1599px) {
            .testimonials-slide-{{ $widget->public_id }} .slick-arrow {
                left: -15%;
            }
        }

        @media (max-width: 991px) {
            .testimonials-slide-{{ $widget->public_id }} .slick-arrow {
                left: -8%;
                font-size: 35px;
            }
        }

        .testimonials-slide-{{ $widget->public_id }} .slick-arrow.next-arrow {
            left: auto;
            right: -18%;
        }

        @media (max-width: 1599px) {
            .testimonials-slide-{{ $widget->public_id }} .slick-arrow.next-arrow {
                right: -15%;
            }
        }

        @media (max-width: 991px) {
            .testimonials-slide-{{ $widget->public_id }} .slick-arrow.next-arrow {
                right: -8%;
            }
        }

        .testimonials-slide-{{ $widget->public_id }} .slick-arrow:hover {
            opacity: 1;
            color: #333;
        }

        .widget-{{ $widget->public_id }} .testimonials-dots {
            text-align: center;
            margin-top: 55px;
        }

        @media (max-width: 575px) {
            .widget-{{ $widget->public_id }} .testimonials-dots {
                display: none;
            }
        }

        .widget-{{ $widget->public_id }} .testimonials-dots li {
            display: inline-block;
            margin: 0 10px;
            width: 60px;
            height: 60px;
        }

        .widget-{{ $widget->public_id }} .testimonials-dots li img {
            cursor: pointer;
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }
    </style>
@endpush
@push('js')
    <script>
        $(function () {
            $('.widget-module-{{ $widget->public_id }}').slick({
                infinite: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                speed: 500,
                arrows: true,
                fade: false,
                dots: true,
                swipe: true,
                nextArrow: '<button class="slick-arrow next-arrow"><i class="fa-solid fa-long-arrow-right"></i></button>',
                prevArrow: '<button class="slick-arrow prev-arrow"><i class="fa-solid fa-long-arrow-left"></i></button>',
                appendDots: $('.testimonials-dots-{{ $widget->public_id }}'),
                responsive: [
                    {
                        breakpoint: 991,
                        settings: {
                            arrows: false
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            arrows: false
                        }
                    }],
                customPaging: function (slick, index) {
                    var portrait = $(slick.$slides[index]).data('thumb');
                    return '<img src=" ' + portrait + ' "/>';
                },
            });
        });
    </script>
@endpush
