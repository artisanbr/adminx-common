<?php

namespace ArtisanBR\Adminx\Common\App\Libs\FrontendEngine;

use ArtisanBR\Adminx\Common\App\Facades\FrontendSite;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\HtmlModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\WidgeteableModel;
use ArtisanBR\Adminx\Common\App\Models\Page;
use ArtisanBR\Adminx\Common\App\Models\Site;
use ArtisanBR\Adminx\Common\App\Models\Widgeteable;
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

class FrontendHtmlEngine extends FrontendEngineBase
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection|mixed|Widgeteable[]
     */
    protected mixed $widgeteables;

    protected ArrayLoader $twigLoader;

    protected string $rawHtml;

    protected array $customViewData = [];

    protected Environment $twig;
    /**
     * @var \Illuminate\Database\Eloquent\Collection|mixed
     */
    protected mixed $menus;

    public array $viewData = [];

    public function __construct(
        public Site|null  $currentSite = null,
        public string     $viewTemporaryName = 'temp-html'
    )
    {
        if (!$this->currentSite) {
            $this->setCurrentSite(FrontendSite::current());
        }

        $this->viewData = [
            'site' => $this->currentSite,
        ];

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

        $this->twig->addFunction(new TwigFunction('widget', function (string $public_id){
            return $this->widget($public_id);
        }));

        return $this;
    }

    public function setCurrentSite(Site $site): static
    {
        $this->currentSite = $site;
        $this->widgeteables = $this->currentSite->widgeteables;
        $this->menus = $this->currentSite->menus;

        return $this;
    }

    public function setViewName(string $viewTemporaryName = 'temp-html'): static
    {
        $this->viewTemporaryName = $viewTemporaryName;

        return $this;
    }

    public function page(Page $page): self
    {
        $this->viewData = $page->getBuildViewData();

        return $this;
    }

    public function html($rawHtml): static
    {
        $this->rawHtml = $rawHtml;

        $this->startTwig();

        return $this;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    public function render(): string
    {
        if(!$this->twig){
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


        $widget = $this->widgeteables->firstWhere('public_id', $widgeteable_public_id);

        if (!$widget) {
            return "Widget {$widgeteable_public_id} não encontrado";
        }

        //Widget View
        $widgeteableView = View::make(($widget->config->ajax_render ?? true) ? 'adminx-common::Elements.Widgets.renders.ajax-render' : 'adminx-common::Elements.Widgets.renders.static-render', [
            'widgeteable' => $widget,
        ]);

        return $widgeteableView->render();

    }

    public function menu($menuSlug)
    {

        $menu = $this->menus->firstWhere('slug', $menuSlug);

        return $menu->html ?? 'Menu não encontrado';

    }
}
