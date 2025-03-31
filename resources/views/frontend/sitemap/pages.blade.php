<?php
/**
 * @var \Adminx\Common\Models\Sites\Site $site
 */
?>
<?php
print '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<urlset
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
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
            @if($page->articles()->count())
                <changefreq>always</changefreq>
                <priority>{{ $page->is_home ? '1.0' : '0.8' }}</priority>
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

        @foreach($page->children as $childrenPage)

            <url>
                <loc>{{ $childrenPage->uri }}</loc>
                <lastmod>{{ $childrenPage->updated_at->toIso8601String() }}</lastmod>
                @if($childrenPage->articles()->count())
                    <changefreq>always</changefreq>
                    <priority>{){ $childrenPage->is_home ? '1.0' : '0.8' }}</priority>
                @else
                    <changefreq>daily</changefreq>
                    <priority>{){ $childrenPage->is_home ? '1.0' : '0.5' }}</priority>
                @endif

                @if(!empty($childrenPage->seoImage()))
                    <image:image>
                        <image:loc>{{ FrontendUtils::url($childrenPage->seoImage()) }}</image:loc>
                    </image:image>
                @endif
            </url>

            @if(($childrenPage->pageable ?? false) && ($childrenPage->pageable->items?->count() ?? false))
                @foreach($childrenPage->pageable->items()->paginate(100, ['*'], 'page', 1) as $pageableItem)
                    @if($pageableItem->url ?? false)
                        <url>
                            <loc>{{ $site->uriTo($pageableItem->url) }}</loc>
                            <changefreq>weekly</changefreq>
                            <lastmod>{{ $pageableItem->updated_at->toIso8601String() }}</lastmod>
                            <priority>0.5</priority>

                            @if($pageableItem->data->image_url ?? false)
                                <image:image>
                                    <image:loc>{{ FrontendUtils::url($pageableItem->image_url) }}</image:loc>
                                    {{--<image:title>{{ $article->seoTitle() }}</image:title>--}}
                                </image:image>
                            @endif
                        </url>
                    @endif
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
