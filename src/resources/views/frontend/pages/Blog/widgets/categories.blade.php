<?php
/**
 * @var \ArtisanBR\Adminx\Common\App\Models\Site $site
 * @var \ArtisanBR\Adminx\Common\App\Models\Page $page
 */
?>
@if($page->categories->count())
    <div class="card border-light mb-5 blog-widget-categories mb-9">
        <h5 class="card-header border-light mb-0">Categorias</h5>
        <div class="card-content">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($page->categories as $category)
                        <li class="list-group-item">
                            <a href="{{ $category->urlFrom($page) }}">
                                {{ $category->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif