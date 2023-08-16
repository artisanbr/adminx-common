<?php

namespace Adminx\Common\Http\Controllers\API\Widgets;

use Adminx\Common\Facades\Frontend\FrontendHtml;
use Adminx\Common\Facades\Frontend\FrontendPage;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Facades\Frontend\FrontendTwig;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Widgets\SiteWidget;
use Barryvdh\Debugbar\Facades\Debugbar;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Symfony\Component\ErrorHandler\Debug;
use voku\helper\HtmlMin;

/**
 * @middleware frontend
 * @prefix
 * @as
 */
class WidgetController extends Controller
{

    public function __construct(
        public ?Site $site = null,
    ) {}

    /**
     * @i
     */
    protected function getViewData(SiteWidget $widgeteable, array $merge_data = []): array
    {
        $viewData = [
            'widgeteable' => $widgeteable,
            'site' => $widgeteable->site,
            'variables'   => $widgeteable->variables,
        ];

        Debugbar::debug($widgeteable->source->type);

        switch (true) {
            case $widgeteable->source->type === 'articles':
                /**
                 * @var Page|null $page ;
                 */

                $page = $widgeteable->source->data;

                if ($page) {

                    $articlesQuery = $page->articles()->published();
                    if ($widgeteable->config->sorting->enable || $widgeteable->widget->config->sorting->enable) {
                        $articlesQuery = $articlesQuery->orderBy($widgeteable->config->sort_column, $widgeteable->config->sort_direction);
                    }
                    $viewData['page'] = $page;
                    $viewData['articles'] = $articlesQuery->take(10)->get();
                }
                break;
            case Str::contains($widgeteable->source->type, 'list'):

                $customList = $widgeteable->source->data;
                $page = $customList->page;

                $viewData['page'] = $page;
                $viewData['customList'] = $customList;
                //Todo: persinalizar quantidade de itens
                $viewData['customListItems'] = $customList->items()->with(['list','list.page'])->take(10)->get();
                break;
            case $widgeteable->source->type === 'form':
                $viewData['form'] = $widgeteable->source->data;
                break;
            default:
                break;
            //Todo:
            /*case 'page':
            case 'products':
            case 'form':
            case 'article':
            case 'address':*/

        }

        return !empty($merge_data) ? array_merge($viewData, $merge_data) : $viewData;
    }


    /**
     * @url render/{public_Id}
     * @method get
     */
    public function render(Request $request, $public_id)
    {
        /**
         * @var ?SiteWidget $siteWidget
         */
        Debugbar::startMeasure('get site');
        $this->site = FrontendSite::current();
        Debugbar::stopMeasure('get site');


        if (!$this->site) {
            return Response::json('Unauthorized', 401);
        }

        Debugbar::startMeasure('get widget');
        $siteWidget = $this->site->widgets()->wherePublicId($public_id)->whereHas('widget')->first();

        if (!$siteWidget) {
            return Response::json('Widget not found', 404);
        }
        Debugbar::stopMeasure('get widget');


        //dd($siteWidget->toArray(), $viewData);

        if(!empty($siteWidget->template_content)){
            Debugbar::startMeasure('get build data');
            $viewData = $siteWidget->getTwigRenderData();
            Debugbar::stopMeasure('get build data');

            Debugbar::startMeasure('render widget template');
            $widgetRender = FrontendTwig::html($siteWidget->template_content, $viewData, 'widget-'.$siteWidget->public_id);
            Debugbar::stopMeasure('render widget template');

            return response($widgetRender);
        }

        Debugbar::startMeasure('get build data');
        $viewData = $siteWidget->getViewRenderData();
        Debugbar::stopMeasure('get build data');


        $widgetView = "adminx-frontend::api.Widgets.{$siteWidget->widget->type->slug}.{$siteWidget->widget->slug}";


        Debugbar::startMeasure('check view');
        if (!View::exists($widgetView)) {
            return Response::json('Widget View not Found', 501);
        }
        Debugbar::stopMeasure('check view');


        //$htmlMin = new HtmlMin();

        Debugbar::startMeasure('render view');
        $viewRender = view($widgetView, $viewData);
        Debugbar::stopMeasure('render view');

        return $viewRender;

        /*return Cache::remember("widget-view-{$this->site->public_id}-{$public_id}", 60 * 24 * 7, function() use($htmlMin, $viewRender){
            return $htmlMin->minify($viewRender);
        });*/
    }
}
