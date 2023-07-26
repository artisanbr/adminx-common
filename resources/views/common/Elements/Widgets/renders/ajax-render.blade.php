<?php
/***
 * @var \Adminx\Common\Models\SiteWidget $siteWidget
 */
?>
<div class="row">
    <div class="col-12 {{ $siteWidget->css_class ?? '' }} widget-portal widget-{{ $siteWidget->public_id }}"
         id="widget-{{ $siteWidget->public_id }}">
        <div class="w-100 d-flex justify-content-center my-5 py-5 fa-3x"><i
                    class="fa-solid fa-circle-notch fa-spin fa-spin-pulse"></i></div>
    </div>
    <script async>
        const WidgetModule_{{ $siteWidget->public_id }} = function () {
            const widgetAreaID = "#widget-{{ $siteWidget->public_id }}";
            const renderRemote = function () {
                const $widgetArea = $(widgetAreaID);
                axios
                    .get('{{ route('common.api.widgets.render', $siteWidget->public_id, false) }}')
                    .then(function (response) {
                        const widgetHTML = response.data;
                        $widgetArea.html(widgetHTML);
                    })
                    .catch(function (e) {
                        console.info('Falha de carregamento, Cód: W-{{ $siteWidget->public_id }} - Contate o Suporte');
                        console.log(e);
                    })
                    .then(function (response) {
                        //always
                    });
            };
            return {
                init: function () {
                    renderRemote();
                }
            }
        }();
        document.addEventListener('DOMContentLoaded', (event) => {
            WidgetModule_{{ $siteWidget->public_id }}.init();
        });
    </script>
</div>
