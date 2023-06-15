@props([...config('frontend.components.props.pages.Posts.miniature'),'post' => new \Adminx\Common\Models\Post()])
<article {{ $attributes->merge(['id' => "post-item-{$post->id}"])->class([
        'post-item post-miniature post-miniature-one',
        'mb-5' => $big,
        'mb-4' => !$big,
        ]) }}>
    @if($showCover && $post->cover_url)
        <div class="post-cover img-hover-effect mb-3">
            <a class="img-zoom-effect" href="{{ $post->dynamic_uri }}">
                <img class="img-full img-fluid w-100" src="{{ $post->cover_url }}"
                     alt="{{ $post->title }}">
            </a>
        </div>
    @endif
    <div class="post-content {{ $showCover && $post->cover_url ? 'pt-6' : '' }}">
        <a @class([
        'post-title mt-3',
        'limit-lines' => !$big,
        'h2' => $big,
        'h5' => $small,
        'h4' => !$big && !$small
        ]) href="{{ $post->dynamic_uri }}"
           data-bs-toggle="tooltip"
           title="{{ $post->title }}" style="-webkit-line-clamp: {{ $titleLines }};">{{ $post->title }}</a>

        @if($showMeta && !$bottomMeta)
            <x-frontend::pages.Posts.shared.post-meta/>
        @endif

        @if($showDescription)
            <p class="post-description limit-lines mb-0"
               style="-webkit-line-clamp: {{ $descriptionLines }};">{!! $post->description ?? $post->limitContent($big ? 500 : 150) !!}</p>
        @endif

        @if($showMeta && $bottomMeta)
            {{--<x-frontend::pages.Posts.shared.post-meta :post="$post" :show-icons="$showIcons" />--}}
            <x-frontend::pages.Posts.shared.post-meta class="mt-2"/>
        @endif

        @if($showReadMore || $showCategories)
            <p @class([
        'button-wrap d-flex justify-content-between align-items-center mb-0 post-footer',
        'mt-5' => $big,
        'mt-4' => !$big,
])>
                @if($showReadMore)
                    <a class="btn btn-primary px-4 py-2 my-auto post-readmore-button" href="{{ $post->dynamic_uri }}">
                        {!! $readMoreText !!}
                    </a>
                @endif

                @if($showCategories)
                    <span class="ms-auto me-0  my-auto small post-categories" data-bs-toggle="tooltip"
                          data-bs-title="Categorias">
                        {!! $showIcons ? $categoriesIcon : '' !!}
                        {{ $post->categories->count() ? $post->categories->pluck('title')->implode(', ') : 'Nenhuma Categoria' }}
                    </span>
                @endif
            </p>
        @endif
    </div>
</article>
