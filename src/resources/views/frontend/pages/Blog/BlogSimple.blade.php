<?php
/**
 * @var \ArtisanBR\Adminx\Common\App\Models\Site                                               $site
 * @var \ArtisanBR\Adminx\Common\App\Models\Page                                               $page
 * @var \Illuminate\Pagination\LengthAwarePaginator|\ArtisanBR\Adminx\Common\App\Models\Post[] $posts
 * @var string|null                                                                            $searchTerm
 */
?>

@extends('adminx-frontend::layout.partials.content', compact('page'))

@section('content')
    <main class="main-content">

        @if(!$page->is_home && ($page->config->breadcrumb ? $page->config->breadcrumb->enable : $site->theme->config->breadcrumb->enable))
            <x-frontend::breadcrumb :page="$page"/>
        @endif

        {!! $page->html !!}

        {{--Content--}}
        <div class="blog-area py-10">
            <div class="container">
                <div class="row g-8">
                    <div class="col-lg-4 order-lg-1 order-2 pt-10 pt-lg-0">
                        @include('adminx-frontend::pages.Blog.shared.sidebar', compact('page', 'site', 'searchTerm'))
                    </div>
                    <div class="col-lg-8 order-lg-2 order-1">
                        <div class="blog-item-wrap row">
                            @if($posts->count())
                                <div class="col-12">
                                    <x-frontend::pages.Posts.post-miniature-1 :post="$posts->first()" big/>
                                </div>
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            @foreach($posts->getCollection()->forget(0)->chunk(2) as $chunk)

                                                @foreach($chunk as $post)
                                                    @if($loop->odd)
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <x-frontend::pages.Posts.post-miniature-1
                                                                    :post="$post"/>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach

                                            @endforeach
                                        </div>
                                        <div class="col-12 col-md-6">
                                            @foreach($posts->getCollection()->forget(0)->chunk(2) as $chunk)

                                                @foreach($chunk as $post)
                                                    @if($loop->even)
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <x-frontend::pages.Posts.post-miniature-1
                                                                    :post="$post"/>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach

                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="pagination-area pt-10">
                                        <nav aria-label="Paginação">
                                            {!! $posts->links('adminx-frontend::layout.inc.pagination') !!}
                                        </nav>
                                    </div>
                                </div>
                            @else
                                <div class="col-12">
                                    <h1>Nenhuma Postagem Encontrada</h1>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection