<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FrontendEngine\Twig;

use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\FrontendEngine\FrontendEngineBase;
use Adminx\Common\Libs\FrontendEngine\Twig\Extensions\FrontendTwigExtension;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Category;
use Adminx\Common\Models\CustomLists\Abstract\CustomListItemAbstract\CustomListItemAbstract;
use Adminx\Common\Models\CustomLists\CustomListItem;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Objects\Seo\Seo;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Pages\PageInternal;
use Adminx\Common\Models\Templates\Global\Manager\Facade\GlobalTemplateManager;
use Adminx\Common\Models\Themes\Theme;
use Adminx\Common\Models\Themes\ThemeBuild;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Extension\StringLoaderExtension;
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
        $this->frontendBuild = new FrontendBuildObject();
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

        $this->initFrontendBuild();

        return $this;
    }

    public function registerFrontendBuild(FrontendBuildObject $frontendBuild = new FrontendBuildObject()): static
    {

        if (!empty($frontendBuild->head->gtag_script)) {
            $this->frontendBuild->head->gtag_script = $frontendBuild->head->gtag_script;
        }

        if (!empty($frontendBuild->body->gtag_script)) {
            $this->frontendBuild->body->gtag_script = $frontendBuild->body->gtag_script;
        }

        if (!empty($frontendBuild->head->css)) {
            $this->frontendBuild->head->css .= $frontendBuild->head->css;
        }

        if (!empty($frontendBuild->head->before)) {
            $this->frontendBuild->head->addBefore($frontendBuild->head->before);
        }

        if (!empty($frontendBuild->head->after)) {
            $this->frontendBuild->head->addAfter($frontendBuild->head->after);
        }

        if (!empty($frontendBuild->body->id)) {
            $this->frontendBuild->body->id = $frontendBuild->body->id;
        }

        if (!empty($frontendBuild->body->class)) {
            $this->frontendBuild->body->class = $frontendBuild->body->class;
        }

        if (!empty($frontendBuild->body->before)) {
            $this->frontendBuild->body->addBefore($frontendBuild->body->before);
        }

        if (!empty($frontendBuild->body->after)) {
            $this->frontendBuild->body->addAfter($frontendBuild->body->after);
        }

        $this->registerFrontendSeo($frontendBuild->seo);

        return $this;
    }

    public function registerFrontendSeo(Seo|array $seo): static
    {

        $this->frontendBuild->seo->mergeWith($seo);

        return $this;
    }

    public function initFrontendBuild(): static
    {
        $this->frontendBuild->meta->initialize();
        $this->frontendBuild->meta->addCsrfToken();

        $this->frontendBuild->meta->registerSeoObject($this->frontendBuild->seo);

        $this->frontendBuild->seo->html = $this->frontendBuild->meta->toHtml();

        return $this;
    }

    /**
     * @throws FrontendException
     */
    public function applyTheme(Theme $theme): static
    {
        $themeBuild = $theme->build;

        if (!$themeBuild) {

            $theme->generateBuild();

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

        Debugbar::startMeasure('prepareTwig');
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
        $this->twig->addExtension(new StringLoaderExtension());

        Debugbar::stopMeasure('prepareTwig');

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


    /**
     * @throws FrontendException
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function error(Exception $exception, $title = 'Página não encontrada', ?Theme $theme = null): string
    {
        $this->currentSite = FrontendSite::current();

        $this->setViewData($this->currentSite->getBuildViewData());

        $this->registerFrontendBuild($this->currentSite->frontendBuild());

        //$this->frontendBuild->meta->registerSeoForPage($page);

        $theme = $theme ?? $this->currentSite->theme;

        $this->applyTheme($theme);


        $templateName = 'error-' . time();

        //dd($this->getPageBaseTemplate($this->getErrorTemplate($exception, $title)));

        $this->templates->put($templateName, $this->getPageBaseTemplate($this->getErrorTemplate($exception, $title)));

        $this->prepareTwig();

        return $this->renderTwig($templateName);
    }

    public function getPageBaseTemplate(string $content): string
    {

        $this->initFrontendBuild();

        $headHtml = $this->themeBuild->head;
        $headerHtml = $this->themeBuild->header;
        $footerHtml = $this->themeBuild->footer;
        $seoHtml = $this->frontendBuild->seo->html;

        /*<main class="main-content"></main>*/

        return <<<html
                <!DOCTYPE html>
                <html lang="pt-BR">
                    <head>
                        {$seoHtml}
                        {$headHtml}
                    </head>
                    <body id="{{ frontendBuild.body.id }}" class="{{ frontendBuild.body.class }}">
                        {$this->frontendBuild->body->gtag_script}
                        {$headerHtml}                        
                        {% if breadcrumb and breadcrumb.enabled %}
                                {{ include('@base/components/breadcrumb.twig') }}
                        {% endif %}
                        {$content}
                        
                        {$footerHtml}
                    </body>
                </html>
                html;

    }

    public function getErrorTemplate(Exception $exception, $title = 'Página não encontrada'): string
    {

        $previousUrl = url()->previous();

        return <<<html
                <div class="error-{$exception->getCode()}-area py-150">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-5 align-self-center">
                                <div class="error-404-content">
                                    <h1 class="tit0e mb-4">{$exception->getCode()}</h1>
                                    <h2 class="sub-title mb-3">{$title}</h2>
                                    <p class="short-desc">{$exception->getMessage()}</p>
                                    <div class="button-wrap py-50">
                                        <a class="btn btn-custom-size lg-size btn-primary btn-secondary-hover rounded-0 me-2"
                                           href="{$previousUrl}">Voltar</a>
                                        <a class="btn btn-custom-size lg-size btn-primary btn-secondary-hover rounded-0 me-2"
                                           href="/">Página Inicial</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="error-404-img">
                                    <div class="scene fill">
                                        <div class="layer expand-width" data-depth="0.2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                html;

    }

    public function getCDNDirListTemplate(): string
    {

        return Cache::remember('cdn-dir-list', now()->addMonth(1), function () {
            return <<<html
                <!DOCTYPE html>
                <html lang="pt-BR">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Listagem de Diretório</title>
                </head>
                <body>
                    <h1>Listagem de Diretório</h1>
                    <p>Este diretório não possui uma listagem de arquivos, por favor refira-se a um arquivo específico.</p>
                </body>
                </html>
                html;
        });

    }

    public function useCache()
    {

        if ($this->currentSite) {
            //Verifica se está ativo nas configurações
            $useCacheConfig = $this->currentSite->config->performance->enable_advanced_cache ?? false;

            if ($useCacheConfig) {
                //Verifica se não está em uma página de busca
            }
        }

        return false;
    }

    public function getFromCache($name)
    {
        return Cache::has("site-cache:{$name}") ? Cache::get("site-cache:{$name}") : false;
    }

    public function saveCache($name, $content)
    {
        return Cache::put("site-cache:{$name}", $content, now()->addHours($this->cacheHours));
    }

    public function purgeCache($name): bool
    {
        return Cache::forget("site-cache-{$name}");
    }

    /**
     * @throws FrontendException
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function page(Page $page, $mergeData = []): string
    {
        /**
         * @var ?Category $category
         */
        $this->templateNamePrefix = "@page={$page->public_id}";

        $this->setViewData($page->getBuildViewData($mergeData));

        //$this->registerFrontendBuild($page->frontend_build);
        $this->registerFrontendBuild($page->prepareFrontendBuild());

        //$this->frontendBuild->meta->registerSeoForPage($page);

        $this->currentSite = $page->site;

        //Template Name considerando busca, categoria, etc..
        if (($this->viewData['search'] ?? false) && !empty($this->viewData['searchQuery'] ?? null)) {
            $this->templateNamePrefix .= '@search=' . Str::slug($this->viewData['searchQuery']);
        }

        if ($this->viewData['category'] ?? null) {
            $this->templateNamePrefix .= '@category=' . $this->viewData['category']->id;
        }


        $useCache = $this->currentSite->config->performance->enable_advanced_cache ?? false;

        //Debugbar::debug($this->templateNamePrefix, $useCache);

        if ($useCache) {
            $cache = $this->getFromCache($this->templateNamePrefix);

            Debugbar::debug($cache);
            if (!empty($cache)) {
                return $cache;
            }
        }
        else {
            $this->purgeCache($this->templateNamePrefix);
        }

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

        $category = $this->viewData['category'] ?? null;
        //dd($category);

        if ($category) {
            $this->registerFrontendSeo([
                                           'title'        => $category->title,
                                           'title_prefix' => '{{ site.seoTitle() }} - {{ page.seoTitle() }}',
                                       ]);

            /*if($this->viewData['breadcrumb'] ?? false){
                $this->viewData['breadcrumb']->items->add('')
            }*/

        }


        //$pageContent = $page->page_template ? "{{ include('@template/index.twig') }}" : '';


        $this->templates->put('page', $this->getPageBaseTemplate($pageContent));


        $renderedTemplate = $this->renderTwig('page');


        //$renderedBlade = Blade::render($rawBlade, $this->viewData);

        if ($this->currentSite->config->performance->enable_html_minify) {
            $htmlMin = new HtmlMin();

            $renderedTemplate = $htmlMin->minify($renderedTemplate);
        }

        if ($useCache) {
            $this->saveCache($this->templateNamePrefix, $renderedTemplate);
        }

        //dd($this->viewData['articles']->first()->description);

        return $renderedTemplate;
    }

    /**
     * @throws FrontendException
     */
    public function article(Article $article): string
    {
        $page = $article->page;
        $this->templateNamePrefix = "@page={$page->public_id}@article={$article->public_id}";


        $useCache = $page->site->config->performance->enable_advanced_cache ?? false;

        //Debugbar::debug($this->templateNamePrefix, $useCache);

        if ($useCache) {
            $cache = $this->getFromCache($this->templateNamePrefix);

            if (!empty($cache)) {
                return $cache;
            }
        }
        else {
            $this->purgeCache($this->templateNamePrefix);
        }

        //$this->setCurrentSite($article->site, false);

        //Meta::registerSeoForArticle($article);
        $this->setViewData($article->getBuildViewData([
                                                          'customPageTemplate' => $page->page_template ? '@template/article.twig' : false,
                                                      ]));

        //dd($this->themeBuild);

        //$this->registerFrontendBuild($article->meta->frontend_build);
        $this->registerFrontendBuild($article->prepareFrontendBuild());

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


        if ($page->site->config->performance->enable_html_minify ?? false) {
            $htmlMin = new HtmlMin();

            $renderedTemplate = $htmlMin->minify($renderedTemplate);
        }

        if ($useCache) {
            $this->saveCache($this->templateNamePrefix, $renderedTemplate);
        }

        return $renderedTemplate;
    }

    /**
     * @throws FrontendException
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function pageInternal(Page $page, PageInternal $pageInternal, $modelItem): string
    {
        /**
         * @var EloquentModelBase|CustomListItem $modelItem
         */

        $this->templateNamePrefix = "@page={$page->public_id}@internal={$pageInternal->public_id}@model=" . (@$modelItem->public_id ?? @$modelItem->slug ?? Str::slug($modelItem?->title ?? ''));

        $pageInternal->breadcrumb_config->background_url = $modelItem->image_url;

        $this->setViewData($pageInternal->page->getBuildViewData([
                                                                     'pageInternal' => $pageInternal,
                                                                     'currentItem'  => $modelItem,
                                                                     'breadcrumb'   => $pageInternal->breadcrumb([
                                                                                                                     ...$pageInternal->page->breadcrumb->items->toArray(),
                                                                                                                     '#' => $modelItem->title,
                                                                                                                 ]),
                                                                 ]));

        if ($pageInternal->frontend_build ?? false) {
            $this->registerFrontendBuild($pageInternal->frontend_build);
        }
        /*if ($modelItem->schema->frontend_build ?? false) {
            $this->registerFrontendBuild($modelItem->schema->frontend_build);
        }*/

        //$this->frontendBuild->meta->registerSeoForPage($page);

        $this->registerFrontendSeo([
                                       'title'        => $modelItem->title,
                                       'title_prefix' => '{{ site.seoTitle() }} - {{ page.seoTitle() }}',
                                       'published_at' => $modelItem->created_at,
                                       'updated_at'   => $modelItem->updated_at,
                                   ]);

        if ($modelItem->seo ?? null) {
            $this->registerFrontendSeo(array_filter($modelItem->seo->toArray()));
        }


        $this->currentSite = $pageInternal->page->site;


        $useCache = $this->currentSite->config->performance->enable_advanced_cache ?? false;

        //Debugbar::debug($this->templateNamePrefix, $useCache);

        if ($useCache) {
            $cache = $this->getFromCache($this->templateNamePrefix);

            if (!empty($cache)) {
                return $cache;
            }
        }
        else {
            $this->purgeCache($this->templateNamePrefix);
        }

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

        if ($this->currentSite->config->performance->enable_html_minify) {
            $htmlMin = new HtmlMin();

            $renderedTemplate = $htmlMin->minify($renderedTemplate);
        }

        if ($useCache) {
            $this->saveCache($this->templateNamePrefix, $renderedTemplate);
        }

        return $renderedTemplate;
    }
}
