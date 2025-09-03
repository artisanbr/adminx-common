@props([
    //'site' => new \Adminx\Common\Models\Sites\Site(),
    'noAjax' => false,
    'id' => 'g-recaptcha-'.\Delight\Random\Random::alphaLowercaseHumanString(7),
    'callback' => null,
    'siteKey' => null,
    //'privateKey' => null,
])

<div id="{{ $id }}" class="g-recaptcha mb-3"></div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        //recaptcha

        grecaptcha.render(document.getElementById('{{ $id }}'), {
            'sitekey': '{{ $siteKey /*$site->config->recaptcha_site_key*/ }}',

            @if($callback)
            'callback': {{ $callback }},
            @endif

        });

        $(function(){

        });

    });
</script>
