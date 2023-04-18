@props([
    'post' => new \Adminx\Common\Models\Post(),
    'big' => false,
])
<article @class([
        'blog-post' ,
        ])>
    @if($post->cover)
        <div class="blog-post-img img-hover-effect mb-3">
            <a class="img-zoom-effect" href="{{ $post->dynamic_uri }}">
                <img class="img-full img-fluid w-100" src="{{ $post->cover->uri }}"
                     alt="{{ $post->title }}">
            </a>
        </div>
    @endif
    <div class="blog-post-content {{ $post->cover ? 'pt-6' : '' }}">

        <div class="blog-post-meta mb-2 d-inline-row text-muted small">
            {{--todo: link do autor--}}
            <span class="me-3 mr-3" title="Autor">
                                        <i class="fa-solid fa-user me-1"></i>
                                        {{ $post->user->name }}
                                    </span>
            <span class="me-3 mr-3"
                  title="Postado {{ $post->published_at->translatedFormat(config('location.formats.datetime.full')) }}">
                                        <i class="fa-solid fa-calendar-days me-1"></i>
                                        <time
                                            datetime="{{ $post->published_at->format('Y-m-d H:i:S') }}">{{ $post->published_at->diffForHumans() }}</time>
                                    </span>
            <span title="Comentários">
                                        <i class="fa-solid fa-comments me-1"></i>
                                        {{ $post->comments()->count() }} Coment&aacute;rios
                                    </span>
        </div>
        <h2 class="mb-3">
            <a class="{{ $big ? 'h2' : 'h4 limit-2-lines' }}" href="{{ $post->dynamic_uri }}" data-bs-toggle="tooltip"
               data-bs-title="{{ $post->title }}">{{ $post->title }}</a>
        </h2>
        <p class="short-desc mb-5">{!! $post->description !!}</p>
        <p class="button-wrap d-flex justify-content-between align-items-center mb-0">
            <a class="btn btn-primary px-4 py-2 my-auto btn-post-readmore" href="{{ $post->dynamic_uri }}">Ler Mais</a>
            <span class="ms-auto me-0  my-auto small" data-bs-toggle="tooltip" data-bs-title="Categorias">
                <i class="fa-solid fa-tags"></i> {{ $post->categories->count() ? $post->categories->pluck('title')->implode(', ') : 'Nenhuma Categoria' }}
            </span>
        </p>
    </div>
</article>
<hr class="my-4"/>
