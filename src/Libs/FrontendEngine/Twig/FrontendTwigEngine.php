<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
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
use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Models\Templates\Global\Manager\Facade\GlobalTemplateManager;
use Adminx\Common\Models\Themes\Theme;
use Adminx\Common\Models\Themes\ThemeBuild;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Blade;
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
use Twig\TwigFilter;
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

        //dd($this->frontendBuild->seo);


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

        $this->addViewData([
                               'theme' => $theme,
                           ]);

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

        $this->twig->addFilter(new TwigFilter('entity_decode', 'html_entity_decode'));


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
            if (env('APP_ENV') == 'local') {
                Debugbar::enable();
                dump($template, $this->viewData, $this->templates->toArray());
            }
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

        if ($exception->getCode() === 404) {
            Debugbar::disable();
        }
        else if (config('app.env') === 'local') {
            Debugbar::enable();
        }

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

    public function getFromCache($name)
    {
        return Cache::has("site-cache:{$name}") ? Cache::get("site-cache:{$name}") : false;
    }

    public function saveCache($name, $content)
    {
        return Cache::remember("site-cache:{$name}", now()->addHours($this->cacheHours), value($content));
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

        $this->currentSite = FrontendSite::current() ?? $page->site;
        $this->applyTheme(FrontendSite::currentTheme() ?? $this->currentSite->theme);

        $this->addViewData($this->currentSite->getBuildViewData($page->getBuildViewData($mergeData)));

        //$urlCategory = Route:

        /*if ($page->pageable_id && $page->pageable) {
            $this->addPageableViewData($page);
        }*/


        //$this->registerFrontendBuild($page->frontend_build);
        $this->registerFrontendBuild($page->prepareFrontendBuild());

        //$this->frontendBuild->meta->registerSeoForPage($page);


        //Template Name considerando busca, categoria, etc..
        if (($this->viewData['search'] ?? false) && !empty($this->viewData['searchQuery'] ?? null)) {
            $this->templateNamePrefix .= '@search=' . Str::slug($this->viewData['searchQuery']);
        }

        if ($this->viewData['category'] ?? null) {
            $this->templateNamePrefix .= '@category=' . $this->viewData['category']->id;
        }


        $useCache = $this->cacheEnabled($this->currentSite);

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


        $pageTemplate = $page->page_template;

        //dd($page, $pageTemplate);

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

        if ($this->minifyEnabled($this->currentSite)) {
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

        $this->currentSite = FrontendSite::current() ?? $page->site;
        $this->applyTheme(FrontendSite::currentTheme() ?? $this->currentSite->theme);

        $useCache = $this->cacheEnabled($this->currentSite);

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

        if ($page->config->captcha->enabled ?? false) {
            $this->addViewData([
                                   'recaptcha' => Blade::render(<<<'blade'
<x-common::recaptcha-v2 :site-key="$page->config->captcha->keys->get('site_key') ??  $site->config->recaptcha_site_key"/>
blade, [
                                                                                                                                                                                                    'page' => $page,
                                                                                                                                                                                                    'site' => $this->currentSite,
                                                                                                                                                                                                ]),
                               ]);
        }

        //Meta::registerSeoForArticle($article);
        $this->addViewData($this->currentSite->getBuildViewData($article->getBuildViewData([
                                                                                               'customPageTemplate' => $page->page_template ? '@template/article.twig' : false,
                                                                                           ])));

        //dd($this->themeBuild);

        //$this->registerFrontendBuild($article->meta->frontend_build);
        $this->registerFrontendBuild($article->prepareFrontendBuild());

        $this->registerFrontendSeo([
                                       'title'        => $article->title,
                                       'title_prefix' => $this->currentSite->seoTitle($page->title),
                                       'published_at' => $article->published_at,
                                       'updated_at'   => $article->updated_at,
                                   ]);

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


        if ($this->minifyEnabled($this->currentSite)) {
            $htmlMin = new HtmlMin();

            $renderedTemplate = $htmlMin->minify($renderedTemplate);
        }

        if ($useCache) {
            $this->saveCache($this->templateNamePrefix, $renderedTemplate);
        }

        return $renderedTemplate;
    }

    public function pageable(Page $page, $pageable, $currentItem, $viewData = []): string
    {
        /**
         * @var EloquentModelBase|CustomListItem $currentItem
         */

        $this->templateNamePrefix = "@page={$page->public_id}@pageable={$pageable->public_id}@model=" . (@$currentItem->public_id ?? @$currentItem->slug ?? Str::slug($currentItem?->title ?? ''));

        $this->currentSite = FrontendSite::current() ?? $page->site;

        if($page->parent_id) {
            $page->load(['parent']);
        }

        if (blank($page->breadcrumb_config->background_url ?? null) && !blank($currentItem->image_url ?? null)) {
            $page->breadcrumb_config->background_url = $currentItem->image_url;
        }


        if (blank($page->title ?? null)) {
            $page->title = $page->parent?->title ?? $currentItem->title;
        }

        $breadcrumb = [];

        if ($page->parent_id) {
            $breadcrumb += $page->parent?->breadcrumb->items->toArray() ?? [];
        }

        $breadcrumb += ['#' => $currentItem->title];


        if ($page->frontend_build ?? false) {
            $this->registerFrontendBuild($page->frontend_build);
        }

        $this->registerFrontendSeo([
                                       'title'         => $this->currentSite->seoTitle($page->title . ' - ' . $currentItem->title),
                                       'title_prefix'  => null,
                                       'published_at'  => $currentItem->created_at,
                                       'updated_at'    => $currentItem->updated_at,
                                       'document_type' => 'website',
                                   ]);

        if (!(blank($currentItem->first_image_url ?? null))) {
            $this->registerFrontendSeo([
                                           'image_url' => $this->currentSite->uriTo($currentItem->first_image_url, false),
                                       ]);
        }


        $this->registerFrontendSeo([
                                       'description' => match (true) {
                                           !blank($currentItem->description ?? null) => $currentItem->description,
                                           !blank($page->seo->description ?? null) => $page->seo->description,
                                           $page->parent_id && !blank($page->parent?->seo->description ?? null) => $page->parent?->seo->description,
                                           !blank($this->currentSite->seo->description ?? null) => $this->currentSite->seo->description,
                                           default => null,
                                       },
                                   ]);

        $this->registerFrontendSeo([
                                       'keywords' => match (true) {
                                           !blank($page->seo->keywords ?? null) => $page->seo->keywords,
                                           $page->parent_id && !blank($page->parent?->seo->keywords ?? null) => $page->parent?->seo->keywords,
                                           !blank($this->currentSite->seo->keywords ?? null) => $this->currentSite->seo->keywords,
                                           default => null,
                                       },
                                   ]);

        if ($currentItem->seo ?? null) {
            $this->registerFrontendSeo(array_filter($currentItem->seo->toArray()));
        }


        $useCache = $this->cacheEnabled($this->currentSite);


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
            $this->applyTheme(FrontendSite::currentTheme() ?? $this->currentSite->theme);
        }

        /*dd([
               ...$page->getBuildViewData(),
               ...$viewData,
               'pageable'     => $pageable,
               'currentItem'  => $currentItem,
               'current_item' => $currentItem,
               'breadcrumb'   => $page->breadcrumb($breadcrumb),
           ]);*/

        $this->addViewData([
                               ...$page->getBuildViewData(),
                               ...$viewData,
                               'pageable'     => $pageable,
                               'currentItem'  => $currentItem,
                               'current_item' => $currentItem,
                               'breadcrumb'   => $page->breadcrumb($breadcrumb),
                           ]);

        //$this->addViewData();


        $pageContent = $page->content;


        $this->templates->put('page', $this->getPageBaseTemplate($pageContent));


        $renderedTemplate = $this->renderTwig('page');


        //$renderedBlade = Blade::render($rawBlade, $this->viewData);

        if ($this->minifyEnabled($this->currentSite)) {
            $htmlMin = new HtmlMin();

            $renderedTemplate = $htmlMin->minify($renderedTemplate);
        }

        if ($useCache) {
            $this->saveCache($this->templateNamePrefix, $renderedTemplate);
        }

        return $renderedTemplate;
    }


    protected function cacheEnabled(Site $site): bool
    {

        return session()->get(
            $site->public_id . '__' . 'enabledAdvancedCache',
            $site->config->performance->enable_advanced_cache
        );

    }

    protected function minifyEnabled(Site $site): bool
    {

        return session()->get(
            $site->public_id . '__' . 'enabledHtmlMinfy',
            $site->config->performance->enable_html_minify
        );

    }

}
