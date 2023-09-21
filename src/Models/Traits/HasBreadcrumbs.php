<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Configs\BreadcrumbConfig;
use Adminx\Common\Models\Pages\Objects\PageBreadcrumb;
use Adminx\Common\Models\Pages\Objects\PageConfig;
use Adminx\Common\Models\Pages\Objects\PageInternalConfig;

/**
 * @var EloquentModelBase                  $this
 * @property PageConfig|PageInternalConfig $config
 */
trait HasBreadcrumbs
{

    public ?PageBreadcrumb $breadcrumbCache = null;

    public function breadcrumb(array $mergeItems = [], ?BreadcrumbConfig $config = null): PageBreadcrumb
    {

        if (!$this->breadcrumbCache) {
            $breadcrumbConfig = $config ?? $this->breadcrumb_config;
            $breadcrumbItems = [@$this->breadcrumb_items, ...$this->getSelfBreadcrumbItem(), ...$mergeItems];

            $this->setBreadcrumb(collect($breadcrumbItems)->filter()->toArray(), $breadcrumbConfig);
        }

        return $this->breadcrumbCache;
    }

    public function setBreadcrumb(?array $items = null, ?BreadcrumbConfig $config = null): self
    {

        $breadcrumbConfig = $config ?? new BreadcrumbConfig();

        $this->breadcrumbCache = new PageBreadcrumb([
                                                        'config' => $breadcrumbConfig->toArray(),
                                                        'items'  => ['/' => 'Home', ...($items ?? [])],
                                                    ]);

        return $this;
    }

    protected function getBreadcrumbAttribute(): PageBreadcrumb
    {
        return $this->breadcrumb();
    }

    protected function getBreadcrumbConfigAttribute()
    {
        return $this->config->breadcrumb ?? null;
    }

    protected function getSelfBreadcrumbItem(): array
    {
        return $this->title ? [$this->uri ?? $this->url ?? '#' => $this->title] : [];
    }

    protected function getShowBreadcrumbAttribute(): bool
    {
        return $this->breadcrumb_config?->enable ?? false;
    }


}
