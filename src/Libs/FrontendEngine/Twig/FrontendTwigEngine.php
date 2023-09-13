<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FrontendEngine\Twig;

use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\FrontendEngine\FrontendEngineBase;
use Adminx\Common\Libs\FrontendEngine\Twig\Extensions\FrontendTwigExtension;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\CustomLists\Abstract\CustomListItemBase;
use Adminx\Common\Models\CustomLists\CustomListItems\CustomListItemHtml;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Pages\PageInternal;
use Adminx\Common\Models\Templates\Global\Manager\Facade\GlobalTemplateManager;
use Adminx\Common\Models\Themes\Theme;
use Adminx\Common\Models\Themes\ThemeBuild;
use Exception;
use Illuminate\Support\Collection as SupportCollection;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use voku\helper\HtmlMin;

class FrontendTwigEngine extends FrontendEngineBase
{
    /**
     * Lista de Templates em Cache
     *
     * @var SupportCollection|array{head?:string,footer?:string,content?:string}
     */
    public SupportCollection|array $templates;

    protected ChainLoader      $twigChainLoader;
    protected FilesystemLoader $twigFileLoader;
    protected ArrayLoader      $twigArrayLoader;

    protected Environment $twig;

    public array $viewData = [];

    public function __construct(
        //public ?Theme                  $theme = null,
        public ?ThemeBuild             $themeBuild = null,
        public ?string                 $templateNamePrefix = null,
        protected ?FrontendBuildObject $frontendBuild = null,
    )
    {
        $this->templates = collect();
        $this->templateNamePrefix = 'tpl-' . time();
        $this->twigFileLoader = new FilesystemLoader();
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
        return [...$this->viewData, 'frontendBuild' => $this->frontendBuild];
    }

    public function setFrontendBuild(FrontendBuildObject $frontendBuild = new FrontendBuildObject()): static
    {
        $this->frontendBuild = $frontendBuild;

        $this->frontendBuild->meta->initialize();
        $this->frontendBuild->meta->addCsrfToken();

        return $this;
    }

    /**
     * @throws FrontendException
     */
    public function applyTheme(Theme $theme): static
    {
        $themeBuild = $theme->build;

        if (!$themeBuild) {

            $theme->compile();

            $themeBuild = $theme->build()->latest()->first();

            if (!$themeBuild) {
                throw new FrontendException('Tema não compilado, salve seu tema.' . 404);
            }
        }

        $this->themeBuild = $themeBuild;

        //Montar Head
        //$this->templates->put('head', $this->themeBuild->head);

        //Montar Header
        //$this->templates->put('header', $this->themeBuild->header);

        //Montar footer
        //$this->templates->put('footer', $this->themeBuild->footer);

        return $this;
    }

    /**
     * Prepare Twig Filters and Functions
     *
     * @throws LoaderError
     * @throws LoaderError
     */
    protected function prepareTwig(): static
    {

        $arrayTemplates = [];
        $this->twigArrayLoader = new ArrayLoader($this->getTemplatesToTwig());

        $this->twigFileLoader->addPath(GlobalTemplateManager::globalTemplatesPath('base'), 'base');
        $this->twigFileLoader->addPath(GlobalTemplateManager::globalTemplatesPath('pages'), 'pages');

        /*if ($this->currentPage) {
            $arrayTemplates[$this->getTwigTemplateName($mainViewName)] = $this->getPageBaseTemplate();

            //$this->twigFileLoader->addPath(GlobalTemplateManager::globalTemplatesPath('widgets'), 'widgets');

            $pageTemplate = $this->currentPage->page_template;
            if ($pageTemplate) {
                //dd($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id));
                $this->twigFileLoader->addPath($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id), 'template');
            }

        }
        else {
            $arrayTemplates[$this->getTwigTemplateName($mainViewName)] = $this->contentHtml;
        }*/
        /* $staticSiteWidgets = $this->currentPage->site->widgets()->where('config->ajax_render', false);

         if ($staticSiteWidgets->count()) {
             foreach ($staticSiteWidgets->get() as $siteWidget) {

                 $siteWidget->save(); //todo: remove

                 //$this->twigFileLoader->addPath($siteWidget->widget->template->template->global_path, "widget-{$siteWidget->public_id}");

                 //Debugbar::debug("widget-{$siteWidget->public_id}");

                 $arrayTemplates["widget-{$siteWidget->public_id}"] = $siteWidget->content->html;

             }
         }*/

        $this->twigChainLoader = new ChainLoader([$this->twigArrayLoader, $this->twigFileLoader]);
        $this->twig = new Environment($this->twigChainLoader, [
            'auto_reload' => true,
            'autoescape'  => false,
            'debug'       => true,
        ]);

        $this->twig->addExtension(new DebugExtension());

        $this->twig->addExtension(new FrontendTwigExtension($this->twig, $this->currentSite));


        /*$this->twig->addFunction(new TwigFunction('getTemplate', function (string $file) {
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
        }));*/

        return $this;
    }

    protected function getTemplatesToTwig()
    {
        return $this->templates->mapWithKeys(fn($template, $name) => [$this->getTemplateName($name) => $template])->toArray();
    }

    protected function getTemplateName($template): string
    {
        return $this->templateNamePrefix . '-' . $template;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws Exception
     */
    public function renderTwig(string $template): string
    {
        $this->prepareTwig();

        /*if (!$this->twig) {
            throw new Exception('Twig Não Iniciado');
        }*/

        try {
            return $this->twig->render($this->getTemplateName($template), $this->getRenderViewData());
        } catch (Exception $e) {
            dump($template, $this->viewData, $this->templates->toArray());
            throw $e;
        }
    }

    /**
     * @throws FrontendException
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function html($rawHtml = '', $viewData = null, $templateName = null, ?Theme $theme = null): string
    {
        if ($viewData) {
            $this->addViewData($viewData);
        }

        if ($theme) {
            $this->applyTheme($theme);
        }

        $templateName = $templateName ?? 'html-' . time();

        $this->templates->put($templateName, $rawHtml);

        $this->prepareTwig();

        return $this->renderTwig($templateName);
    }

    public function content($rawHtml = '', $viewData = null, $templateName = null, ?Theme $theme = null): string
    {
        if ($viewData) {
            $this->addViewData($viewData);
        }

        $this->applyTheme($theme ?? FrontendSite::current()->theme);

        $templateName = $templateName ?? 'content-' . time();

        $this->templates->put($templateName, $this->getPageBaseTemplate($rawHtml));

        $this->prepareTwig();

        return $this->renderTwig($templateName);
    }

    public function getPageBaseTemplate(string $content): string
    {

        $headHtml = $this->themeBuild->head;
        $headerHtml = $this->themeBuild->header;
        $footerHtml = $this->themeBuild->footer;
        $seoHtml = $this->frontendBuild->seo->html;

        return <<<html
                <html lang="pt-BR">
                    <head>
                        {$seoHtml}
                        {$headHtml}
                    </head>
                    <body id="{{ frontendBuild.body.id }}" class="{{ frontendBuild.body.class }}">
                        {$headerHtml}
                        <main class="main-content">
                            {% if breadcrumb and breadcrumb.enabled %}
                                {{ include('@base/components/breadcrumb.twig') }}
                            {% endif %}
                            {$content}
                        </main>
                        {$footerHtml}
                    </body>
                </html>
                html;

    }

    /**
     * @throws FrontendException
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function page(Page $page): string
    {
        $this->templateNamePrefix = 'page-' . $page->public_id;

        $this->setViewData($page->getBuildViewData());

        $this->setFrontendBuild($page->frontend_build);

        //$this->frontendBuild->meta->registerSeoForPage($page);

        $this->currentSite = $page->site;

        if ($this->currentSite->theme ?? false) {
            $this->applyTheme($this->currentSite->theme);
        }

        $pageTemplate = $page->page_template;

        $pageContent = $page->html;

        if ($pageTemplate) {
            //dd($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id));
            $this->twigFileLoader->addPath($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id), 'template');
            $pageContent .= "{{ include('@template/index.twig') }}";
        }


        //$pageContent = $page->page_template ? "{{ include('@template/index.twig') }}" : '';


        $this->templates->put('page', $this->getPageBaseTemplate($pageContent));


        $renderedTemplate = $this->renderTwig('page');


        //$renderedBlade = Blade::render($rawBlade, $this->viewData);

        if ($this->currentSite->config->enable_html_minify) {
            $htmlMin = new HtmlMin();

            $renderedTemplate = $htmlMin->minify($renderedTemplate);
        }

        return $renderedTemplate;
    }

    /**
     * @throws FrontendException
     */
    public function article(Article $article): string
    {
        $page = $article->page;
        $this->templateNamePrefix = 'article-' . $article->public_id;

        //$this->setCurrentSite($article->site, false);

        //Meta::registerSeoForArticle($article);
        $this->setViewData($article->getBuildViewData([
                                                          'customPageTemplate' => $page->page_template ? '@template/article.twig' : false,
                                                      ]));

        //dd($this->themeBuild);

        $this->setFrontendBuild($article->meta->frontend_build);

        //$this->frontendBuild->meta->registerSeoForArticle($article);

        if ($page->site->theme) {
            $this->applyTheme($page->site->theme);
        }

        $pageContent = '';
        $pageTemplate = $page->page_template;

        if ($pageTemplate) {
            //dd($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id));
            $this->twigFileLoader->addPath($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id), 'template');
            $pageContent .= "{{ include('@template/article.twig') }}";
        }


        //$pageContent = $page->page_template ? "{{ include('@template/index.twig') }}" : '';


        $this->templates->put('article', $this->getPageBaseTemplate($pageContent));


        $renderedTemplate = $this->renderTwig('article');


        if ($page->site->config->enable_html_minify ?? false) {
            $htmlMin = new HtmlMin();

            $renderedTemplate = $htmlMin->minify($renderedTemplate);
        }

        return $renderedTemplate;
    }

    /**
     * @throws FrontendException
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function pageInternal(PageInternal $pageInternal, $modelItem): string
    {
        /**
         * @var EloquentModelBase|CustomListItemBase|CustomListItemHtml $modelItem
         */

        $this->templateNamePrefix = 'page-internal-' . $pageInternal->public_id . '-' . (@$modelItem->public_id ?? @$modelItem->slug ?? time());

        $pageInternal->breadcrumb_config->background_url = $modelItem->data->image_url;

        $this->setViewData($pageInternal->page->getBuildViewData([
                                                       'pageInternal'   => $pageInternal,
                                                       'currentItem' => $modelItem,
                                                       'breadcrumb'  => $pageInternal->breadcrumb([
                                                                                                      ...$pageInternal->page->breadcrumb->items->toArray(),
                                                                                                      '#' => $modelItem->title,
                                                                                                  ]),
                                                   ]));

        $this->setFrontendBuild($modelItem->data->frontend_build ?? $pageInternal->frontend_build);

        //$this->frontendBuild->meta->registerSeoForPage($page);

        $this->currentSite = $pageInternal->page->site;

        if ($this->currentSite->theme ?? false) {
            $this->applyTheme($this->currentSite->theme);
        }

        //$pageTemplate = $pageInternal->page_template;

        $pageContent = $pageInternal->content;

       /* if ($pageTemplate) {
            //dd($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id));
            $this->twigFileLoader->addPath($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id), 'template');
            $pageContent .= "{{ include('@template/index.twig') }}";
        }*/


        //$pageContent = $page->page_template ? "{{ include('@template/index.twig') }}" : '';


        $this->templates->put('page', $this->getPageBaseTemplate($pageContent));


        $renderedTemplate = $this->renderTwig('page');


        //$renderedBlade = Blade::render($rawBlade, $this->viewData);

        if ($this->currentSite->config->enable_html_minify) {
            $htmlMin = new HtmlMin();

            $renderedTemplate = $htmlMin->minify($renderedTemplate);
        }

        return $renderedTemplate;
    }
}
