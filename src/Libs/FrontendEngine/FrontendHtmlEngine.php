<?php

namespace Adminx\Common\Libs\FrontendEngine;

use Adminx\Common\Facades\FrontendHtml;
use Adminx\Common\Facades\FrontendSite;
use Adminx\Common\Models\Page;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\ThemeBuild;
use Adminx\Common\Models\Widgeteable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use PragmaRX\Support\Exceptions\Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\ArrayLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use voku\helper\HtmlMin;

class FrontendHtmlEngine extends FrontendEngineBase
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection|mixed|Widgeteable[]
     */
    protected mixed $widgeteables;

    protected ArrayLoader $twigLoader;

    protected string|null $rawHtml = null;

    protected array $customViewData = [];

    protected Environment $twig;
    /**
     * @var \Illuminate\Database\Eloquent\Collection|mixed
     */
    protected mixed $menus;

    public array $viewData = [];

    public function __construct(
        public Site|null $currentSite = null,
        public string    $viewTemporaryName = 'temp-html'
    )
    {
        if (!$this->currentSite) {
            $this->setCurrentSite(FrontendSite::current());
        }

    }

    /**
     * Prepare Twig Filters and Functions
     */
    protected function startTwig(): static
    {

        $this->twigLoader = new ArrayLoader([
                                                $this->viewTemporaryName => $this->rawHtml,
                                            ]);

        $this->twig = new Environment($this->twigLoader, [
            'auto_reload' => true,
            'autoescape'  => false,
            'debug'       => true,
        ]);

        $this->twig->addExtension(new DebugExtension());

        //Menu
        $this->twig->addFilter(new TwigFilter('menu', function (string $slug) {
            return $this->menu($slug);
        }));

        $this->twig->addFunction(new TwigFunction('menu', function (string $slug) {
            return $this->menu($slug);
        }));

        //Widget
        $this->twig->addFilter(new TwigFilter('widget', function (string $public_id) {
            return $this->widget($public_id);
        }));

        $this->twig->addFunction(new TwigFunction('widget', function (string $public_id) {
            return $this->widget($public_id);
        }));

        return $this;
    }

    public function setCurrentSite(Site|null $site = null, $changeData = true): static
    {
        $this->currentSite = $site;

        if ($this->currentSite) {
            $this->widgeteables = $this->currentSite->widgeteables;
            $this->menus = $this->currentSite->menus;

            if($changeData){
                $this->viewData = [
                    'site'  => $this->currentSite,
                    'theme' => $this->currentSite->theme,
                ];
            }

        }

        return $this;
    }

    public function setViewName(string $viewTemporaryName = 'temp-html'): static
    {
        $this->viewTemporaryName = $viewTemporaryName;

        return $this;
    }

    public function page(Page $page): string
    {
        $this->setCurrentSite($page->site, false);
        $this->viewData = $page->getBuildViewData();


        $themeBuild = $page->site->theme->build()->latest()->first();

        if (!$themeBuild) {

            $page->site->theme->compile();
            $themeBuild = $page->site->theme->build()->latest()->first();
        }

        if($themeBuild){

            //Montar Header
            $pageBlade = $themeBuild->renderHeader($page);

            //Montar Corpo
            $pageBlade .= View::make($page->getBuildViewPath(), $this->viewData)->render();

            //Montar footer
            $pageBlade .= $themeBuild->renderFooter($page);

            $this->rawHtml = $pageBlade;

            $this->startTwig();


        }else{
            $this->rawHtml = View::make($page->getBuildViewPath(), $this->viewData)->render();
            $this->startTwig();
        }

        $renderedBlade = Blade::render($this->renderTwig(), compact('page'));

        if ($this->currentSite->config->enable_html_minify) {
            $htmlMin = new HtmlMin();

            $renderedBlade = $htmlMin->minify($renderedBlade);
        }

        return $renderedBlade;
    }

    public function html($rawHtml = null, $viewData = null): string
    {
        if($viewData){
            $this->viewData = $viewData;
        }

        $this->rawHtml = $rawHtml;

        $this->startTwig();

        return $this->renderTwig();
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function renderTwig(): string
    {
        if (!$this->twig) {
            throw new Exception('HTML não definido');
        }

        try {

            return $this->twig->render($this->viewTemporaryName, $this->viewData);

        } catch (\Exception $e) {
            dump($this->viewTemporaryName, $this->viewData, $this->rawHtml);
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function widget($widgeteable_public_id): string
    {


        $widgeteable = $this->widgeteables->firstWhere('public_id', $widgeteable_public_id);

        if (!$widgeteable) {
            return "Widget {$widgeteable_public_id} não encontrado";
        }

        //Widget View
        //Debugbar::debug($widgeteable->public_id, $widgeteable->config->ajax_render);

        $renderView = $widgeteable->config->ajax_render ? 'adminx-common::Elements.Widgets.renders.ajax-render' : 'adminx-common::Elements.Widgets.renders.static-render';
        $renderData = $widgeteable->config->ajax_render ? compact('widgeteable') : $widgeteable->getBuildViewData();

        $widgeteableView = View::make($renderView, $renderData);

        return $widgeteableView->render();

    }

    public function menu($menuSlug)
    {

        $menu = $this->menus->firstWhere('slug', $menuSlug);

        return $menu->html ?? 'Menu não encontrado';

    }
}
