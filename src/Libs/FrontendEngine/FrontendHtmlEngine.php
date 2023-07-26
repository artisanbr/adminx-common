<?php

namespace Adminx\Common\Libs\FrontendEngine;

use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Facades\Frontend\FrontendPage;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Bases\CustomListBase;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\Menu;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\SiteWidget;
use Adminx\Common\Models\Templates\Global\Manager\Facade\PageTemplateManager;
use Adminx\Common\Models\Theme;
use Adminx\Common\Models\ThemeBuild;
use Butschster\Head\Contracts\Packages\ManagerInterface;
use Butschster\Head\MetaTags\Meta;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Blade;
use PragmaRX\Support\Exceptions\Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use voku\helper\HtmlMin;

class FrontendHtmlEngine extends FrontendEngineBase
{
    /**
     * @var Collection|mixed|SiteWidget[]
     */
    protected mixed $widgets;
    /**
     * @var Collection|mixed|Menu[]
     */
    protected mixed $menus;
    /**
     * @var Collection|mixed|CustomListBase[]
     */
    protected mixed $customLists;

    protected ChainLoader $twigLoader;

    protected Environment $twig;

    public array $viewData = [];

    public function __construct(
        public ?Page                   $currentPage = null,
        public ?ThemeBuild             $themeBuild = null,
        public string                  $headerHtml = '',
        public string                  $contentHtml = '',
        public string                  $footerHtml = '',
        public ?string                 $viewTemporaryName = null,
        protected ?FrontendBuildObject $frontendBuild = null,
    )
    {
        $this->widgets = collect();
        $this->menus = collect();
        $this->customLists = collect();

    }

    public function setCurrentPage(Page $page): static
    {
        $this->currentPage = $page;
        $this->widgets = collect();
        $this->menus = collect();
        $this->customLists = collect();

        if (!$this->viewTemporaryName) {
            $this->setViewName("page-{$page->public_id}");
        }

        /*if ($this->currentPage->site->theme) {
            $this->theme($this->currentPage->site->theme, $this->currentPage->frontendBuild());
        }*/

        $this->contentHtml = $this->currentPage->html;

        return $this;
    }

    public function setThemeBuild(ThemeBuild $themeBuild): static
    {
        $this->themeBuild = $themeBuild;

        //$frontendBuild->meta->reset();

        //Montar Header
        $this->headerHtml = $this->themeBuild->renderHeader($this->frontendBuild);

        //Montar footer
        $this->footerHtml = $this->themeBuild->renderFooter($this->frontendBuild);

        return $this;
    }

    public function setFrontendBuild(FrontendBuildObject $frontendBuild = new FrontendBuildObject()): static
    {
        $this->frontendBuild = $frontendBuild;

        return $this;
    }

    public function setViewName(?string $viewTemporaryName = null): static
    {
        $this->viewTemporaryName = $viewTemporaryName ?? 'temp-html';

        return $this;
    }

    public function getTwigTemplateName($name = null): string
    {
        return $this->viewTemporaryName . ($name ? "-{$name}" : "");
    }

    public function getViewName(): string
    {
        return ($this->currentPage ? "{$this->currentPage->public_id}-" : "") . $this->viewTemporaryName;
    }

    public function setViewData(array $viewData = []): static
    {
        $this->viewData = $viewData;

        return $this;
    }

    public function addViewData(array $viewData = []): static
    {
        return $this->mergeViewData($viewData);
    }

    public function mergeViewData(array $viewData = []): static
    {
        $this->viewData = [...$this->viewData, ...$viewData];

        return $this;
    }

    /**
     * Prepare Twig Filters and Functions
     */
    protected function startTwig(): static
    {

        $arrayLoader = new ArrayLoader([
                                           $this->getTwigTemplateName('header')  => $this->headerHtml,
                                           $this->getTwigTemplateName('content') => $this->contentHtml,
                                           $this->getTwigTemplateName('footer')  => $this->footerHtml,
                                       ]);

        $fileLoader = new FilesystemLoader();
        $fileLoader->addPath(PageTemplateManager::globalTemplatesPath('base'), 'base');

        if ($this->currentPage->page_template) {
            $fileLoader->addPath($this->currentPage->page_template->path, 'template');
        }


        $this->twigLoader = new ChainLoader([$arrayLoader, $fileLoader]);

        $this->twig = new Environment($this->twigLoader, [
            'auto_reload' => true,
            'autoescape'  => false,
            'debug'       => true,
        ]);

        $this->twig->addExtension(new DebugExtension());


        $this->twig->addFunction(new TwigFunction('getTemplate', function (string $file) {
            return $this->getTwigTemplateName($file);
        }));

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

        //Custom Lists
        $this->twig->addFunction(new TwigFunction('custom_list', function (string $public_id) {
            return $this->customList($public_id);
        }));

        return $this;
    }

    public function page(Page $page): string
    {
        $this->setCurrentPage($page);
        $this->setViewData($this->currentPage->getBuildViewData([
                                                                    'customPageTemplate' => $this->currentPage->page_template ? '@template/index.twig' : false,
                                                                ]));

        $this->setFrontendBuild($this->currentPage->frontendBuild());

        $this->frontendBuild->meta->registerSeoForPage($this->currentPage);

        if ($this->currentPage->site->theme) {
            $this->theme($this->currentPage->site->theme);
        }

        $this->startTwig();

        $rawBlade = $this->renderTwig();
        //dd($rawBlade);

        $renderedBlade = Blade::render($rawBlade, $this->viewData);

        if ($this->currentPage->site->config->enable_html_minify) {
            $htmlMin = new HtmlMin();

            $renderedBlade = $htmlMin->minify($renderedBlade);
        }

        return $renderedBlade;
    }

    public function article(Article $article): string
    {
        //$this->setCurrentSite($article->site, false);
        $this->setCurrentPage($article->page);

        //Meta::registerSeoForArticle($article);
        $this->setViewName("article-{$article->public_id}");
        $this->setViewData($article->getBuildViewData([
                                                          'customPageTemplate' => $this->currentPage->page_template ? '@template/article.twig' : false,
                                                      ]));

        //dd($this->themeBuild);

        $this->setFrontendBuild($article->frontendBuild());

        $this->frontendBuild->meta->registerSeoForArticle($article);

        if ($this->currentPage->site->theme) {
            $this->theme($this->currentPage->site->theme);
        }

        $this->contentHtml = $article->content;

        $this->startTwig();

        $rawBlade = $this->renderTwig();

        $renderedBlade = Blade::render($rawBlade, $this->viewData);


        if ($this->currentPage->site->config->enable_html_minify ?? false) {
            $htmlMin = new HtmlMin();

            $renderedBlade = $htmlMin->minify($renderedBlade);
        }

        return $renderedBlade;
    }

    /**
     * @throws FrontendException
     */
    public function theme(Theme $theme): static
    {
        $themeBuild = $theme->build;

        if (!$themeBuild) {

            $theme->compile();

            $themeBuild = $theme->build()->latest()->first();

            if (!$themeBuild) {
                throw new FrontendException('Tema não compilado, salve seu tema.' . 404);
            }
        }

        $this->setThemeBuild($themeBuild);

        return $this;
    }

    public function html($rawHtml = null, $viewData = null, $viewName = null): string
    {
        if ($viewData) {
            $this->addViewData($viewData);
        }

        $this->contentHtml = $rawHtml;

        $this->setViewName($viewName ?? 'temp-html-' . time());

        $this->startTwig();

        return $this->renderTwig();
    }

    /**
     * @throws FrontendException
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function content($rawHtml = null, $viewData = null, ?Theme $theme = null): string
    {
        if ($viewData) {
            $this->addViewData($viewData);
        }

        if ($theme ?? FrontendPage::current()) {
            $this->setCurrentPage(FrontendPage::current());
            $this->theme($theme ?? FrontendPage::current()->site->theme);

        }

        return $this->html($rawHtml, $viewData, 'content-' . time());
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function renderTwig(): string
    {
        if (!$this->twig) {
            throw new Exception('HTML não definido');
        }

        try {

            //dd($this->viewData['breadcrumb']);

            return $this->twig->render('@base/page.twig', $this->viewData);

        } catch (\Exception $e) {
            //dump($this->getViewName(), $this->viewData, $this->contentHtml);
            throw $e;
        }
    }

    public function widget($widget_public_id): string
    {

        //Verificar no cache
        $siteWidget = $this->widgets->firstWhere('public_id', $widget_public_id);

        //Não encontrou, buscar no banco
        if (!$siteWidget) {
            $siteWidget = $this->currentPage->site->widgets()->where('public_id', $widget_public_id)->first();

            if ($siteWidget) {
                $this->widgets->add($siteWidget);
            }
        }


        //$siteWidget = $this->widgets->firstWhere('public_id', $widget_public_id);


        if (!$siteWidget) {
            return "Widget {$widget_public_id} não encontrado";
        }

        if (empty($siteWidget->content->html)) {
            $siteWidget->save();
        }

        //return $siteWidget->html;
        return $siteWidget->content->html;

        //Widget View
        //Debugbar::debug($widget->public_id, $widget->config->ajax_render);

        /* $renderView = $siteWidget->config->ajax_render ? 'adminx-common::Elements.Widgets.renders.ajax-render' : 'adminx-common::Elements.Widgets.renders.static-render';
         $renderData = $siteWidget->config->ajax_render ? compact('siteWidget') : $siteWidget->getBuildViewData();

         $widgetView = View::make($renderView, [...$renderData, 'page' => $this->currentPage]);

         return $widgetView->render();*/

    }

    public function menu($menuSlug)
    {

        //Verificar no cache
        $menu = $this->menus->firstWhere('slug', $menuSlug); //todo: public_id

        //Não encontrou, buscar no banco
        if (!$menu) {
            $menu = $this->currentPage->site->menus()->where('slug', $menuSlug)->first();

            if ($menu) {
                $this->menus->add($menu);
            }
        }

        //$menu = $this->menus->firstWhere('slug', $menuSlug);

        return $menu->html ?? 'Menu não encontrado';

    }

    public function customList($public_id)
    {

        //Verificar no cache
        $customList = $this->customLists->firstWhere('public_id', $public_id) ?? $this->customLists->firstWhere('slug', $public_id);

        //Não encontrou, buscar no banco
        if (!$customList) {

            $customList = $this->currentPage->site->lists()->where('public_id', $public_id)->orWhere('slug', $public_id)->first();

            if ($customList) {
                $customList = $customList->mountModel();
                $this->customLists->add($customList);
            }
        }


        return $customList ?? new CustomList();

    }
}
