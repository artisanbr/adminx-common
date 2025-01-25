@php use Adminx\Common\Enums\Forms\FormCaptchaType; @endphp
<?php
/***
 * @var \Adminx\Common\Models\Widgets\SiteWidget|null $widget
 * @var \Adminx\Common\Models\Templates\Template|null $template
 * @var \Adminx\Common\Models\Form                    $form
 */
?>
@extends('common::layouts.api.ajax-view')

@php
    $formId = "form-{$form->public_id}";
@endphp

@if($form->id ?? false)
    <form id="{{ $formId }}" class="form w-100" action="{{ route('frontend.send-form', $form->public_id, false) }}" method="POST"
          enctype="multipart/form-data" data-grecaptcha-action="{{$form->slug}}">
        @method('POST')

        @if($form->config->allow_select_recipient && $form->config->recipients->count() > 1)

            <div class="row mb-3">
                <div class="col-12">
                    @include((@$widget?->template ?? @$template)->getTemplateBladeFile("elements/select_field"), [
    'slug' => 'recipient_address',
    'title' => $form->config->select_recipient_title,
    'optionList' => $form->config->recipients->map(fn($recipient) => [
                                'value' => $recipient->address,
                                'text' => $recipient->title,
                        ])
                ])
                </div>
            </div>

        @endif
        <div class="row">
            @foreach($form->elements as $element)
                @include((@$widget?->template ?? @$template)->getTemplateBladeFile("elements/element"), compact('element','form'))
            @endforeach
        </div>
        <div class="row">
            {{--@dump($form->config->captcha->type?->is(FormCaptchaType::RecaptchaV2->value), $form->config->captcha->keys->get('site_key') ??  $form->site->config->recaptcha_site_key)--}}
            @if($form->config->captcha->enabled)
            <div class="col-12 col-sm-8">
                @if($form->config->captcha->type?->is(FormCaptchaType::RecaptchaV2->value))
                <x-common::recaptcha-v2 :site-key="$form->config->captcha->keys->get('site_key') ??  $form->site->config->recaptcha_site_key"/>
                @endif
            </div>
            @endif
            <div class="col-12 col-sm d-flex justify-content-end align-items-start">
                {!! $form->config->send_button->html !!}
            </div>
        </div>
        {{--Alert--}}
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible my-5 fade collapse" role="alert">
                    <span>Aguarde....</span>
                </div>
            </div>
        </div>
    </form>
    @push('js')
        <script>

            // Class definition
            const formModule_{{$form->public_id}} = function () {
                // Elements
                let $form;
                let $submitButton;
                let $alert;
                let $alertMsg;

                const alertMsgs = {
                    wait: 'Aguarde...',
                    success: 'Enviado com sucesso!',
                    error: 'Falha ao enviar.',
                };

                const alertManager = function () {
                    let alertMsg = alertMsgs.wait;

                    let baseClass = 'alert alert-dismissible my-5 fade collapse';


                    return {
                        type: (type = 'info') => {
                            $alert.attr('class', `${baseClass} alert-${type}`);
                        },
                        show: () => {
                            $alert.addClass('show');
                        },
                        hide: () => {
                            $alert.removeClass('show');
                        },
                        message: (message, autoShow = true) => {
                            $alertMsg.html(message);
                            if (autoShow) {
                                alertManager.show();

                                //const alertElement = document.querySelector(".alert");
                                if ($alert[0]) {
                                    $alert[0].scrollIntoView({ behavior: "smooth", block: "center" });
                                }
                            }
                        },
                        alert: (message, type = 'info') => {
                            alertManager.type(type);
                            alertManager.message(message);
                        },
                        info: (message = alertMsgs.wait) => {
                            alertManager.alert(message);
                        },
                        success: (message = alertMsgs.success) => {
                            alertManager.alert(message, 'success');
                        },
                        error: (message = alertMsgs.error) => {
                            alertManager.alert(message, 'danger');
                        }
                    }
                }();

                // Handle form
                const handleForm = function (e) {
                    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/

                    // Handle form submit
                    $form.on('submit', function (e) {
                        // Prevent button default action
                        e.preventDefault();

                        let validation_check = true;

                        let url_action = $form.attr("action");

                        // Validate form
                        if (validation_check) {

                            alertManager.info();
                            $submitButton.prop('disabled', true);

                            // Simulate ajax request
                            //axios.post(url_action, $form.serializeObject())
                            let formData = new FormData($form[0]);
                            axios.post(url_action, formData).then(function (response) {

                                @if($form->config->on_success ?? false)
                                        {{$form->config->on_success}}(response.data);
                                @endif

                                if (response.data.result) {
                                    $form.resetFormData();
                                    //response.data.message
                                    alertManager.success();
                                } else {
                                    alertManager.error(response.data.message);
                                }

                            }).catch(function (error) {

                                console.log(error.response.data.message);

                                @if($form->config->on_fail ?? false)
                                        {{$form->config->on_fail}}(error.response.data);
                                @endif


                                if (error.response) {
                                    let dataMessage = 'Ops';//error.response.data.message;
                                    let dataErrors = error.response.data.errors ?? false;

                                    for (const errorsKey in dataErrors) {
                                        if (!dataErrors.hasOwnProperty(errorsKey)) continue;
                                        dataMessage += "\r\n" + dataErrors[errorsKey];
                                    }

                                    //alert dataMessage
                                    alertManager.error(error.response.data.message);
                                }

                            })
                                .then(function () {
                                    // always executed
                                    setTimeout(function () {
                                        alertManager.hide();
                                        $submitButton.prop('disabled', false);
                                        let isToRedirect = @json($form->config->enable_redirect);
                                        let redirectUrl = '{{ $form->config->redirect_url }}';
                                        if(isToRedirect && redirectUrl && redirectUrl.length){
                                            location.href = redirectUrl;
                                        }
                                    }, 8000);

                                    //always js event config
                                });


                        } else {
                            $alertMsg.text('Desculpe, parece que os dados preenchidos são inválidos.');
                            $alert.removeClass('alert-primary').addClass('alert-warning show');
                        }
                    });
                }

                // Public functions
                return {
                    // Initialization
                    init: function () {
                        $form = $('form#form-{{$form->public_id}}');
                        $submitButton = $form.find('button:submit');
                        $alert = $form.find('.alert');
                        $alertMsg = $form.find('.alert > span:first');

                        handleForm();
                    }
                };
            }();

            // On document ready
            //$(function () {
            @if($widget ?? false)
            $(function () {
                formModule_{{$form->public_id}}.init();
            });
                @else
                document.addEventListener('DOMContentLoaded', (event) => {
                    formModule_{{$form->public_id}}.init();
                });
                @endif


        </script>
    @endpush

@else
    <x-frontend::alert color="warning" no-close>Não há um formulário vinculado a página</x-frontend::alert>
@endif
