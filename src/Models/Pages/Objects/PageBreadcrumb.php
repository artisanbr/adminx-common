<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Objects;

use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use ArtisanLabs\GModel\GenericModel;

class PageBreadcrumb extends GenericModel
{

    protected $fillable = [
        'config',
        'items',
        'background_url',
    ];

    protected $casts = [
        'config' => BreadcrumbConfig::class,
        'items'  => 'collection',
    ];

    protected $attributes = [
        'config' => [],
        'items'  => ['/' => 'Home'],
    ];

    protected $appends = [
        'enabled',
        'title',
    ];

    //region Attributes
    //region GET's
    protected function getEnabledAttribute()
    {
        return $this->config->enable ?? false;
    }

    protected function getTitleAttribute()
    {
        return $this->items->last() ?? false;
    }

    protected function getBackgroundAttribute()
    {
        return $this->config->background ?? false;
    }

    protected function getBackgroundUrlAttribute()
    {
        return $this->config->background->url ?? false;
    }

    protected function getCssStyleAttribute()
    {

        $style = '';

        if ($this->config->height ?? false) {
            $style .= "height: {$this->config->height}px;";
        }
        if (!empty($this->background_url)) {
            $style .= "background-image: url('{$this->background_url}');";
        }

        return $style;
    }
    //endregion

    //region SET's
    //protected function setAttribute(){}
    protected function setBackgroundUrlAttribute($value)
    {
        $this->config->background->url = $value;

        return $this;
    }

    //endregion
    //endregion
}
