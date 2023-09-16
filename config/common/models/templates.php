<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

use Adminx\Common\Models\Templates\Global\Pages\BlogRightSidebarTemplate;
use Adminx\Common\Models\Templates\Global\Pages\BlogSimplePageTemplate;
use Adminx\Common\Models\Templates\Global\Widgets\CustomFormWidgetTemplate;

return [
    //Pages
    'blog-simple' => BlogSimplePageTemplate::class,
    'blog-right-sidebar' => BlogRightSidebarTemplate::class,

    //Widgets
    'custom-form' => CustomFormWidgetTemplate::class,
];
