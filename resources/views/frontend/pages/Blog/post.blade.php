<?php
/**
 * @var \Adminx\Common\Models\Pages\Page    $page
 * @var \Adminx\Common\Models\Post    $post
 * @var \Illuminate\Pagination\LengthAwarePaginator $comments
 * @var string[] $breadcrumbs
 */
?>
@extends('adminx-frontend::layout.partials.content')

@section('content')

    @if($showBreadcrumb)
        <x-frontend::breadcrumb :page="$page" :append="$breadcrumbs" :bg-image="$post->cover_url"/>
    @endif

    {!! $page->html !!}

    {{--Content--}}
    <div class="blog-area py-10">
        <div class="container">
            <div class="row g-8">
                {{--Sidebar--}}
                <div class="col-lg-4 order-lg-1 order-2 pt-10 pt-lg-0">
                    @include('adminx-frontend::pages.Blog.shared.sidebar', compact('page', 'site'))
                </div>

                {{--Post--}}
                <div class="col-lg-8 order-lg-2 order-1">
                    <article class="blog-post">
                        @if($post->cover_url)
                            <div class="blog-img img-hover-effect mb-3">
                                <img class="img-full img-fluid w-100 d-none" src="{{ $post->cover_url }}"
                                     alt="{{ $post->title }}">
                            </div>
                        @endif
                        <div class="blog-post-content {{ $post->cover_url ? 'pt-6' : '' }}">

                            @if(!$showBreadcrumb)
                                <h1 class="title">{{ $post->title }}</h1>
                            @endif


                            <div class="blog-post-meta mb-2 mt-3 d-inline-row text-muted small">
                                {{--todo: link do autor--}}
                                {{--<span class="me-3 mr-3" title="Autor">
                                        <i class="fa-solid fa-user me-1"></i>
                                        {{ $post->user->name }}
                                    </span>--}}
                                <span class="me-3 mr-3" data-toggle="tooltip"
                                      title="Postado {{ @$post->published_at->translatedFormat(config('location.formats.datetime.full')) }}">
                                        <i class="fa-solid fa-calendar-days me-1"></i>
                                        <time
                                                datetime="{{ $post->published_at->format('Y-m-d H:i:S') }}">{{ $post->published_at->diffForHumans() }}</time>
                                    </span>
                                <span title="Comentários">
                                        <i class="fa-solid fa-comments me-1"></i>
                                        {{ $post->comments()->count() }} Coment&aacute;rios
                                    </span>
                            </div>
                            @if($post->categories->count())
                                <p class="text-muted">
                                    Categorias: {{ $post->categories->pluck('title')->join(', ') }}
                                </p>
                            @endif
                            @if($post->description ?? false)
                            <p class="short-desc mb-5 pb-5">
                                {!! $post->description !!}
                            </p>
                            @endif

                            {!! $post->content !!}

                            {{--<div class="meta-wtih-social py-2 px-3">
                                <div class="social-link">
                                    <ul>
                                        <li>
                                            <a href="#" data-tippy="Facebook" data-tippy-inertia="true" data-tippy-animation="shift-away" data-tippy-delay="50" data-tippy-arrow="true" data-tippy-theme="sharpborder">
                                                <i class="fa fa-facebook"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" data-tippy="Dribbble" data-tippy-inertia="true" data-tippy-animation="shift-away" data-tippy-delay="50" data-tippy-arrow="true" data-tippy-theme="sharpborder">
                                                <i class="fa fa-dribbble"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" data-tippy="Pinterest" data-tippy-inertia="true" data-tippy-animation="shift-away" data-tippy-delay="50" data-tippy-arrow="true" data-tippy-theme="sharpborder">
                                                <i class="fa fa-pinterest-p"></i>
                                            </a>
                                        </li>
                                        <li class="comment">
                                            <a href="#">
                                                <span>2</span>
                                                <i class="fa fa-comments"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>--}}

                            <hr class="mt-5 mb-0"/>

                            {{--Prev/Next--}}
                            <div class="page-navigation row py-5">
                                <div class="prev-nav col-12 col-md-6 d-inline-flex align-items-center mb-5 mb-md-0">
                                    @if($post->previous)
                                        <div class="navigation-img  me-2 mr-2">
                                            <a href="{{ $post->previous->uri }}"
                                               class="btn btn-primary rounded-0 p-4 text-white">
                                                <i class="fa-solid fa-angle-left font-size-25 fw-bolder"></i>
                                            </a>
                                        </div>

                                        <div class="navigation-content w-100 ps-xl-4 pt-4 pt-xl-0">
                                            <div class="blog-post-meta mb-2 d-inline-row text-muted small">
                                                    {{--<span class="me-3 mr-3" title="Autor">
                                                        <i class="fa-solid fa-user me-1"></i>
                                                        {{ $post->previous->user->name }}
                                                    </span>--}}
                                                <span class="me-3 mr-3"
                                                      title="Postado {{ @$post->previous->published_at->translatedFormat(config('location.formats.datetime.full')) }}">
                                                        <i class="fa-solid fa-calendar-days me-1"></i>
                                                        <time
                                                            datetime="{{ $post->previous->published_at->format('Y-m-d H:i:S') }}">{{ $post->previous->published_at->diffForHumans() }}</time>
                                                    </span>
                                                <span title="Comentários">
                                                        <i class="fa-solid fa-comments me-1"></i>
                                                        {{ $post->previous->comments()->count() }}
                                                    </span>
                                            </div>
                                            <a href="{{ $post->previous->uri }}"
                                               class="h5 limit-1-line mb-0">{{ $post->previous->title }}</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="next-nav col-12 col-md d-inline-flex align-items-center">
                                    @if($post->next)
                                        <div
                                            class="navigation-content w-100 text-end text-right pe-xl-4 pb-4 pb-xl-0">
                                            <div class="blog-post-meta mb-2 d-inline-row text-muted small">
                                                    {{--<span class="me-3 mr-3" title="Autor">
                                                        <i class="fa-solid fa-user me-1"></i>
                                                        {{ $post->next->user->name }}
                                                    </span>--}}
                                                <span class="me-3 mr-3"
                                                      title="Postado {{ @$post->next->published_at->translatedFormat(config('location.formats.datetime.full')) }}">
                                                        <i class="fa-solid fa-calendar-days me-1"></i>
                                                        <time
                                                            datetime="{{ $post->next->published_at->format('Y-m-d H:i:S') }}">{{ $post->next->published_at->diffForHumans() }}</time>
                                                    </span>
                                                <span title="Comentários">
                                                        <i class="fa-solid fa-comments me-1"></i>
                                                        {{ $post->next->comments()->count() }}
                                                    </span>
                                            </div>
                                            <a href="{{ $post->next->uri }}"
                                               class="h5 limit-1-line mb-0">{{ $post->next->title }}</a>
                                        </div>
                                        <div class="navigation-img justify-content-end ms-2 ml-2">
                                            <a href="{{ $post->next->uri }}"
                                               class="btn btn-primary rounded-0 p-4 text-white">
                                                <i class="fa-solid fa-angle-right font-size-25 fw-bolder"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{--Comments--}}
                            <div id="post-comments" class="blog-comment with-bg mt-10 mb-5">
                                <h4 class="heading mb-7">{{ $post->comments()->count() }} Comentários</h4>
                                @if(!$post->comments()->count())
                                    <p>Seja o primeiro a comentar!</p>
                                @endif

                                @foreach($comments as $comment)
                                    <div class="blog-comment-item d-inline-flex mb-8">
                                        <div class="blog-comment-img ms-0 me-3 ml-0 mr-3">
                                            <img src="{{ $comment->gravatar }}"
                                                 alt="{{ $comment->name }} Gravatar">
                                        </div>
                                        <div class="blog-comment-content ms-0 me-0 ml-0 mr-0 w-auto pb-5">
                                            <div class="user-meta">
                                                <span><strong>{{ $comment->name }} -</strong> {{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="user-comment mb-4">
                                                {{ $comment->comment }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                {{ $comments->links('adminx-frontend::layout.inc.pagination') }}
                            </div>

                            <div id="comment-form" class="feedback-area with-bg mt-10 mb-5 pb-5">
                                <h4 class="heading mb-1">Deixar Comentário</h4>
                                @push('js')
                                    {!! JsValidator::formRequest(\App\Http\Requests\StoreCommentFrontendRequest::class, "#form-comment")->render() !!}
                                @endpush
                                <form id="form-comment" class="feedback-form pt-8"
                                      action="{{ route('frontend.send-comment', ['post', $post->public_id]) }}"
                                      method="POST" data-grecaptcha-action="comment">
                                    @method('POST')
                                    @csrf
                                    <x-common::recaptcha :site="$page->site" no-ajax/>
                                    <div class="row">
                                        <div class="col-12">
                                            <x-frontend::alert color="info" class="mb-5 fade collapse" no-close>
                                                Aguarde....
                                            </x-frontend::alert>
                                        </div>

                                        @if ($errors->any())
                                            @foreach ($errors->all() as $error)
                                                <div class="col-12">
                                                    <x-frontend::alert color="danger" no-close>
                                                        {!! $error !!}
                                                    </x-frontend::alert>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <div class="form-field d-flex flex-column">
                                                <input type="text" name="name" id="name" placeholder="Nome*"
                                                       class="form-control rounded-0 input-field"/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <div class="form-field d-flex flex-column">
                                                <input type="text" name="email" id="email" placeholder="Email*"
                                                       class="form-control rounded-0 input-field"/>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="form-field d-flex flex-column mt-6">
                                                    <textarea name="comment" id="comment" placeholder="Comentário"
                                                              class="form-control rounded-0 textarea-field"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="button-wrap mt-8">
                                        <button type="submit" value="submit"
                                                class="btn btn-custom-size lg-size btn-primary"
                                                data-bg-toggle="collapse" data-bs-target="#form-comment > .alert">
                                            Comentar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
@endsection
