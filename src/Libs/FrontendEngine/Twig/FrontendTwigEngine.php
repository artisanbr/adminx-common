<?php

namespace Adminx\Common\Libs\FrontendEngine\Twig;

use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Facades\Frontend\FrontendPage;
use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Libs\FrontendEngine\FrontendEngineBase;
use Adminx\Common\Libs\FrontendEngine\Twig\Extensions\FrontendTwigExtension;
use Adminx\Common\Models\Article;
use Adminx\Common\Models\CustomLists\Abstract\CustomListBase;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\Menu;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Widgets\SiteWidget;
use Adminx\Common\Models\Templates\Global\Manager\Facade\PageTemplateManager;
use Adminx\Common\Models\Themes\Theme;
use Adminx\Common\Models\Themes\ThemeBuild;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Blade;
use JetBrains\PhpStorm\NoReturn;
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

        $this->twigFileLoader->addPath(PageTemplateManager::globalTemplatesPath('base'), 'base');
        $this->twigFileLoader->addPath(PageTemplateManager::globalTemplatesPath('pages'), 'pages');

        /*if ($this->currentPage) {
            $arrayTemplates[$this->getTwigTemplateName($mainViewName)] = $this->getPageBaseTemplate();

            //$this->twigFileLoader->addPath(PageTemplateManager::globalTemplatesPath('widgets'), 'widgets');

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

        $this->setFrontendBuild($page->frontendBuild());

        $this->frontendBuild->meta->registerSeoForPage($page);

        $this->currentSite = $page->site;

        if ($this->currentSite->theme ?? false) {
            $this->applyTheme($this->currentSite->theme);
        }

        $pageTemplate = $page->page_template;
        if ($pageTemplate) {
            //dd($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id));
            $this->twigFileLoader->addPath($pageTemplate->template->getTemplateGlobalFile($pageTemplate->template->public_id), 'template');
        }


        $pageContent = $page->page_template ? "{{ include('@template/index.twig') }}" : '';

        $pageContent .= $page->html;

        $this->templates->put('page', $this->getPageBaseTemplate($pageContent));


        $renderedTemplate = $this->renderTwig('page');


        //$renderedBlade = Blade::render($rawBlade, $this->viewData);

        if ($this->currentSite->config->enable_html_minify) {
            $htmlMin = new HtmlMin();

            $renderedTemplate = $htmlMin->minify($renderedTemplate);
        }

        return $renderedTemplate;
    }

    public function getPageBaseTemplate(string $content): string
    {

        $headHtml = $this->themeBuild->head;
        $headerHtml = $this->themeBuild->header;
        $footerHtml = $this->themeBuild->footer;

        return <<<html
                <html lang="pt-BR">
                    <head>
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
}
