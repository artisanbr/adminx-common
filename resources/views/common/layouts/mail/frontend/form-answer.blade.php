<?php
/**
 * @var \Adminx\Common\Models\FormAnswer $formAnswer
 */
?>
@extends('adminx-common::layouts.mail.base', [
    'site' => $formAnswer->site ?? Auth::user()->site ?? new \Adminx\Common\Models\Sites\Site()
])
@section('subject', "Nova mensagem em \"{$formAnswer->form->title}\".")
@section('description', 'Mensagem recebida às '.$formAnswer->created_at->format('d/m/Y \à\s H:i:s').', confira abaixo as respostas enviadas:')
@section('action_uri', '#')
{{--@section('action_uri', route('app.elements.forms.answers', [$formAnswer->form->id))--}}
@section('action_text', 'Ver Mais Respostas (em breve)')
@section('content')
    {{--<p class="text-gray-700"
       style="line-height: 24px; font-size: 16px; color: #4a5568; width: 100%; margin: 0;"
       align="left">Uma nova mensagem foi recebida em "{{ $formAnswer->form->title }}", confira abaixo as respostas enviadas:</p>--}}

    <x-common::mail.space/>

    @if($formAnswer->form_elements ?? false)
        <div class="row">
            @foreach($formAnswer->form_elements as $element)

                <div class="{{ $element->size_class }} mb-3 d-flex flex-column">

                    @if($element->type === \Adminx\Common\Enums\Forms\FormElementType::Html)
                        {!! $element->html !!}
                    @else

                        <p class="text-gray-700"
                           style="line-height: 24px; font-size: 16px; color: #4a5568; width: 100%; margin: 0;"
                           align="left"><b>{{ $element->title }}</b></p>

                        <div class="border border-1 rounded-1 p-3">
                            @if($formAnswer->data->{$element->slug} ?? false)
                                @if($element->type->_hasUpload())
                                    @php
                                        $fileModel = \Adminx\Common\Models\File::find($formAnswer->data->{$element->slug});
                                    @endphp

                                    @if($fileModel ?? false)
                                        <a href="{{ $fileModel->uri }}" target="_blank"
                                           class="btn btn-sm btn-light-info d-inline-flex align-items-center">
                                            <x-icon icon="attach" size="2" color="info"/>
                                            Ver Arquivo
                                        </a>
                                    @else
                                        <p class="mb-0">Nenhum arquivo anexado</p>
                                    @endif
                                @else
                                    <p class="mb-0">{!! @$formAnswer->data->{$element->slug} ?? '' !!}</p>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
    <x-common::mail.space/>
    <x-common::mail.space/>
    <p class="text-gray-700"
       style="line-height: 24px; font-size: 16px; color: #4a5568; width: 100%; margin: 0;"
       align="left">
        <small>Mensagem enviada em {{ $formAnswer->created_at->format('d/m/Y \à\s H:i:s') }}</small>
    </p>
    <p class="text-gray-700"
       style="line-height: 24px; font-size: 16px; color: #4a5568; width: 100%; margin: 0;"
       align="left">
        <small>Endereço de origen: <a href="{{ $formAnswer->origin_url }}" target="_blank"
                                      style="color: #0d6efd;">{{ $formAnswer->origin_url }}</a></small>
    </p>
    <p class="text-gray-700"
       style="line-height: 24px; font-size: 16px; color: #4a5568; width: 100%; margin: 0;"
       align="left">
        <small>IP de origen: {{ $formAnswer->origin_ip }}</small>
    </p>
    <x-common::mail.space/>
    <p class="text-gray-700"
       style="line-height: 24px; font-size: 16px; color: #4a5568; width: 100%; margin: 0;"
       align="left">
        Em breve você poderá conferir o histórico dos formulários diretamente pelo seu Painel Administrativo.
        {{--todo: Confira mais respostas para este formul&aacute;rio no <a href="{{ route('app.elements.forms.answers', $formAnswer->form->id) }}" target="_blank" style="color: #0d6efd;">Painel de Controle</a> do seu site.--}}
    </p>
@endsection
