<?php
/**
 * @var \Adminx\Common\Models\Sites\Site $site
 */
?>
<?php print '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"
              xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    @foreach($sitemaps as $sitemap)

        <sitemap>

            <loc>{{ $sitemap['loc'] }}</loc>

            <lastmod>{{ $sitemap['lastmod'] }}</lastmod>

        </sitemap>

    @endforeach

</sitemapindex>
