<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Templates\Global\Widgets;


use Adminx\Common\Models\Templates\Global\Abstract\AbstractTemplate;

class CustomFormWidgetTemplate extends AbstractTemplate
{

    protected $attributes = [
        'public_id'   => 'custom-form',
        'title'       => 'Formulário Personalizado',
        'description' => 'Renderiza um formulário criado na plataforma.',
        'morphs'      => ['widgets', 'site_widgets'],
        'path'        => 'widgets/dynamic-content/forms',
        'config'      => [
            'render_engine'  => 'blade',
            'use_files'      => true,
            'require_source' => true,
            'source_types'   => ['form'],
        ],
    ];

}