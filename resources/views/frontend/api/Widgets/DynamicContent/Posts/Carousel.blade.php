<?php
/***
 * @var \Adminx\Common\Models\Widgets\SiteWidget                                         $widget
 * @var \Adminx\Common\Models\Pages\Page                                         $page
 * @var \Adminx\Common\Models\Article                                            $article
 * @var \Adminx\Common\Models\Article[]|\Illuminate\Database\Eloquent\Collection $articles
 */
?>
{{--@extends('common::layouts.api.ajax-view')--}}

<html lang="pt-BR">
<head>
    @if($articles->count())
        <style>


            .articles-carousel-{{ $widget->public_id }} .articles-carousel-item {
                margin-bottom: 30px;
            }

            .articles-carousel-{{ $widget->public_id }} .articles-carousel-item .article-thumb-wrap {
                width: 100%;
                height: 270px;
                overflow: hidden;
                position: relative;
            }

            .articles-carousel-{{ $widget->public_id }} .articles-carousel-item .article-thumb-wrap .article-thumb {
                height: 100%;
                width: 100%;
                -webkit-transition: all 0.3s ease-out 0s;
                -moz-transition: all 0.3s ease-out 0s;
                -ms-transition: all 0.3s ease-out 0s;
                -o-transition: all 0.3s ease-out 0s;
                transition: all 0.3s ease-out 0s;
            }

            .articles-carousel-{{ $widget->public_id }} .articles-carousel-item .article-thumb-wrap .article-date {
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

            .articles-carousel-{{ $widget->public_id }} .articles-carousel-item .article-desc .article-date {
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

            .articles-carousel-{{ $widget->public_id }} .articles-carousel-item .article-date i {
                margin-right: 10px;
            }

            .articles-carousel-{{ $widget->public_id }} .articles-carousel-item .article-desc {
                background-color: #f5f5f5;
                padding: 40px 30px;
                -webkit-transition: all 0.3s ease-out 0s;
                -moz-transition: all 0.3s ease-out 0s;
                -ms-transition: all 0.3s ease-out 0s;
                -o-transition: all 0.3s ease-out 0s;
                transition: all 0.3s ease-out 0s;
            }

            @media (max-width: 1199px) {
                .articles-carousel-{{ $widget->public_id }} .articles-carousel-item .article-desc {
                    padding: 35px 25px;
                }
            }

            .articles-carousel-{{ $widget->public_id }} .articles-carousel-item .article-desc .title {
                font-size: 26px;
                font-weight: 600;
                letter-spacing: -1px;
                margin-bottom: 20px;
                line-height: 33px;
            }

            .articles-carousel-{{ $widget->public_id }} .articles-carousel-item .article-desc .article-description {
                margin-bottom: 20px;
            }

            @media (max-width: 1199px) {
                .posts-carousel-{{ $widget->public_id }} .posts-carousel-item .article-desc .title {
                    font-size: 22px;
                }
            }

            @media (max-width: 767px) {
                .posts-carousel-{{ $widget->public_id }} .posts-carousel-item .article-desc .title {
                    font-size: 20px;
                }
            }

            .posts-carousel-{{ $widget->public_id }} .posts-carousel-item .article-desc .article-link {
                font-weight: 700;
                color: #333333;
            }

            .posts-carousel-{{ $widget->public_id }} .posts-carousel-item .article-desc .article-link i {
                margin-left: 10px;
                position: relative;
                top: 2px;
            }

            .posts-carousel-{{ $widget->public_id }} .posts-carousel-item .article-desc .article-link:hover {
                color: #333;
            }

            .posts-carousel-{{ $widget->public_id }} .posts-carousel-item:hover .article-thumb {
                transform: scale(1.1);
            }

            .posts-carousel-{{ $widget->public_id }} .posts-carousel-item:hover .article-desc {
                background-color: #fff;
                box-shadow: 0px 10px 30px 0px rgba(203, 203, 203, 0.3);
            }
        </style>
    @endif
</head>
<body>
@if($articles->count())
    <div class="row articles-carousel articles-carousel-{{ $widget->public_id }} widget-module widget-module-{{ $widget->public_id }}">
        {{--Left--}}
        @foreach($articles as $article)
            <div class="col-lg-3 col-md-3 articles-carousel-left">
                <x-frontend::pages.Articles.article-miniature-2 :article="$article"
                                                          :show-description="$variables['show_description'] ?? false"
                                                          class="articles-carousel-item widget-article-item mb-30"
                                                          :show-read-more="$variables['show_read_more'] ?? false"
                                                          :read-more-text="$variables['read_more_text'] ?? 'Leia Mais'"
                                                          :show-categories="$variables['show_categories'] ?? false"
                                                          :title-lines="$variables['title_lines'] ?? 1"
                                                          :show-date="$variables['show_date'] ?? false"
                                                          :date-format="$variables['date_format'] ?? false"/>
            </div>
        @endforeach

    </div>
    <script>
        $(function () {
            $('.widget-module-{{ $widget->public_id }}').slick({
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
@endif
</body>
</html>
{{--@push('css')
@endpush
@push('js')
@endpush--}}
