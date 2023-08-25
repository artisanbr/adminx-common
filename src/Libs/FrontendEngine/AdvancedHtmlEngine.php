<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FrontendEngine;

use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Pages\Page;
use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Models\Widgets\SiteWidget;
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

/**
 * @property Page $model
 */
class AdvancedHtmlEngine
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection|mixed|SiteWidget[]
     */
    protected mixed       $widgeteables;
    protected ArrayLoader $twigLoader;
    protected string      $rawHtml;
    protected array       $customViewData = [];
    protected array       $viewData = [];
    protected Environment $twig;
    /**
     * @var \Illuminate\Database\Eloquent\Collection|mixed
     */
    protected mixed $menus;

    public function __construct(
        public Site             $site,
        public HtmlModel $model,
        public string           $viewTemporaryName
    ) {
        $this->widgeteables = $this->site->widgeteables;
        $this->menus = $this->site->menus;


    }

    public static function start(Site $site, HtmlModel $model, string $viewTemporaryName = "temp-html"): self
    {
        $site->load(['theme','widgeteables','menus']);
        return (new self($site, $model, $viewTemporaryName));
    }

    public function mount(array $customViewData = [], $html = null): static
    {

        if($customViewData){
            $this->customViewData($customViewData);
        }

        if($html){
            $this->html($html);
        }

        return $this;
    }

    /**
     * Prepare Twig Filters and Functions
     * @return void
     */
    protected function enableFilters($enable = true){

        //Menu
        $this->twig->addFilter(new TwigFilter('menu', function (string $slug) use($enable) {
            if ($enable) {
                return $this->menu($slug);
            }

            return "{{ '{$slug}'|menu }}";
        }));

        $this->twig->addFunction(new TwigFunction('menu', function (string $slug) use($enable) {
            if ($enable) {
                return $this->menu($slug);
            }

            return "{{ menu('{$slug}') }}";
        }));

        //Widget
        $this->twig->addFilter(new TwigFilter('widget', function (string $public_id) use($enable) {
            if ($enable) {
                return $this->widget($public_id);
            }

            return "{{ '{$public_id}'|widget }}";
        }));

        $this->twig->addFunction(new TwigFunction('widget', function (string $public_id) use($enable) {
            if ($enable) {
                return $this->widget($public_id);
            }

            return "{{ widget('{$public_id}') }}";
        }));
    }

    public function html($rawHtml): static
    {
        $this->rawHtml = $rawHtml;

        $this->twigLoader = new ArrayLoader([
                                                $this->viewTemporaryName => $this->rawHtml,
                                            ]);

        $this->twig = new Environment($this->twigLoader, [
            'auto_reload' => true,
            'autoescape'  => false,
            'debug'       => true,
        ]);

        $this->twig->addExtension(new DebugExtension());


        return $this;
    }

    public function customViewData($customViewData = []): static
    {
        $this->customViewData = $customViewData ?? [];

        $this->viewData = array_merge($this->viewData ?? [], $this->customViewData ?? []);

        return $this;
    }

    /**
     * Montar HTML Completo
     */
    public function buildHtml(array $customViewData = [], $html = null): string
    {
        $this->mount($customViewData, $html);
        $this->enableFilters();

        $this->viewData['site'] = $this->site;
        $this->viewData['currentPage'] = $this->model;

        if(get_class($this->model) === Page::class && $this->model->config->sources->count()){

            $sourceData = [];

            foreach ($this->model->config->sources as $source){
                $sourceData[$source->name] = $source->data;

                /*if($this->model->config->isUsingModule('internal_pages')){
                    foreach($sourceData[$source->name]->items as $dataItem){
                        $dataItem->internal_url = $this->model->internalUrl($dataItem, $source->internal_url);
                    }
                }*/
            }

            $this->viewData['data'] = $sourceData;
        }

        return $this->getHTmlOutput($this->viewTemporaryName, collect($this->viewData)->merge($this->customViewData)->toArray());
    }

    /**
     * Montar Cache para armazenamento
     *
     * @param array $customViewData
     * @param $html
     *
     * @return string
     */
    public function getCache(array $customViewData = [], $html = null): string
    {
        $this->mount($customViewData, $html);
        $this->enableFilters(false);

        return $this->getHTmlOutput($this->viewTemporaryName, $this->customViewData ?? []);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    protected function getHTmlOutput($viewName, $viewData){
        try {
            return $this->twig->render($viewName, $viewData);
        } catch (\Exception $e) {
            dump($this->model,$viewName, $viewData, $this->rawHtml);
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
