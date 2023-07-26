<?php
/**
 * @var \Adminx\Common\Models\Site $site
 * @var \Adminx\Common\Models\Pages\Page $page
 */
?>
<div class="card border-light mb-5 blog-widget-last-articles">
    <h5 class="card-header border-light mb-0">Postagens Recentes</h5>
    <div class="card-content">
        <div class="card-body">
            @foreach($page->articles as $article)
                <x-frontend::pages.Articles.article-miniature-1 :article="$article" class="sidebar-widget-last-articles-item" :show-read-more="false" :show-categories="false" :show-description="false" :show-cover="false" bottom-meta small />

                @if(!$loop->last)
                    <hr class="mb-4"/>
                @endif
            @endforeach
        </div>
    </div>
</div>
