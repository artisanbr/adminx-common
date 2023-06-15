<?php
    /**
     * @var Exception $exception
 */
    ?>
@extends('adminx-frontend::errors.layout')

@section('code', $code ?? $exception->getCode() ?? 404)
@section('title', __($title ?? 'não encontramos o que esta procurando!'))
@section('message', __($exception->getMessage() ?? $message ?? 'Parece que o conteúdo desejado não foi encontrado. Tente outro endereço ou você pode seguir para nossa página inicial no botão abaixo:'))
