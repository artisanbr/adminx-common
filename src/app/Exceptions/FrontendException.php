<?php

namespace ArtisanBR\Adminx\Common\App\Exceptions;

use Butschster\Head\Facades\Meta;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontendException extends Exception
{
    public function __construct($message = 'Página não encontrada', $code = 404, \Throwable $previous = null)
    {
        {{ Meta::setTitle($message); }}
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {

    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param Request   $request
     *
     * @return Response
     */
    public function render(Request $request): Response
    {

        if ($request->expectsJson()) {
            return response()->json([
                                        'message' => $this->getMessage(),
                                        'error' => [
                                            'code' => $this->getCode(),
                                            'file' => $this->getFile(),
                                            'line' => $this->getLine(),
                                            'trace' => $this->getTrace(),
                                        ]
                                    ], $this->getCode());
        }

        return response()->view(['errors.frontend.'.$this->getCode(), 'errors.frontend.error'], [
            'exception' => $this,
            'message' => $this->getMessage()
        ], $this->getCode())->withException($this);
    }
}
