<?php
/**
 * @var \Adminx\Common\Models\Sites\Site                                        $site
 * @var \Adminx\Common\Models\Themes\Theme                                       $theme
 * @var \Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject $frontendBuild
 */

$captcha = new \Anhskohbo\NoCaptcha\NoCaptcha($site->config->recaptcha_private_key, $site->config->recaptcha_site_key);
?>
{{--GTag--}}
@{{ frontendBuild.head.gtag_script }}
{{--Before Head--}}
@{{ frontendBuild.head.before }}
{{--Assets--}}
{{--Meta--}}
{{--@{{ frontendBuild.meta.toHtml() }}--}}

{{--Pre meta--}}
@if($themeMeta ?? false)
    {!! $themeMeta->toHtml() !!}
@endif

{{--Build--}}
{{--@if($theme->build->bundles?->get('css') ?? null)
    <style type="text/css">
        {!! $theme->build->bundles?->get('css') ?? '' !!}
    </style>
@endif--}}
@if($theme->build->bundles?->get('head_js') ?? null)
    <script type="text/javascript">
        {!! $theme->build->bundles?->get('head_js') ?? '' !!}
    </script>
@endif
{{--Site CSS--}}
{!! $theme->assets->css_bundle_html ?? '' !!}
{{--Page CSS--}}
@{{ frontendBuild.head.css }}
{{--Pre-load--}}
{{--<link rel="preload" as="image" href="{{ FrontendUtils::asset(config('common.app.provider.logo')) }}"/>
@foreach(['logo','logo_secondary'] as $media)
    @if(($theme->media->{$media} ?? false) && ($theme->media->{$media}->url ?? false))
        <link rel="preload" as="image" href="{{ $theme->media->{$media}->url ?? '' }}"/>
    @endif
@endforeach--}}
{!! $theme->assets->js->head->html ?? '' !!}
{!! $theme->assets->head_script->html ?? '' !!}
@{{ frontendBuild.head.after }}


{!! $captcha->renderJs() !!}
