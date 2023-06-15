<?php
/**
 * @var \Adminx\Common\Models\Site $site
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

    {{--<url>
        <loc>{{ $site->uri }}</loc>
        <changefreq>weekly</changefreq>
        <lastmod>2022-09-25T14:06:34+00:00</lastmod>
    </url>--}}

    {{--Pages--}}
    @foreach($site->pages as $page)

        <url>
            <loc>{{ $page->uri }}</loc>
            <lastmod>{{ $page->updated_at->toIso8601String() }}</lastmod>
            @if($page->using_posts && $page->posts()->count())
                <changefreq>always</changefreq>
                <priority>{{ $page->is_home ? '1.0' : '0.8' }}</priority>
            @elseif($page->data_sources->count())
                <changefreq>daily</changefreq>
                <priority>{{ $page->is_home ? '1.0' : '0.7' }}</priority>
            @else
                <changefreq>daily</changefreq>
                <priority>{{ $page->is_home ? '1.0' : '0.5' }}</priority>
            @endif

            @if(!empty($page->seoImage()))
                <image:image>
                    <image:loc>{{ FrontendUtils::url($page->seoImage()) }}</image:loc>
                    {{--<image:title>{{ $page->seoTitle() }}</image:title>--}}
                </image:image>
            @endif
        </url>

        @foreach($page->data_sources as $dataSource)

            @if($dataSource->items ?? false)
                @foreach($dataSource->items as $dataSourceItem)
                    <url>
                        <loc>{{ $dataSourceItem->uri }}</loc>
                        <changefreq>weekly</changefreq>
                        <lastmod>{{ $dataSourceItem->updated_at->toIso8601String() }}</lastmod>
                        <priority>0.5</priority>

                        @if($dataSourceItem->data->image->file && $dataSourceItem->data->image->file->url)
                            <image:image>
                                <image:loc>{{ FrontendUtils::url($dataSourceItem->data->image->file->url) }}</image:loc>
                                {{--<image:title>{{ $post->seoTitle() }}</image:title>--}}
                            </image:image>
                        @endif
                    </url>
                @endforeach
            @endif

        @endforeach
    @endforeach



    {{--@foreach(\Adminx\Common\Models\Artigo::orderBy("updated_at", "asc")->take(20)->get() as $artigo)
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
