<?php
/***
 * @var \Adminx\Common\Models\Widgeteable $widgeteable
 * @var \Adminx\Common\Models\Page        $page
 * @var \Adminx\Common\Models\Post        $post
 * @var \Adminx\Common\Models\Post[]|\Illuminate\Database\Eloquent\Collection        $posts
 */
?>
@extends('adminx-common::layouts.api.ajax-view')

@if($posts->count())
    <div
            class="row posts-carousel posts-carousel-{{ $widgeteable->public_id }} widget-module widget-module-{{ $widgeteable->public_id }}">
        {{--Left--}}
        @foreach($posts as $post)
            <div class="col-lg-3 col-md-3 posts-carousel-left">
                <x-frontend::pages.Posts.post-miniature-2 :post="$post"
                                                          :show-description="$variables['show_description'] ?? false"
                                                          class="posts-carousel-item widget-post-item mb-30"
                                                          :show-read-more="$variables['show_read_more'] ?? false"
                                                          :read-more-text="$variables['read_more_text'] ?? 'Leia Mais'"
                                                          :show-categories="$variables['show_categories'] ?? false"
                                                          :title-lines="$variables['title_lines'] ?? 1"
                                                          :show-date="$variables['show_date'] ?? false"
                                                          :date-format="$variables['date_format'] ?? false"/>
            </div>
        @endforeach

    </div>
@endif

@push('css')
    <style>


        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item {
            margin-bottom: 30px;
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-thumb-wrap {
            width: 100%;
            height: 270px;
            overflow: hidden;
            position: relative;
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-thumb-wrap .post-thumb {
            height: 100%;
            width: 100%;
            -webkit-transition: all 0.3s ease-out 0s;
            -moz-transition: all 0.3s ease-out 0s;
            -ms-transition: all 0.3s ease-out 0s;
            -o-transition: all 0.3s ease-out 0s;
            transition: all 0.3s ease-out 0s;
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-thumb-wrap .post-date {
            position: absolute;
            left: 30px;
            bottom: 30px;
            height: 40px;
            /*width: 150px;*/
            padding: 0px 20px;
            width: auto;
            line-height: 40px;
            text-align: center;
            border-radius: 30px;
            z-index: 2;
            color: #fff;
            background-color: #333;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-desc .post-date {
            height: 40px;
            padding: 0px 20px;
            width: auto;
            line-height: 40px;
            text-align: center;
            border-radius: 30px;
            z-index: 2;
            color: #fff;
            background-color: #333;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-date i {
            margin-right: 10px;
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-desc {
            background-color: #f5f5f5;
            padding: 40px 30px;
            -webkit-transition: all 0.3s ease-out 0s;
            -moz-transition: all 0.3s ease-out 0s;
            -ms-transition: all 0.3s ease-out 0s;
            -o-transition: all 0.3s ease-out 0s;
            transition: all 0.3s ease-out 0s;
        }

        @media (max-width: 1199px) {
            .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-desc {
                padding: 35px 25px;
            }
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-desc .title {
            font-size: 26px;
            font-weight: 600;
            letter-spacing: -1px;
            margin-bottom: 20px;
            line-height: 33px;
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-desc .post-description {
            margin-bottom: 20px;
        }

        @media (max-width: 1199px) {
            .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-desc .title {
                font-size: 22px;
            }
        }

        @media (max-width: 767px) {
            .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-desc .title {
                font-size: 20px;
            }
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-desc .post-link {
            font-weight: 700;
            color: #333333;
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-desc .post-link i {
            margin-left: 10px;
            position: relative;
            top: 2px;
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item .post-desc .post-link:hover {
            color: #333;
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item:hover .post-thumb {
            transform: scale(1.1);
        }

        .posts-carousel-{{ $widgeteable->public_id }} .posts-carousel-item:hover .post-desc {
            background-color: #fff;
            box-shadow: 0px 10px 30px 0px rgba(203, 203, 203, 0.3);
        }
    </style>
@endpush
@push('js')
    <script>
        $(function () {
            $('.widget-module-{{ $widgeteable->public_id }}').slick({
                infinite: true,
                slidesToShow: 4,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                speed: 500,
                arrows: false,
                fade: false,
                dots: false,
                swipe: true,
                nextArrow: '<div class="col-12 order-1"><button class="slick-arrow next-arrow"><i class="fa-solid fa-long-arrow-right"></i></button></div>',
                prevArrow: '<div class="col-12 order-2"><button class="slick-arrow prev-arrow"><i class="fa-solid fa-long-arrow-left"></i></button></div>',
                responsive: [{
                    breakpoint: 1600,
                    settings: {
                        slidesToShow: 3,
                    },
                },
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2,
                        },
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1,
                            centerMode: true,
                            centerPadding: '10%',
                        },
                    },
                    {
                        breakpoint: 400,
                        settings: {
                            slidesToShow: 1,
                            centerMode: false,
                        },
                    },
                ],
            });
        });
    </script>
@endpush
