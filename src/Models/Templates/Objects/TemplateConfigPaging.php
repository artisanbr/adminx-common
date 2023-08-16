<?php
namespace Adminx\Common\Models\Templates\Objects;

use ArtisanLabs\GModel\GenericModel;

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
