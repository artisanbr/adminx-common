<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Templates\Objects;

use ArtisanBR\GenericModel\Model as GenericModel;

class TemplateConfigPaging extends GenericModel
{

    protected $fillable = [
        'enable',
        'max_page_items',
        'max_pages',
    ];

    protected $attributes = [
        'enable' => false,
        'max_page_items' => 10,
        'max_pages' => 1,
    ];

    protected $casts = [
        'enable' => 'boolean',
        'max_page_items' => 'int',
        'max_pages' => 'int'
    ];

    /*protected function getHasCustomPermissionsAttribute(){
        return $this->custom_permissions;
    }*/
}
