@aware([
    'post' => new \Adminx\Common\Models\Post(),
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

<div {{ $attributes->merge(['id' => "post-meta-{$post->id}"])->class(['post-meta','mb-2','d-inline-row','ext-muted','small']) }}>
    {{--todo: link do autor--}}
    @if($showAuthor)
        <span class="me-3 mr-3 post-meta-author" title="Autor">
            {!! $showIcons ? $authorIcon : '' !!}
            {{ $post->user->name }}
        </span>
    @endif

    @if($showDate)
        <span class="me-3 mr-3 post-meta-date"
              title="Postado {{ $post->published_at->translatedFormat(config('location.formats.datetime.full')) }}">
            {!! $showIcons ? $dateIcon : '' !!}
            <time datetime="{{ $post->published_at->format('Y-m-d H:i:S') }}">
                {{ $dateHumanFormat ? $post->published_at->diffForHumans() : $post->published_at->format($dateFormat) }}
            </time>
        </span>
    @endif

    @if($showComments)
        <span class="post-meta-comments" title="Comentários">
            {!! $showIcons ? $commentsIcon : '' !!}
            {{ $post->comments()->count() }} {{ !$shortComments ? 'Comentários' : '' }}
        </span>
    @endif

    {{ $slot }}
</div>
