<?php

namespace Adminx\Common\Libs\FrontendEngine\Twig\Extensions;

use Adminx\Common\Facades\Frontend\FrontendSite;
use Adminx\Common\Models\CustomLists\Abstract\CustomListBase;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\Menu;
use Adminx\Common\Models\Site;
use Adminx\Common\Models\Widgets\SiteWidget;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FrontendTwigExtension extends AbstractExtension
{
    /**
     * @var Collection|SiteWidget[]|Builder
     */
    protected array|Collection $widgets;

    protected ?SiteWidget $currentSiteWidget = null;

    //protected Site $currentSite;


    /**
     * @var Collection|mixed|Menu[]
     */
    protected mixed $menus;
    /**
     * @var Collection|mixed|CustomListBase[]
     */
    protected mixed $customLists;

    public function __construct(protected Environment $twig, protected ?Site $currentSite = null)
    {
        if (!$this->currentSite) {
            $this->currentSite = FrontendSite::current();
        }
        $this->widgets = collect();

        $this->menus = collect();
        $this->customLists = collect();

    }

    public function getFunctions()
    {
        return [
            new TwigFunction('widget', [$this, 'widget'], ['needs_context' => true]),
            new TwigFunction('menu', [$this, 'menu'], ['needs_context' => true]),
            new TwigFunction('custom_list', [$this, 'customList'], ['needs_context' => true]),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('widget', [$this, 'widget'], ['needs_context' => true]),
            new TwigFilter('menu', [$this, 'menu'], ['needs_context' => true]),
            new TwigFilter('custom_list', [$this, 'customList'], ['needs_context' => true]),
        ];
    }


    /**
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function widget($context, $widget_public_id): string
    {

        if (!$this->currentSiteWidget || $this->currentSiteWidget->public_id != $widget_public_id) {
            //Não foi o ultimo utilizado, Verificar no cache
            $this->currentSiteWidget = $this->widgets->firstWhere('public_id', $widget_public_id) ?? $this->widgets->firstWhere('slug', $widget_public_id);

            //Não encontrou, buscar no banco
            if (!$this->currentSiteWidget) {
                $this->currentSiteWidget = $this->currentSite->widgets()->where('public_id', $widget_public_id)->orWhere('slug', $widget_public_id)->first();

                if ($this->currentSiteWidget) {
                    $this->widgets->add($this->currentSiteWidget);
                }
            }

            //Se não encontrar parar aqui.
            if (!$this->currentSiteWidget) {
                return "Widget '{$widget_public_id}' não encontrado";
            }
        }


        //Se estiver sem content, compilar
        /*if (empty($this->currentSiteWidget->content->html)) {
            $this->currentSiteWidget->save();
        }*/

        if (!$this->currentSiteWidget->config->ajax_render) {

            $templateContent = $this->currentSiteWidget->template?->id ? $this->currentSiteWidget->template_content : $this->currentSiteWidget->content->html;

            $template = $this->twig->createTemplate($templateContent, "widget-{$this->currentSiteWidget->public_id}");

            return $template->render($this->currentSiteWidget->getViewRenderData());
        }

        return $this->currentSiteWidget->content->html;

    }

    public function menu($context, $menuSlug)
    {

        //Verificar no cache
        $menu = $this->menus->firstWhere('slug', $menuSlug); //todo: public_id

        //Não encontrou, buscar no banco
        if (!$menu) {
            $menu = $this->currentSite->menus()->where('slug', $menuSlug)->first();

            if ($menu) {
                $this->menus->add($menu);
            }
        }

        //$menu = $this->menus->firstWhere('slug', $menuSlug);

        return $menu->html ?? 'Menu não encontrado';

    }

    public function customList($context, $public_id)
    {

        //Verificar no cache
        $customList = $this->customLists->firstWhere('public_id', $public_id) ?? $this->customLists->firstWhere('slug', $public_id);

        //Não encontrou, buscar no banco
        if (!$customList) {

            $customList = $this->currentSite->lists()->where('public_id', $public_id)->orWhere('slug', $public_id)->first();

            if ($customList) {
                $customList = $customList->mountModel();
                $this->customLists->add($customList);
            }
        }


        return $customList ?? new CustomList();

    }
}