<?php
/**
 * @var \Adminx\Common\Models\Site $site
 * @var \Adminx\Common\Models\Post[] $posts
 */
?>
<?php print '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
    xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

    @foreach($posts as $post)
        <url>
            <loc>{{ $post->uri }}</loc>
            <news:news>
                <news:publication>
                    <news:name>{{ $site->seoTitle($post->page->seoTitle()) }}</news:name>
                    <news:language>pt</news:language>
                </news:publication>
                <news:publication_date>{{ $post->published_at->toIso8601String() }}</news:publication_date>
                <news:title>{{ $post->title }}</news:title>
                <news:keywords>{{ $post->seo->keywords }}</news:keywords>
            </news:news>
            @if(!empty($post->seoImage()))
                <image:image>
                    <image:loc>{{ FrontendUtils::url($post->seoImage()) }}</image:loc>
                    {{--<image:title>{{ $post->seoTitle() }}</image:title>--}}
                </image:image>
            @endif


        </url>
    @endforeach
</urlset>
