<?php
/**
 * @var \Adminx\Common\Models\Site $site
 * @var \Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject $frontendBuild
 */

?>


{{--Footer--}}
{!! $site->theme->footer_twig_html ?? '' !!}

<footer id="footer-copyright" class="footer-bottom py-2">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div
                        class="footer-copyright footer-copyright-3 text-center d-flex align-items-center justify-content-center">
                    <span class="mt-1">
                        {{ date("Y") }} © <b>{{ $site->title }}</b> — {{ config('adminx.app.provider.copyright') }}
                    </span>
                    <a href="{{ config('adminx.app.provider.url') }}" target="_blank"
                       title="Powered by {{ config('adminx.app.provider.name') }}"
                       class="ms-2 ml-2">
                        <img src="{{ FrontendUtils::asset(config('adminx.app.provider.logo')) }}" height="18"
                             alt="{{ config('adminx.app.provider.name') }}"/>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

{{--</div>--}}

{{--Scripts--}}
{!! Meta::footer()->toHtml() !!}
@@stack('js-includes')

<script>
    moment.locale("pt-br");

    // Ajax calls should always have the CSRF token attached to them, otherwise they won't work
    $.ajaxSetup({
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
    });

</script>

@include('adminx-frontend::layout.inc.alerts')

@@stack('footer-includes')
@@stack('js')
{!! $site->theme->js_html ?? '' !!}
@{!! $frontendBuild->body->after !!}
</body>
</html>
