<?php
/***
 * @var \Adminx\Common\Models\Widgets\SiteWidget $widget
 */
?>

@if(($widget->config->variables ?? false) && $widget->config->variableValue('facebook_url'))

    @once
        @push('body-js')
            <div id="fb-root"></div>
            <script async defer crossorigin="anonymous"
                    src="https://connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v15.0"
                    nonce="MJf88bdf"></script>
        @endpush
    @endonce

    <div class="fb-page" data-href="{{ $widget->config->variableValue('facebook_url') }}"
         data-tabs="{{ $widget->config->variableValue('tabs') }}"
         data-width="{{ $widget->config->variableValue('width') }}"
         data-height="{{ $widget->config->variableValue('height') }}"
         data-small-header="{{ $widget->config->variableValue('small_header', false) }}"
         data-adapt-container-width="true" data-hide-cover="{{ $widget->config->variableValue('hide_cover') }}"
         data-show-facepile="true">
        <blockquote cite="{{ $widget->config->variableValue('facebook_url') }}" class="fb-xfbml-parse-ignore">
            <a href="{{ $widget->config->variableValue('facebook_url') }}">{{ $widget->site->title }}</a>
        </blockquote>
    </div>
@endif


