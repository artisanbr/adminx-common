<?php
/**
 * @var \ArtisanBR\Adminx\Common\App\Models\Site $site
 * @var \ArtisanBR\Adminx\Common\App\Models\Page $page
 */
?>

{!! $site->theme->header_html ?? '' !!}

@yield('header')
