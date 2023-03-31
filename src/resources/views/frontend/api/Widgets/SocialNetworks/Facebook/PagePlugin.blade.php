<?php
/***
 * @var \ArtisanBR\Adminx\Common\App\Models\Widgeteable $widgeteable
 */
?>

@if(($widgeteable->config->variables ?? false) && $widgeteable->config->variableValue('facebook_url'))

    @once
        @push('body-js')
            <div id="fb-root"></div>
            <script async defer crossorigin="anonymous" src="https://connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v15.0"
                    nonce="MJf88bdf"></script>
        @endpush
    @endonce

    <div class="fb-page" data-href="{{ $widgeteable->config->variableValue('facebook_url') }}"
         data-tabs="{{ $widgeteable->config->variableValue('tabs') }}"
         data-width="{{ $widgeteable->config->variableValue('width') }}"
         data-height="{{ $widgeteable->config->variableValue('height') }}"
         data-small-header="{{ $widgeteable->config->variableValue('small_header', false) }}"
         data-adapt-container-width="true" data-hide-cover="{{ $widgeteable->config->variableValue('hide_cover') }}"
         data-show-facepile="true">
        <blockquote cite="{{ $widgeteable->config->variableValue('facebook_url') }}" class="fb-xfbml-parse-ignore">
            <a href="{{ $widgeteable->config->variableValue('facebook_url') }}">{{ $widgeteable->site->title }}</a>
        </blockquote>
    </div>
@endif


