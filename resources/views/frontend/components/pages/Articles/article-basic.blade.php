@props([
    'article' => new \Adminx\Common\Models\Article(),
    'big' => false,
])
<article @class([
        'blog-article' ,
        ])>
    @if($article->cover_url)
        <div class="blog-article-img img-hover-effect mb-3">
            <a class="img-zoom-effect" href="{{ $article->dynamic_uri }}">
                <img class="img-full img-fluid w-100" src="{{ $article->cover_url }}"
                     alt="{{ $article->title }}">
            </a>
        </div>
    @endif
    <div class="blog-article-content {{ $article->cover_url ? 'pt-6' : '' }}">

        <div class="blog-article-meta mb-2 d-inline-row text-muted small">
            {{--todo: link do autor--}}
            {{--<span class="me-3 mr-3" title="Autor">
                                        <i class="fa-solid fa-user me-1"></i>
                                        {{ $article->user->name }}
                                    </span>--}}
            <span class="me-3 mr-3"
                  title="Postado {{ $article->published_at->translatedFormat(config('location.formats.datetime.full')) }}">
                                        <i class="fa-solid fa-calendar-days me-1"></i>
                                        <time
                                                datetime="{{ $article->published_at->format('Y-m-d H:i:S') }}">{{ $article->published_at->diffForHumans() }}</time>
                                    </span>
            <span title="Comentários">
                                        <i class="fa-solid fa-comments me-1"></i>
                                        {{ $article->comments()->count() }} Coment&aacute;rios
                                    </span>
        </div>
        <h2 class="mb-3">
            <a class="{{ $big ? 'h2' : 'h4 limit-2-lines' }}" href="{{ $article->dynamic_uri }}" data-bs-toggle="tooltip"
               data-bs-title="{{ $article->title }}">{{ $article->title }}</a>
        </h2>
        <p class="short-desc mb-5">{!! $article->description !!}</p>
        <p class="button-wrap d-flex justify-content-between align-items-center mb-0">
            <a class="btn btn-primary px-4 py-2 my-auto btn-article-readmore" href="{{ $article->dynamic_uri }}">Ler Mais</a>
            <span class="ms-auto me-0  my-auto small" data-bs-toggle="tooltip" data-bs-title="Categorias">
                <i class="fa-solid fa-tags"></i> {{ $article->categories->count() ? $article->categories->pluck('title')->implode(', ') : 'Nenhuma Categoria' }}
            </span>
        </p>
    </div>
</article>
<hr class="my-4"/>
