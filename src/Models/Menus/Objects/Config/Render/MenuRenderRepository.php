<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus\Objects\Config\Render;

use Adminx\Common\Models\Casts\AsCollectionOf;
use ArtisanBR\GenericModel\GenericModel;

class MenuRenderRepository extends GenericModel
{

    protected $fillable = [
        'items',
    ];

    protected $casts = [
        'items' => AsCollectionOf::class.':'.MenuRenderConfig::class,
    ];

    protected $attributes = [
        'items' => [],
    ];

    public function getDefault(): MenuRenderConfig {
        return $this->items->where('default', true)->first() ?? MenuRenderConfig::makeDefault();
    }

    public function getBySlug($slug): ?MenuRenderConfig {
        return $this->items->where('slug', $slug)->first();
    }

    public function getByIndex($index): ?MenuRenderConfig {
        return $this->items->get($index);
    }
}
