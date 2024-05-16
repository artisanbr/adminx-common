<?php
/**
 * @var \Adminx\Common\Models\Sites\Site                                        $site
 * @var \Adminx\Common\Models\Themes\Theme                                       $theme
 * @var \Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject $frontendBuild
 * @var \Butschster\Head\MetaTags\Meta                                    $themeMeta
 */

/*$captcha = new \Anhskohbo\NoCaptcha\NoCaptcha($site->config->recaptcha_private_key, $site->config->recaptcha_site_key);*/

?>


{{--Footer--}}
{!! $theme->footer->html ?? '' !!}

@if(empty($theme->copyright->html))
<footer id="footer-copyright" class="footer-bottom py-2">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div
                        class="footer-copyright footer-copyright-3 text-center d-flex align-items-center justify-content-center">
                    <span class="mt-1">
                        {{ date("Y") }} © <b>{{ $site->title }}</b> — {{ config('common.app.provider.copyright') }}
                    </span>
                    <a href="{{ config('common.app.provider.url') }}" target="_blank"
                       title="Powered by {{ config('common.app.provider.name') }}"
                       class="ms-2 ml-2">
                        <img src="{{ FrontendUtils::asset(config('common.app.provider.logo')) }}" height="18"
                             alt="{{ config('common.app.provider.name') }}"/>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
@else
    {!! $theme->copyright->html !!}
@endif

{{--</div>--}}
{{--Build--}}
{{--@if($theme->build->bundles?->get('js') ?? null)
    <script defer>
        {!! $theme->build->bundles?->get('js') ?? '' !!}
    </script>
@endif--}}

{{--Scripts--}}
@if($themeMeta ?? false)
    {!! $themeMeta->footer()->toHtml() !!}
@endif

{{--<script src="https://www.google.com/recaptcha/api.js?render=explicit"></script>--}}
{{--@@stack('js-includes')--}}

<script type="text/javascript" async>
    document.addEventListener('DOMContentLoaded', (event) => {
        moment.locale("pt-br");
    });

    // Ajax calls should always have the CSRF token attached to them, otherwise they won't work
    /*$.ajaxSetup({
        // force ajax call on all browsers
        cache: false,
        // Enables cross domain requests
        crossDomain: true,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        }
    });*/
    //recaptcha
    /*window.onload = function() {
        const recaptchaDivs = document.getElementsByClassName('g-recaptcha');

        Array.prototype.forEach.call(recaptchaDivs, function(div) {
            let sitekey = div.getAttribute('data-sitekey');
            grecaptcha.render(div, {
                'sitekey' : sitekey
            });
        });
    };*/
</script>
{{--{!! $captcha->renderJs() !!}--}}

@include('common-frontend::layout.inc.alerts')

{{--@@stack('footer-includes')
@@stack('js')--}}
{!! $theme->assets->js->after_body->html ?? '' !!}
@{{ frontendBuild.body.after }}

{!! '</body></html>' !!}
