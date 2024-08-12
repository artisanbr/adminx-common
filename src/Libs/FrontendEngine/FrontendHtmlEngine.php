<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FrontendEngine;

use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Facades\Frontend\FrontendPage;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\CustomLists\Abstract\CustomListAbstract;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\Menus\Menu;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Templates\Global\Manager\Facade\GlobalTemplateManager;
use Adminx\Common\Models\Themes\Theme;
use Adminx\Common\Models\Themes\ThemeBuild;
use Adminx\Common\Models\Widgets\SiteWidget;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Blade;
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
    protected mixed $widgetsTemplates;
    /**
     * @var Collection|mixed|Menu[]
     */
    protected mixed $menus;
    /**
     * @var Collection|mixed|CustomListAbstract[]
     */
    protected mixed $customLists;

    protected ChainLoader $twigLoader;

    protected Environment $twig;

    public array $viewData = [];

    public function __construct(
        public ?Page                   $currentPage = null,
        public ?ThemeBuild             $themeBuild = null,
        public string                  $headHtml = '',
        public string                  $headerHtml = '',
        public string                  $contentHtml = '',
        public string                  $footerHtml = '',
        public ?string                 $viewTemporaryName = null,
        protected ?FrontendBuildObject $frontendBuild = null,
    )
    {
        $this->widgets = collect();
        $this->widgetsTemplates = collect();
        $this->menus = collect();
        $this->customLists = collect();

    }

    public function setCurrentPage(Page $page): static
    {
        $this->currentPage = $page;
        $this->widgets = collect();
        $this->menus = collect();
        $this->customLists = collect();

        $this->setViewName("page-{$page->public_id}");

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

        //Montar Head
        $this->headHtml = $this->themeBuild->head;//renderHead($this->frontendBuild);

        //Montar Header
        $this->headerHtml = $this->themeBuild->header;//renderHeader($this->frontendBuild);

        //Montar footer
        $this->footerHtml = $this->themeBuild->footer;//renderFooter($this->frontendBuild);

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

    public function getPageBaseTemplate(string|bool $customPageTemplate = false): string
    {

        $customTemplateHtml = $this->currentPage->page_template ? "{{ include('@template/index.twig') }}" : '';

        return <<<blade
<html lang="pt-BR">
<head>
    {$this->headHtml}
</head>
<body id="{{ frontendBuild.body.id }}" class="{{ frontendBuild.body.class }}">
{$this->headerHtml}
<main class="main-content">
    {% if breadcrumb and breadcrumb.enabled %}
        {{ include('@base/components/breadcrumb.twig') }}
    {% endif %}
    {$this->contentHtml}
    {$customTemplateHtml}
</main>
{$this->footerHtml}
</body>
</html>
blade;

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

    public function getRenderViewData(): array
    {
        return [...$this->viewData, 'frontendBuild' => $this->frontendBuild, 'header' => $this->headerHtml];
    }

    /**
     * Prepare Twig Filters and Functions
     *
     * @throws LoaderError
     * @throws LoaderError
     */
    protected function startTwig($mainViewName = 'page_base'): static
    {

        $arrayTemplates = [
            //$this->getTwigTemplateName('head')    => $this->headHtml,
            //$this->getTwigTemplateName('header')  => $this->headerHtml,
            //$this->getTwigTemplateName('content') => $this->contentHtml,
            //$this->getTwigTemplateName('footer')  => $this->footerHtml,
        ];

        $fileLoader = new FilesystemLoader();
        $fileLoader->addPath(GlobalTemplateManager::globalTemplatesPath('base'), 'base');
        $fileLoader->addPath(GlobalTemplateManager::globalTemplatesPath('pages'), 'pages');

        if ($this->currentPage) {
            $arrayTemplates[$this->getTwigTemplateName($mainViewName)] = $this->getPageBaseTemplate();

            //$fileLoader->addPath(GlobalTemplateManager::globalTemplatesPath('widgets'), 'widgets');

            $pageTemplate = $this->currentPage->page_template;
            if ($pageTemplate) {
                //dd($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id));
                $fileLoader->addPath($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id), 'template');
            }

        }
        else {
            $arrayTemplates[$this->getTwigTemplateName($mainViewName)] = $this->contentHtml;
        }

        /* $staticSiteWidgets = $this->currentPage->site->widgets()->where('config->ajax_render', false);

         if ($staticSiteWidgets->count()) {
             foreach ($staticSiteWidgets->get() as $siteWidget) {

                 $siteWidget->save(); //todo: remove

                 //$fileLoader->addPath($siteWidget->widget->template->template->global_path, "widget-{$siteWidget->public_id}");

                 //Debugbar::debug("widget-{$siteWidget->public_id}");

                 $arrayTemplates["widget-{$siteWidget->public_id}"] = $siteWidget->content->html;

             }
         }*/

        $arrayLoader = new ArrayLoader($arrayTemplates);
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
        //$this->twig->addExtension(new WidgetTwigExtension($this->currentPage->site->widgets()));
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

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function renderTwig(string $template): string
    {
        $this->startTwig($template);

        if (!$this->twig) {
            throw new Exception('Twig Não Iniciado');
        }

        try {
            return $this->twig->render($template, $this->getRenderViewData());
        } catch (Exception $e) {
            //dump($this->getViewName(), $this->viewData, $this->contentHtml);
            throw $e;
        }
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
    public function renderPageTwig(): string
    {
        return $this->renderTwig($this->getTwigTemplateName('page_base'));
        //return $this->renderTwigFile('@base/page.twig');
    }

    /**
     * @throws FrontendException
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function page(Page $page): string
    {
        $this->setCurrentPage($page);
        $this->setViewData($this->currentPage->getBuildViewData(/*[
                                                                    'customPageTemplate' => $this->currentPage->page_template ? '@template/index.twig' : false,
                                                                ]*/));

        $this->setFrontendBuild($this->currentPage->prepareFrontendBuild());

        $this->frontendBuild->meta->registerSeoForPage($this->currentPage);

        if ($this->currentPage->site->theme) {
            $this->theme($this->currentPage->site->theme);
        }

        //$rawBlade = $this->renderPageTwig();
        //dd($rawBlade);


        $renderedBlade = $this->renderPageTwig();
        //$renderedBlade = Blade::render($rawBlade, $this->viewData);

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

        $this->setFrontendBuild($article->prepareFrontendBuild());

        $this->frontendBuild->meta->registerSeoForArticle($article);

        if ($this->currentPage->site->theme) {
            $this->theme($this->currentPage->site->theme);
        }

        $this->contentHtml = $article->content;

        $this->startTwig();

        $rawBlade = $this->renderPageTwig();

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

            $theme->generateBuild();

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
        $viewName = $viewName ?? 'temp-html-' . time();

        if ($viewData) {
            $this->addViewData($viewData);
        }

        $this->contentHtml = $rawHtml;

        $this->setViewName($viewName);

        $this->startTwig();

        return $this->renderPageTwig();
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function widget($widget_public_id): string
    {

        //Verificar no cache
        $siteWidget = $this->widgets->firstWhere('public_id', $widget_public_id);

        //Não encontrou, buscar no banco
        if (!$siteWidget) {
            $siteWidget = FrontendSite::current()->widgets()->where('public_id', $widget_public_id)->first();

            if ($siteWidget) {
                $this->widgets->add($siteWidget);
            }
        }

        //Se não encontrar parar aqui.
        if (!$siteWidget) {
            return "Widget {$widget_public_id} não encontrado";
        }

        //Se estiver sem content, compilar
        if (empty($siteWidget->content->html)) {
            $siteWidget->save();
        }

        if (!$siteWidget->config->ajax_render) {

            $template = $this->twig->createTemplate($siteWidget->content->html, "widget-{$siteWidget->public_id}");

            return $template->render($siteWidget->getBuildViewData());
        }

        return $siteWidget->content->html;

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
                $this->customLists->add($customList);
            }
        }


        return $customList ?? new CustomList();

    }
}
