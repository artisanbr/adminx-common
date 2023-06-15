@props([...config('frontend.components.props.pages.Posts.miniature'),'post' => new \Adminx\Common\Models\Post()])
<article {{ $attributes->merge(['id' => "post-item-{$post->id}"])->class([
        'post-item post-miniature post-miniature-two',
        'mb-5' => $big,
        'mb-4' => !$big,
        ]) }}>
    @if($showCover && $post->cover_url)
        <div class="post-thumb-wrap">
            <div class="post-thumb bg-img-c"
                 style="background-image: url('{{ $post->cover_url }}');">
            </div>

            @if($showDate)
                <time datetime="{{ $post->published_at->format('Y-m-d H:i:S') }}" class="post-date">
                    <i class="far fa-calendar-alt"></i>
                    {{ $dateHumanFormat ? $post->published_at->diffForHumans() : $post->published_at->format($dateFormat) }}
                </time>
            @endif
        </div>
    @endif
    <div class="post-desc">
        <h3 class="title">
            <a href="{{ $post->dynamic_uri }}" class="limit-lines" style="-webkit-line-clamp: {{ $titleLines }};">
                {{ $post->title }}
            </a>
        </h3>
        @if($showDescription)
            <p class="post-description limit-lines"
               style="-webkit-line-clamp: {{ $descriptionLines }};">{!! $post->description ?? $post->limitContent($big ? 500 : 150) !!}</p>
        @endif

        <div class="d-flex align-items-center justify-content-between">
            @if($showReadMore)
                <a href="{{ $post->dynamic_uri }}" class="post-link">
                    {!! $readMoreText !!} <i class="fa-solid fa-long-arrow-right"></i>
                </a>
            @endif
            @if((!$showCover || !$post->cover_url) && $showDate)
                <time datetime="{{ $post->published_at->format('Y-m-d H:i:S') }}"
                      class="post-date d-inline-flex align-items-center">
                    <i class="far fa-calendar-alt"></i> {{ $dateHumanFormat ? $post->published_at->diffForHumans() : $post->published_at->format($dateFormat) }}
                </time>
            @endif
        </div>

    </div>
</article>


