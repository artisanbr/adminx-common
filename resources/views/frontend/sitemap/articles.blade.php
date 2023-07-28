<?php
/**
 * @var \Adminx\Common\Models\Site      $site
 * @var \Adminx\Common\Models\Article[] $articles
 */
?>
<?php
print '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<urlset
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

    @foreach($articles as $article)
        <url>
            <loc>{{ $article->uri }}</loc>
            <news:news>
                <news:publication>
                    <news:name>{{ $site->seoTitle($article->page->seoTitle()) }}</news:name>
                    <news:language>pt</news:language>
                </news:publication>
                <news:publication_date>{{ $article->published_at->toIso8601String() }}</news:publication_date>
                <news:title>{{ $article->title }}</news:title>
                <news:keywords>{{ $article->seo->keywords }}</news:keywords>
            </news:news>
            @if(!empty($article->seoImage()))
                <image:image>
                    <image:loc>{{ FrontendUtils::url($article->seoImage()) }}</image:loc>
                    {{--<image:title>{{ $article->seoTitle() }}</image:title>--}}
                </image:image>
            @endif


        </url>
    @endforeach
</urlset>
