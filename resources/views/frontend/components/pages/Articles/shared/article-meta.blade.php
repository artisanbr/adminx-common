@aware([
    'article' => new \Adminx\Common\Models\Article(),
    'showAuthor' => false,
    'showDate' => true,
    'showComments' => false,
    'showIcons' => true,

    'dateHumanFormat' => true,
    'dateFormat' => 'd/m/Y H:i:s',
    'shortComments' => true,

    'authorIcon' => '<i class="fa-solid fa-user me-1"></i>',
    'dateIcon' => '<i class="fa-solid fa-calendar-days me-1"></i>',
    'commentsIcon' => '<i class="fa-solid fa-comments me-1"></i>',
])

<div {{ $attributes->merge(['id' => "article-meta-{$article->id}"])->class(['article-meta','mb-2','d-inline-row','ext-muted','small']) }}>
    {{--todo: link do autor--}}
    @if($showAuthor)
        <span class="me-3 mr-3 article-meta-author" title="Autor">
            {!! $showIcons ? $authorIcon : '' !!}
            {{ $article->user->name }}
        </span>
    @endif

    @if($showDate)
        <span class="me-3 mr-3 article-meta-date"
              title="Postado {{ @$article->published_at->translatedFormat(config('location.formats.datetime.full')) }}">
            {!! $showIcons ? $dateIcon : '' !!}
            <time datetime="{{ $article->published_at->format('Y-m-d H:i:S') }}">
                {{ $dateHumanFormat ? $article->published_at->diffForHumans() : $article->published_at->format($dateFormat) }}
            </time>
        </span>
    @endif

    @if($showComments)
        <span class="article-meta-comments" title="Comentários">
            {!! $showIcons ? $commentsIcon : '' !!}
            {{ $article->comments()->count() }} {{ !$shortComments ? 'Comentários' : '' }}
        </span>
    @endif

    {{ $slot }}
</div>
