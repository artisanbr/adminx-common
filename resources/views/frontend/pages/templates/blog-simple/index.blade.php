<?php
/**
 * @var \Adminx\Common\Models\Site                                                  $site
 * @var \Adminx\Common\Models\Pages\Page                                            $page
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Adminx\Common\Models\Article[] $articles
 * @var string|null                                                                 $searchTerm
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
                        @include('pages-templates::blog-simple.shared.sidebar', compact('page', 'site', 'searchTerm'))
                    </div>
                    <div class="col-lg-8 order-lg-2 order-1">
                        <div class="blog-item-wrap row">
                            @if($articles->count())
                                <div class="col-12">
                                    <x-frontend::pages.Articles.article-miniature-1 :article="$articles->first()" big/>
                                    <hr class="mb-5"/>
                                </div>
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            @foreach($articles->getCollection()->forget(0)->filter(fn ($article, $i) => $i % 2 !== 0)->values() as $article)
                                                <div class="row">
                                                    <div class="col-12">
                                                        <x-frontend::pages.Articles.article-miniature-1
                                                                :article="$article"/>

                                                        @if(!$loop->last)
                                                            <hr class="mb-4"/>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-12 col-md-6">
                                            @foreach($articles->getCollection()->forget(0)->filter(fn ($article, $i) => $i % 2 === 0)->values() as $article)
                                                <div class="row">
                                                    <div class="col-12">
                                                        <x-frontend::pages.Articles.article-miniature-1
                                                                :article="$article"/>

                                                        @if(!$loop->last)
                                                            <hr class="mb-4"/>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="pagination-area pt-10">
                                        <nav aria-label="Paginação">
                                            {!! $articles->links('adminx-frontend::layout.inc.pagination') !!}
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
