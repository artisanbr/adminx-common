<?php
/***
 * @var \Adminx\Common\Models\Widgets\SiteWidget $widget
 * @var \Adminx\Common\Models\Form       $form
 */
?>
@extends('common::layouts.api.ajax-view')

{{--@php
    $form = $widget->sources->first();
@endphp--}}

@if($form->id ?? false)
    <form id="form-{{$form->id}}" class="form w-100" action="{{ route('frontend.send-form', $form->id, false) }}"
          enctype="multipart/form-data" data-grecaptcha-action="{{$form->slug}}">
        @method('POST')


        <div class="row">
            @foreach($form->elements as $element)
                <x-frontend::forms.element :element="$element" :form="$form"/>
            @endforeach
        </div>
        <div class="row">
            <div class="col-12 col-sm-8">
                {{--<script>
                    const verifyCallback_{{$form->id}} = function(response) {
                        alert(response);
                    };
                </script>--}}

                @if($form->config->captcha->enabled)
                <x-common::recaptcha-v2 :site-key="$form->config->captcha->keys->get('site_key') ??  $form->site->config->recaptcha_site_key"  {{--:callback='"verifyCallback_{$form->id}"'--}}/>
                @endif
            </div>
            <div class="col-12 col-sm d-flex justify-content-end align-items-end">
                {{--<button type="submit"
                        class="primary_btn btn btn-primary btn-icon ml-0 ms-auto me-0">
                    <span><i class="fa-solid fa-paper-plane"></i></span> Enviar
                </button>--}}
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

            // On document ready
            $(function () {

                // Class definition
                const formModule_{{$form->id}} = function () {
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
                                /*$.ajax({
                                    url: url_action,
                                    type: 'POST',
                                    data: formData,
                                    cache: false,
                                    processData: false,
                                    contentType: false,
                                    dataType: "json",
                                    accepts: {
                                        json: "application/json"
                                    },
                                }).done*/
                                axios.post(url_action, formData).then(function (response) {

                                    console.log(response.data);

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
                            $form = $('form#form-{{$form->id}}');
                            $submitButton = $form.find('button:submit');
                            $alert = $form.find('.alert');
                            $alertMsg = $form.find('.alert > span:first');

                            console.log('iniciando formulário');
                            console.log($form);

                            handleForm();
                        }
                    };
                }();


                formModule_{{$form->id}}.init();

            });

        </script>
    @endpush

@else
    <x-frontend::alert color="warning" no-close>Não há um formulário vinculado a página</x-frontend::alert>
@endif
