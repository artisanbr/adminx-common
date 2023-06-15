<?php
/**
 * @var \Adminx\Common\Models\Site $site
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

    <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

        @foreach($sitemaps as $sitemap)

            <sitemap>

                <loc>{{ $sitemap['loc'] }}</loc>

                <lastmod>{{ $sitemap['lastmod'] }}</lastmod>

            </sitemap>

        @endforeach

    </sitemapindex>

</urlset>
