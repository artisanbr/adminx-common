@props([...config('frontend.components.props.pages.Articles.miniature'),'article' => new \Adminx\Common\Models\Article()])
<article {{ $attributes->merge(['id' => "article-item-{$article->id}"])->class([
        'article-item article-miniature article-miniature-one',
        'mb-5' => $big,
        'mb-4' => !$big,
        ]) }}>
    @if($showCover && $article->cover_url)
        <div @class([
                'article-cover img-hover-effect mb-3',
                'mt-3' => $showCover && $article->cover_url
                ])>
            <a class="img-zoom-effect" href="{{ $article->dynamic_uri }}">
                <img class="img-full img-fluid w-100" src="{{ $article->cover_url }}"
                     alt="{{ $article->title }}">
            </a>
        </div>
    @endif
    <div class="article-content">
        <a @class([
        'article-title mt-3',
        'limit-lines' => !$big,
        'h2' => $big,
        'h5' => $small,
        'h4' => !$big && !$small
        ]) href="{{ $article->dynamic_uri }}"
           data-bs-toggle="tooltip"
           title="{{ $article->title }}" style="-webkit-line-clamp: {{ $titleLines }};">{{ $article->title }}</a>

        @if($showMeta && !$bottomMeta)
            <x-frontend::pages.Articles.shared.article-meta/>
        @endif

        @if($showDescription)
            <p class="article-description limit-lines mb-0"
               style="-webkit-line-clamp: {{ $descriptionLines }};">{!! $article->description ?? $article->limitContent($big ? 500 : 150) !!}</p>
        @endif

        @if($showMeta && $bottomMeta)
            {{--<x-frontend::pages.Articles.shared.article-meta :article="$article" :show-icons="$showIcons" />--}}
            <x-frontend::pages.Articles.shared.article-meta class="mt-2"/>
        @endif

        @if($showReadMore || $showCategories)
            <p @class([
        'button-wrap d-flex justify-content-between align-items-center mb-0 article-footer',
        'mt-5' => $big,
        'mt-4' => !$big,
])>
                @if($showReadMore)
                    <a class="btn btn-primary px-4 py-2 my-auto article-readmore-button" href="{{ $article->dynamic_uri }}">
                        {!! $readMoreText !!}
                    </a>
                @endif

                @if($showCategories)
                    <span class="ms-auto me-0  my-auto small article-categories" data-bs-toggle="tooltip"
                          data-bs-title="Categorias">
                        {!! $showIcons ? $categoriesIcon : '' !!}
                        {{ $article->categories->count() ? $article->categories->pluck('title')->implode(', ') : 'Nenhuma Categoria' }}
                    </span>
                @endif
            </p>
        @endif
    </div>
</article>
