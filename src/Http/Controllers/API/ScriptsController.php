<?php

namespace Adminx\Common\Http\Controllers\API;

use App\Libs\Helpers\HtmlHelper;
use App\Libs\Support\Url;
use App\Models\Site;
use App\Http\Controllers\Controller;
use JShrink\Minifier;

/**
 * @prefix scripts
 * @as scripts.
 * @middleware ['guest','auth.site']
 */
class ScriptsController extends Controller
{

    /**
     * @url /{debug?}
     * @method get,post
     */
    public function index($public_id = null, $debug = false)
    {
        $site = Site::getFromPreviousDomain($public_id);

        if (!$site) {
            return response('Unauthorized', 500);
        }

        //Page
        $previousPath = Url::previousUrlPath();

        $page = null;

        if(empty($previousPath) || $previousPath === '/'){
            $page = $site->home_page;
        }else{
            $page_slug = explode('/', $previousPath)[0];

            dd($previousPath, $page_slug, $page);
        }

        dd($previousPath, $page);


        //$plugins = view('api.frontend.scripts.plugins', compact('site'))->render();
        //$content = view('api.frontend.scripts.index', compact('site'))->render();

        $retorno = $debug ? $content : $plugins.Minifier::minify($content);

        return response($retorno, 200)->header('Content-Type', 'text/javascript');
    }

}
