<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Exceptions;

use Adminx\Common\Facades\Frontend\FrontendTwig;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontendException extends Exception
{


    public function __construct(protected string $title = 'Página não encontrada', $message = 'Desculpe, não encontramos o que procura.', $code = 404, \Throwable $previous = null)
    {

        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report() {}

    /**
     * Render the exception into an HTTP response.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function render(Request $request): Response
    {

        if ($request->expectsJson()) {
            return response()->json([
                                        'message' => $this->getMessage(),
                                        'error'   => [
                                            'code' => $this->getCode(),
                                                                                                                    'file' => $this->getFile(),
                                                                                                                    'line' => $this->getLine(),
                                                                                                                    'trace' => $this->getTrace(),
                                        ],
                                    ], $this->getCode());
        }

        return response()->make(FrontendTwig::error($this, $this->title), $this->getCode());

        /*return response()->view(['common-frontend::errors.' . $this->getCode(), 'common-frontend::errors.error'], [
            'exception' => $this,
            'code'      => $this->getCode(),
            'error'     => [
                'code'  => $this->getCode(),
                'file'  => $this->getFile(),
                'line'  => $this->getLine(),
                'trace' => $this->getTrace(),
            ],
            'message'   => $this->getMessage(),
        ],                      $this->getCode())->withException($this);*/
    }
}
