<?php

namespace Adminx\Common\Http\Middleware;

use Adminx\Common\Libs\Support\Str;
use Closure;
use FrontendSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ForceSSL
{

    public function handle(Request $request, Closure $next)
    {
        return self::forceRequest($request, $next);
    }

    public static function forceRequest(Request $request, Closure $next, $scheme = 'https') {

        if (!Str::contains(config('app.url'), ['.local', 'localhost'])) {
            //Redirect if using http
            if (!FrontendSite::isForwarded() && !$request->secure()) {
                return redirect()->secure($request->getRequestUri());
            }

            //Set https as default
            URL::forceScheme($scheme);
        }

        return $next($request);
    }
}
