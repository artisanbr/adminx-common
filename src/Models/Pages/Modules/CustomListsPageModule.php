<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Modules;

use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\Pages\Modules\Abstract\AbstractPageModule;

class CustomListsPageModule extends AbstractPageModule
{

    public string $moduleRelatedModel = CustomList::class;

    protected $attributes = [
        'title'       => 'Listas Personalizadas',
        'description' => 'Módulo de Formulários',
        'slug'        => 'lists',
    ];


}