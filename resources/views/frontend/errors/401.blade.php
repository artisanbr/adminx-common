<?php
    /**
     * @var Exception $exception
 */
    ?>
@extends('common-frontend::errors.layout')

@section('code', 401)
@section('title', __('procedimento não autorizado!'))
@section('message', __($exception->getMessage() ?? $message ?? 'Você não tem permissão para executar esta ação.'))
@section('buttons')
@endsection
