<?php
/**
 * @var \Adminx\Common\Models\Sites\Site                                        $site
 * @var \Adminx\Common\Models\Themes\Theme                                       $theme
 * @var \Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject $frontendBuild
 */
?>
{{--before--}}
@{{ frontendBuild.head.gtag_script }}
<!--Meta Before-->
@{{ frontendBuild.head.before }}
{{--Assets--}}
<!--Meta HTML-->
@{{ frontendBuild.meta.toHtml() }}
<!--End Meta HTML-->
{{--Pre meta--}}
@if($themeMeta ?? false)
    {!! $themeMeta->toHtml() !!}
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