
@extends('adminx-frontend::layout.base')
@section('content')

    {{-- <x-frontend::breadcrumb :append="[ View::getSection('title', 'Erro ' . View::getSection('code', '')) ]" :site="\Adminx\Common\Libs\FrontendEngine\FrontendSiteEngine::current()" />--}}

    @hasSection('content')
        @yield('content')
    @else

        <div class="error-404-area py-150">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 align-self-center">
                        <div class="error-404-content">
                            <h1 class="title mb-5">@yield('code', __('Ops!'))</h1>
                            <h2 class="sub-title mb-4"><span>Desculpe, </span>@yield('title')</h2>
                            <p class="short-desc mb-7">@yield('message')</p>
                            <div class="button-wrap">
                                <a class="btn btn-custom-size lg-size btn-primary btn-secondary-hover rounded-0 me-2"
                                   href="{{ url()->previous() }}">Voltar</a>
                                <a class="btn btn-custom-size lg-size btn-primary btn-secondary-hover rounded-0 me-2"
                                   href="/">Página Inicial</a>
                                @yield('buttons')
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="error-404-img">
                            <div class="scene fill">
                                <div class="layer expand-width" data-depth="0.2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif

@stop
