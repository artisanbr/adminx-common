<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Types;

use Adminx\Common\Models\Pages\Types\Abstract\AbstractPageType;

class DynamicPageType extends AbstractPageType
{
    protected $attributes = [
        'slug'        => 'DynamicPage',

        'title'       => 'Página Dinâmica',
        'description' => 'Página com conteúdo dinâmico para itens de uma lista ou outra fonte de dados. Útil para criar páginas internas.',

        'allowed_modules' => ['forms', 'widgets', 'lists', 'advanced_html'],
        'enabled_modules' => ['widgets', 'lists'],
    ];
}