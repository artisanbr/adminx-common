<?php
/***
 * @var \Adminx\Common\Models\SiteWidget                                      $widgeteable
 * @var \Adminx\Common\Models\Pages\Page                                      $page
 * @var \Adminx\Common\Models\Post                                            $post
 * @var \Adminx\Common\Models\Post[]|\Illuminate\Database\Eloquent\Collection $posts
 */

$postsQuery = $page->posts()->published();
?>
@extends('adminx-common::layouts.api.ajax-view')

@if($posts->count())
    <div class="row posts-mosaic posts-mosaic-{{ $widgeteable->public_id }} widget-module widget-module-{{ $widgeteable->public_id }}">
        {{--Left--}}
        {{--@foreach($page->posts()->published()->take(2)->get() as $post)--}}
        @foreach($postsQuery->take(2)->get() as $post)
            <div class="col-xl-4 col-lg-6 col-md-6 posts-mosaic-left">
                <x-frontend::pages.Posts.post-miniature-1 :post="$post"
                                                          class="posts-mosaic-item posts-mosaic-featured-item widget-post-featured-item mb-30"
                                                          :show-read-more="false" :show-categories="false"
                                                          bottom-meta show-comments/>
            </div>
        @endforeach

        {{--Right--}}
        <div class="col-xl-4 col-lg-12 col-md-12 posts-mosaic-right">
            <div class="widget-post-list mb-30">
                @foreach($postsQuery->skip(2)->take(3)->get() as $post)
                    <x-frontend::pages.Posts.post-miniature-1 :post="$post"
                                                              class="posts-mosaic-item posts-mosaic-list-item widget-post-list-item"
                                                              :show-read-more="false" :show-categories="false"
                                                              :show-description="false" :show-cover="false"
                                                              bottom-meta/>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{--<script>
    alert('carregou');
</script>--}}
