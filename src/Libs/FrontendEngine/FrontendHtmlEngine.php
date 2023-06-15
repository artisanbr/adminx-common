<?php

namespace Adminx\Common\Libs\FrontendEngine;

use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Facades\FrontendHtml;
use Adminx\Common\Facades\FrontendSite;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Post;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Theme;
use Adminx\Common\Models\ThemeBuild;
use Adminx\Common\Models\SiteWidget;
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
     * @var \Illuminate\Database\Eloquent\Collection|mixed|SiteWidget[]
     */
    protected mixed $widgets;

    protected ArrayLoader $twigLoader;

    protected string $headerHtml  = '';
    protected string $contentHtml = '';
    protected string $footerHtml  = '';

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

    public function setViewData(array $viewData = []): static
    {
        $this->viewData = $viewData;

        return $this;
    }

    public function addViewData(array $viewData = []): static
    {
        $this->viewData = [...$this->viewData, ...$viewData];

        return $this;
    }

    /**
     * Prepare Twig Filters and Functions
     */
    protected function startTwig(): static
    {

        $this->twigLoader = new ArrayLoader([
                                                $this->viewTemporaryName => $this->headerHtml . $this->contentHtml . $this->footerHtml,
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
            $this->widgets = $this->currentSite->widgets;
            $this->menus = $this->currentSite->menus;

            if ($changeData) {
                $this->viewData = [
                    'site'  => $this->currentSite,
                    'theme' => $this->currentSite->theme,
                ];
            }

        }

        return $this;
    }

    public function setViewName(string|null $viewTemporaryName = null): static
    {
        $this->viewTemporaryName = $viewTemporaryName ?? 'temp-html';

        return $this;
    }

    public function page(Page $page): string
    {
        $this->setCurrentSite($page->site, false);
        $this->setViewData($page->getBuildViewData());
        $this->setViewName("page-tg-{$page->public_id}");


        if($page->site->theme){
            $this->theme($page->site->theme, $page->frontendBuild());
        }

        $this->contentHtml = View::make($page->getBuildViewPath(), $this->viewData)->render();
        $this->startTwig();

        $rawBlade = $this->renderTwig();
        dd($rawBlade);

        $renderedBlade = Blade::render($rawBlade, $this->viewData);

        if ($this->currentSite->config->enable_html_minify) {
            $htmlMin = new HtmlMin();

            $renderedBlade = $htmlMin->minify($renderedBlade);
        }

        return $renderedBlade;
    }

    public function post(Post $post): string
    {
        $this->setCurrentSite($post->site, false);
        $this->setViewData($post->getBuildViewData());
        $this->setViewName("post-tg-{$post->public_id}");


        if($post->site->theme){
            $this->theme($post->site->theme, $post->frontendBuild());
        }

        $this->contentHtml = View::make($post->getBuildViewPath(), $this->viewData)->render();
        $this->startTwig();

        $rawBlade = $this->renderTwig();
        // dd($rawBlade);

        $renderedBlade = Blade::render($rawBlade, $this->viewData);

        if ($this->currentSite->config->enable_html_minify) {
            $htmlMin = new HtmlMin();

            $renderedBlade = $htmlMin->minify($renderedBlade);
        }

        return $renderedBlade;
    }

    /**
     * @throws FrontendException
     */
    public function theme(Theme $theme, FrontendBuildObject $frontendBuild = new FrontendBuildObject()): static
    {
        $themeBuild = $theme->build;

        if (!$themeBuild) {

            $theme->compile();

            $themeBuild = $theme->build()->latest()->first();

            if (!$themeBuild) {
                throw new FrontendException('Tema não compilado, salve seu tema.' . 404);
            }
        }

        //Montar Header
        $this->headerHtml = $themeBuild->renderHeader($frontendBuild);

        //Montar footer
        $this->footerHtml .= $themeBuild->renderFooter($frontendBuild);

        return $this;
    }

    public function html($rawHtml = null, $viewData = null): string
    {
        if ($viewData) {
           $this->addViewData($viewData);
        }

        $this->contentHtml = $rawHtml;

        $this->setViewName('temp-html-' . time());

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
            dump($this->viewTemporaryName, $this->viewData, $this->contentHtml);
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function widget($widget_public_id): string
    {


        $siteWidget = $this->widgets->firstWhere('public_id', $widget_public_id);


        if (!$siteWidget) {
            return "Widget {$widget_public_id} não encontrado";
        }

        if (empty($siteWidget->content->html)) {
            $siteWidget->save();
        }

        return $siteWidget->content->html;

        //Widget View
        //Debugbar::debug($widget->public_id, $widget->config->ajax_render);

        $renderView = $siteWidget->config->ajax_render ? 'adminx-common::Elements.Widgets.renders.ajax-render' : 'adminx-common::Elements.Widgets.renders.static-render';
        $renderData = $siteWidget->config->ajax_render ? compact('siteWidget') : $siteWidget->getBuildViewData();

        $widgetView = View::make($renderView, $renderData);

        return $widgetView->render();

    }

    public function menu($menuSlug)
    {

        $menu = $this->menus->firstWhere('slug', $menuSlug);

        return $menu->html ?? 'Menu não encontrado';

    }
}
