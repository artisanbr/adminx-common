<?php
/**
 * @var \Adminx\Common\Models\Site                                        $site
 * @var \Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject $frontendBuild
 */
?>
        <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{--before--}}
    @{!! $frontendBuild->head->gtag_script !!}
    @{!! $frontendBuild->head->before !!}
    <!-- Assets -->
    {!! Meta::toHtml() !!}
    {{--Site CSS--}}
    {!! $site->theme->css_html ?? '' !!}
    {{--Page CSS--}}
    @{!! $frontendBuild->head->css !!}
    {{--@@yield('css-includes')
    @@stack('css-includes')
    @@stack('css')
    @@yield('css')--}}

    <link rel="preload" as="image" href="{{ FrontendUtils::asset(config('adminx.app.provider.logo')) }}"/>

    @foreach(['logo','logo_secondary'] as $media)
        @if(($site->theme->media->{$media} ?? false) && ($site->theme->media->{$media}->url ?? false))
            <link rel="preload" as="image" href="{{ $site->theme->media->{$media}->url ?? '' }}"/>
        @endif
    @endforeach


    {!! $site->theme->js->head_js_html ?? '' !!}
    @{!! $frontendBuild->head->after !!}
    {{--@@stack('head-js')
    @@yield('head-js')--}}

</head>
<body id="@{{ $frontendBuild->body->id }}"
      class="@{{ $frontendBuild->body->class }}">
{!! $site->theme->js->after_body_js_html ?? '' !!}

@{!! $frontendBuild->body->before !!}

{{--Theme Header--}}
{!! $site->theme->header_twig_html ?? '' !!}
