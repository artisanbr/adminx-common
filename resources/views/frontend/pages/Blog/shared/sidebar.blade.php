<?php
/**
 * @var \Adminx\Common\Models\Site $site
 * @var \Adminx\Common\Models\Page $page
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Adminx\Common\Models\Post[] $posts
 * @var string|null $searchTerm
 */
?>
<div class="blog-sidebar-area sidebar-area mb-5 pb-5">
    <div class="card border-light mb-5 blog-widget-searchbox widgets-searchbox widgets-area">
        <div class="card-body">
            {{--Todo: busca no blog--}}
            <form id="blog-widget-searchbox-form" method="get" action="{{ $page->uri }}">
                <div class="input-group input-group-lg mb-0">
                    <input type="text" name="q" class="form-control" placeholder="Pesquisar" aria-label="Pesquisar" aria-describedby="blog-widget-searchbox-submit">
                    <button class="btn btn-outline-primary" type="submit" id="blog-widget-searchbox-submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @include('adminx-frontend::pages.Blog.widgets.last-posts', compact('page', 'site'))
    @include('adminx-frontend::pages.Blog.widgets.categories', compact('page', 'site'))
    {{--todo--}}
    {{--@include('adminx-frontend::pages.Blog.widgets.archive', compact('page', 'site'))--}}
    {{--@include('adminx-frontend::pages.Blog.widgets.subscribe', compact('page', 'site'))--}}
    {{--@include('adminx-frontend::pages.Blog.widgets.tags', compact('page', 'site'))--}}
</div>
