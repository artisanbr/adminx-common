<?php
/**
 * @var \Adminx\Common\Models\Sites\Site $site
 * @var \Adminx\Common\Models\Pages\Page $page
 */
?>
@push('head-meta')
    <link rel="preload" as="image" href="{{ appAsset(config('common.app.provider.logo')) }}" />
@endpush

{!! $site->theme->footer_html ?? '' !!}

<footer id="footer-copyright" class="footer-bottom py-2">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div
                    class="footer-copyright footer-copyright-3 text-center d-flex align-items-center justify-content-center">
                    <span class="mt-1">
                        @{{ "now"|date("Y") }} © <b>@{{ site.title }}</b> — {{ config('common.app.provider.copyright') }}
                    </span>
                    <a href="{{ config('common.app.provider.url') }}" target="_blank" title="Powered by {{ config('common.app.provider.name') }}"
                       class="ms-2 ml-2">
                        <img src="{{ appAsset(config('common.app.provider.logo')) }}" height="18px"
                             alt="{{ config('common.app.provider.name') }}"/>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
