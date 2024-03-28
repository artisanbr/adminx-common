<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Modules\Abstract;

use Adminx\Common\Models\Traits\HasSelect2;
use ArtisanBR\GenericModel\Model as GenericModel;


abstract class AbstractPageModule extends GenericModel
{
    use HasSelect2;

    public string $moduleName;
    public string $moduleRelatedModel;

    protected $fillable = [
        'title',
        'description',
        'slug',
    ];

    protected $casts = [
        'slug'             => 'string',
        'description'      => 'string',
        'title'            => 'string',
    ];

    /**
     * Allowed Modules in child Pages
     */
    public array $allowedModules;

    /**
     * Auto-enabled Modules in child Pages
     */
    public array $enabledModules;

    protected $attributes = [
        'title'       => 'string',
        'description' => 'string',
        'slug'        => 'string',
    ];

    protected $appends = [
    ];

    //region Attributes
    //region GET's
    /*protected function getConfigAttribute()
    {
        return [
            ...$this->attributes['config'] ?? [],
            'enabled_modules' => $this->enabledModules,
        ];
    }*/
    //endregion

    //region SET's
    //protected function setConfigAttribute(){}

    //endregion
    //endregion

}
