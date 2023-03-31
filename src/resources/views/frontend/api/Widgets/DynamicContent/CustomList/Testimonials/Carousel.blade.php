<?php
/***
 * @var \ArtisanBR\Adminx\Common\App\Models\Widgeteable                                            $widgeteable
 * @var \ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListTestimonials                     $customList
 * @var \ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItemTestimonials $listItem
 */
?>
@extends('adminx-common::layouts.api.ajax-view')

@php
    $customList = \ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomList::findAndMount($widgeteable->source_ids->first());
@endphp
@if($customList->items()->count())
    <div class="testimonials-carousel testimonials-carousel-{{ $widgeteable->public_id }} widget-module widget-module-{{ $widgeteable->public_id }}">
        {{--Left--}}
        @foreach($customList->items()->take(10)->get() as $listItem)
            <div class="col-xl-12 testimonials-item">
                <div class="testimonials-box-2">
                    {!! $listItem->data->content !!}
                    <div class="testimonials-author d-flex align-items-center mt-30">
                        <div class="testimonials-author-image">
                            <img class="h-auto" src="{{ $listItem->data->image->file->url }}"/>
                        </div>
                        <div class="testimonials-author-name">
                            <h4 class="limit-1-line">{{ $listItem->title }}</h4>
                            <span>{{ $listItem->created_at }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endif
@push('css')
    <style>
        .testimonials-author-image {
            width: 20%;
        }

        .testimonials-author-image img {
            padding-right: 25px;
        }

        .testimonials-author-name {
            width: 80%
        }
    </style>
@endpush
@push('js')
    <script>
        $(function () {

            $('.widget-module-{{ $widgeteable->public_id }}').slick({
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

            });
        });
    </script>
@endpush
