<?php

namespace Adminx\Common\Http\Controllers\API\Widgets;

use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\SiteWidget;
use Barryvdh\Debugbar\Facades\Debugbar;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use voku\helper\HtmlMin;

/**
 * @prefix
 * @as
 */
class WidgetController extends Controller
{

    public function __construct(
        public Site|null $site = null,
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
            case $widgeteable->source->type === 'posts':
                /**
                 * @var Page|null $page ;
                 */

                $page = $widgeteable->source->data;

                if ($page) {

                    $postsQuery = $page->posts()->published();
                    if ($widgeteable->config->sorting->enable || $widgeteable->widget->config->sorting->enable) {
                        $postsQuery = $postsQuery->orderBy($widgeteable->config->sort_column, $widgeteable->config->sort_direction);
                    }
                    $viewData['page'] = $page;
                    $viewData['posts'] = $postsQuery->take(10)->get();
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
            case 'post':
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
        Debugbar::startMeasure('start', "Widget Init #{$public_id}");

        $this->site = FrontendSite::current();


        if (!$this->site) {
            return Response::json('Unauthorized', 401);
        }

        $widgeteable =  $this->site->widgets()->wherePublicId($public_id)->whereHas('widget')->first();

        if (!$widgeteable) {
            return Response::json('Widget not found', 404);
        }

        $widgetView = "adminx-frontend::api.Widgets.{$widgeteable->widget->type->slug}.{$widgeteable->widget->slug}";

        if (!View::exists($widgetView)) {
            return Response::json('Widget View not Found', 501);
        }

        $viewData = $widgeteable->getBuildViewData();

        Debugbar::stopMeasure('start');

        Debugbar::debug($widgetView);
        Debugbar::debug($viewData);


        $htmlMin = new HtmlMin();

        Debugbar::startMeasure('render', "Widget Render #{$public_id}");
        $viewRender = View::make($widgetView, $viewData)->render();
        Debugbar::stopMeasure('render');

        /*return $viewRender;*/

        return Cache::remember("widget-view-{$this->site->public_id}-{$public_id}", 60 * 24 * 7, function() use($htmlMin, $viewRender){
            return $htmlMin->minify($viewRender);
        });
    }
}
