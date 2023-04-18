<?php

namespace Adminx\Common\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use FrontendSite;
use ReCaptcha\ReCaptcha as GReCaptcha;

class Recaptcha
{

    private $error_msg = "Verificação de ReCaptcha inválida, atualize a página e tente novamente.";

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->_jsvalidation) return $next($request);

        $request->validate([
                               'recaptcha_token' => 'required',
                           ]);

        $site = FrontendSite::current();

        $response = (new GReCaptcha($site->config->recaptcha_private_key))
            ->setExpectedAction($request->recaptcha_action ?? config("services.recaptcha.action"))
            ->verify($request->recaptcha_token, $request->ip());

        if (!$response->isSuccess() || $response->getScore() < config('services.recaptcha.minimum_score')) {

            return $request->ajax() ? ApiResponse::error($response->getErrorCodes(), $this->error_msg, $response->getErrorCodes(), 401)->toJson() : redirect()->back()->withErrors($this->error_msg)->with($request->all());
        }

        return $next($request);

        //Auth::guard($guard)
    }
}
