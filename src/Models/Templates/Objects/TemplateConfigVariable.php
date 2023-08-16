<?php
namespace Adminx\Common\Models\Templates\Objects;

use Adminx\Common\Enums\Widgets\WidgetConfigVariableType;
use ArtisanLabs\GModel\GenericModel;

class TemplateConfigVariable extends GenericModel
{

    protected $fillable = [
        'title',
        'description',
        'slug',
        'type',
        'value',
        'options',
        'default_value',
        'required',
    ];

    protected $attributes = [
        'type' => 'text.field',
        'required' => false,
        'options' => [],
    ];

    protected $casts = [
        'title' => 'string',
        'description' => 'string',
        'slug' => 'string',
        'type' => WidgetConfigVariableType::class,
        'value' => 'string',
        'default_value' => 'string',
        'required' => 'boolean',
        'options' => 'collection'
    ];

    /*protected function getHasCustomPermissionsAttribute(){
        return $this->custom_permissions;
    }*/
}
