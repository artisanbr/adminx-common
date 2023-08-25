<?php
/**
 * @var \Adminx\Common\Models\Sites\Site $site
 * @var \Adminx\Common\Models\Pages\Page $page
 */
?>

{!! $site->theme->header_html ?? '' !!}

@yield('header')
