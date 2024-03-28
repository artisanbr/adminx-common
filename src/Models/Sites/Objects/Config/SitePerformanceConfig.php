<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites\Objects\Config;

class SitePerformanceConfig extends GenericModel
{

    protected $fillable = [
        'enable_simple_cache',
        'enable_advanced_cache',

        'clear_simple_cache',
        'clear_advanced_cache',

        'enable_html_minify',
        'enable_image_optimize',
    ];

    protected $attributes = [
        'enable_simple_cache' => false,
        'enable_advanced_cache' => false,

        'clear_simple_cache' => false,
        'clear_advanced_cache' => false,

        'enable_html_minify' => false,
        'enable_image_optimize' => true,
    ];

    protected $casts = [
        'enable_simple_cache' => 'bool',
        'enable_advanced_cache' => 'bool',

        'clear_simple_cache' => 'bool',
        'clear_advanced_cache' => 'bool',

        'enable_html_minify' => 'bool',
        'enable_image_optimize' => 'bool',
    ];

    //region ATTRIBUTES

    public function clearAll(): static
    {
        $this->attributes = [...$this->attributes, 'clear_simple_cache' => true, 'clear_advanced_cache' => true];

        return $this;
    }

}
