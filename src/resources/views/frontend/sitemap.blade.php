<?php
/**
 * @var \ArtisanBR\Adminx\Common\App\Models\Site $site
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

    {{--<url>
        <loc>{{ $site->uri }}</loc>
        <changefreq>weekly</changefreq>
        <lastmod>2022-09-25T14:06:34+00:00</lastmod>
    </url>--}}

    {{--Pages--}}
    @foreach($site->pages as $page)
        <url>
            <loc>{{ $page->uri }}</loc>
            <changefreq>weekly</changefreq>
            <lastmod>{{ $page->updated_at->toIso8601String() }}</lastmod>
        </url>

        @if($page->using_posts && $page->posts()->count())
            @foreach($page->posts()->take(100)->get() as $post)
                <url>
                    <loc>{{ $post->uri }}</loc>
                    <news:news>
                        <news:publication>
                            <news:name>{{ $page->seoTitle() }}</news:name>
                            <news:language>pt</news:language>
                        </news:publication>
                        <news:publication_date>{{ $post->published_at->toIso8601String() }}</news:publication_date>
                        <news:title>{{ $post->title }}</news:title>
                        <news:keywords>{{ $post->seo->keywords }}</news:keywords>
                    </news:news>
                    @if($post->seo->image ?? false)
                        <image:image>
                            <image:loc>{{ $post->seoImage() }}</image:loc>
                            <image:title>{{ $post->seoTitle() }}</image:title>
                        </image:image>
                    @endif
                </url>
            @endforeach
        @endif
    @endforeach



    {{--@foreach(\ArtisanBR\Adminx\Common\App\Models\Artigo::orderBy("updated_at", "asc")->take(20)->get() as $artigo)
        <url>
            <loc>{{ $artigo->full_url }}</loc>
            <news:news>
                <news:publication>
                    <news:name>Artigos Use Digital</news:name>
                    <news:language>pt</news:language>
                </news:publication>
                <news:publication_date>{{ $artigo->updated_at->format(DateTime::W3C) }}</news:publication_date>
                <news:title>{{ $artigo->titulo }}</news:title>
                <news:keywords>{{ implode(",", $artigo->meta->tags ?? []) }}</news:keywords>
            </news:news>
            @if(@$artigo->meta->image)
                <image:image>
                    <image:loc>{{ url("uploads/{$artigo->meta->image}") }}</image:loc>
                    <image:title>{{ $artigo->meta->titulo ?? $artigo->titulo }}</image:title>
                </image:image>
            @endif
        </url>
    @endforeach--}}
</urlset>
