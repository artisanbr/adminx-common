<?php
/**
 * @var \Adminx\Common\Models\Site $site
 * @var \Adminx\Common\Models\Pages\Page $page
 */
?>
@if($page->tags->count())
<div class="widgets-area">
    <h2 class="widgets-title mb-5">Tags</h2>
    <div class="widgets-item">
        <ul class="widgets-tags">
            @foreach($page->tags as $tag)
                <li>
                    <a href="{{ $tag->uriFrom($page) }}">{{ $tag->title }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
