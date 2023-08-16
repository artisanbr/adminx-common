@props([...config('frontend.components.props.pages.Articles.miniature'),'article' => new \Adminx\Common\Models\Article()])
<article {{ $attributes->merge(['id' => "article-item-{$article->id}"])->class([
        'article-item article-miniature article-miniature-two',
        'mb-5' => $big,
        'mb-4' => !$big,
        ]) }}>
    @if($showCover && $article->cover_url)
        <div class="article-thumb-wrap">
            <div class="article-thumb bg-img-c"
                 style="background-image: url('{{ $article->cover_url }}');">
            </div>

            @if($showDate)
                <time datetime="{{ $article->published_at->format('Y-m-d H:i:S') }}" class="article-date">
                    <i class="far fa-calendar-alt"></i>
                    {{ $dateHumanFormat ? $article->published_at->diffForHumans() : $article->published_at->format($dateFormat) }}
                </time>
            @endif
        </div>
    @endif
    <div class="article-desc">
        <h3 class="title">
            <a href="{{ $article->url }}" class="limit-lines" style="-webkit-line-clamp: {{ $titleLines }};">
                {{ $article->title }}
            </a>
        </h3>
        @if($showDescription)
            <p class="article-description limit-lines"
               style="-webkit-line-clamp: {{ $descriptionLines }};">{!! $article->description ?? $article->limitContent($big ? 500 : 150) !!}</p>
        @endif

        <div class="d-flex align-items-center justify-content-between">
            @if($showReadMore)
                <a href="{{ $article->url }}" class="article-link">
                    {!! $readMoreText !!} <i class="fa-solid fa-long-arrow-right"></i>
                </a>
            @endif
            @if((!$showCover || !$article->cover_url) && $showDate)
                <time datetime="{{ $article->published_at->format('Y-m-d H:i:S') }}"
                      class="article-date d-inline-flex align-items-center">
                    <i class="far fa-calendar-alt"></i> {{ $dateHumanFormat ? $article->published_at->diffForHumans() : $article->published_at->format($dateFormat) }}
                </time>
            @endif
        </div>

    </div>
</article>


