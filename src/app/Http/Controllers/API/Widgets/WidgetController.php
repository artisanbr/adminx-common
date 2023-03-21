<?php

namespace ArtisanBR\Adminx\Common\App\Http\Controllers\API\Widgets;

use App\Http\Controllers\Controller;
use ArtisanBR\Adminx\Common\App\Facades\FrontendSiteEngine;
use ArtisanBR\Adminx\Common\App\Models\Page;
use ArtisanBR\Adminx\Common\App\Models\Site;
use ArtisanBR\Adminx\Common\App\Models\Widgeteable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

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
    protected function getViewData(Widgeteable $widgeteable, array $merge_data = []): array
    {
        $viewData = [
            'widgeteable' => $widgeteable,
            'variables'   => $widgeteable->variables,
        ];

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
        $this->site = FrontendSiteEngine::current();


        if (!$this->site) {
            return Response::json('Unauthorized', 401);
        }

        $widgeteable = Widgeteable::wherePublicId($public_id)->with(['widget'])->first();

        if (!$widgeteable) {
            return Response::json('Widget not found', 404);
        }

        $widgetView = "api.Widgets.{$widgeteable->widget->type->slug}.{$widgeteable->widget->slug}";

        if (!View::exists($widgetView)) {
            return Response::json('Widget View not Found', 501);
        }

        $viewData = $this->getViewData($widgeteable);

        /*Debugbar::startMeasure('render', 'Renderização');
        dump($viewData);
        Debugbar::stopMeasure('render');*/

        return view($widgetView, $viewData);
    }
}
