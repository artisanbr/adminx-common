<?php
/**
 * @var \ArtisanBR\Adminx\Common\App\Models\Site $site
 * @var \ArtisanBR\Adminx\Common\App\Models\Page $page
 */
?>
<div class="card border-light mb-5 blog-widget-last-posts">
    <h5 class="card-header border-light mb-0">Postagens Recentes</h5>
    <div class="card-content">
        <div class="card-body">
            @foreach($page->posts as $post)
                <x-frontend::pages.Posts.post-miniature-1 :post="$post" class="sidebar-widget-last-posts-item" :show-read-more="false" :show-categories="false" :show-description="false" :show-cover="false" bottom-meta small />

                @if(!$loop->last)
                    <hr class="mb-4"/>
                @endif
            @endforeach
        </div>
    </div>
</div>
