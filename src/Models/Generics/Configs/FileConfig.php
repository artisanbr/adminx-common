<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Generics\Configs;

class FileConfig extends GenericModel
{

    protected $fillable = [
        'is_theme_bundle',
        'theme_bundle_position',
    ];

    protected $attributes = [
        'is_theme_bundle' => false,
    ];

    protected $casts = [
        'is_theme_bundle' => 'bool',
        'theme_bundle_position' => 'int',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

}
