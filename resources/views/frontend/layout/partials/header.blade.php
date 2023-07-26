<?php
/**
 * @var \Adminx\Common\Models\Site                                        $site
 * @var \Adminx\Common\Models\Theme                                       $theme
 * @var \Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject $frontendBuild
 */
?>
        <!DOCTYPE html>
{!! '<html lang="'.str_replace('_', '-', app()->getLocale()).'">' !!}

<head>
    {{--before--}}
    @{!! $frontendBuild->head->gtag_script !!}
    @{!! $frontendBuild->head->before !!}
    <!-- Assets -->
    {{--{!! Meta::toHtml() !!}--}}
    @{!! $frontendBuild->meta->toHtml() !!}
    @if($themeMeta ?? false)
        {!! $themeMeta->toHtml() !!}
    @endif
    {{--Site CSS--}}
    {!! $theme->assets->css_bundle_html ?? '' !!}
    {{--Page CSS--}}
    @{!! $frontendBuild->head->css !!}
    {{--@@yield('css-includes')
    @@stack('css-includes')
    @@stack('css')
    @@yield('css')--}}

    <link rel="preload" as="image" href="{{ FrontendUtils::asset(config('common.app.provider.logo')) }}"/>

    @foreach(['logo','logo_secondary'] as $media)
        @if(($theme->media->{$media} ?? false) && ($theme->media->{$media}->url ?? false))
            <link rel="preload" as="image" href="{{ $theme->media->{$media}->url ?? '' }}"/>
        @endif
    @endforeach


    {!! $theme->assets->js->head->html ?? '' !!}
    {!! $theme->assets->head_script->html ?? '' !!}
    @{!! $frontendBuild->head->after !!}
    {{--@@stack('head-js')
    @@yield('head-js')--}}

</head>

{!! '<body id="@{{ $frontendBuild->body->id }}" class="@{{ $frontendBuild->body->class }}">' !!}

{!! $theme->assets->js->before_body->html ?? '' !!}

@{!! $frontendBuild->body->before !!}

{{--Theme Header--}}
{!! $theme->header->twig_html ?? '' !!}
