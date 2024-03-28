<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Templates\Global\Abstract;

use Adminx\Common\Models\Templates\Objects\TemplateConfig;
use Adminx\Common\Models\Templates\Template;
use Adminx\Common\Models\Traits\HasSelect2;
use ArtisanBR\GenericModel\GenericModel;

/**
 * @property array{slug: string, description: string, title: string} $attributes
 */
abstract class AbstractTemplate extends GenericModel
{
    use HasSelect2;

    protected $fillable = [
        'title',
        'description',
        'public_id',
        'config',
        'morphs',
        'path',
    ];

    protected $casts = [
        'public_id'        => 'string',
        'description' => 'string',
        'title'       => 'string',
        'path'  => 'string',
        'morphs' => 'array',
        'config' => TemplateConfig::class,
    ];

    //protected $appends = ['views_path'];

    /*protected $attributes = [
        'slug'             => 'string',
        'description'      => 'string',
        'title'            => 'string',
        'path'            => 'string',
    ];*/

    public function templateModel(): Template
    {
        return Template::where([
                                   ['public_id', $this->public_id],
                                   ['global', 1],
                               ])->first() ?? new Template([...$this->attributes, 'global' => true]);
    }

    //region Helpers
    public function getTemplateViewFile($file): string
    {
        return "{$this->path}/{$file}";
    }

    /*public function getTemplateView($view, $data): \Illuminate\Contracts\View\View
    {
        return View::make($this->getTemplateViewFile($view), $data);
    }*/
    //endregion

    //region Attributes
    //region GET's
    /*protected function getPathAttribute()
    {
        $relativePath = $this->attributes['path'];
        return base_path("vendor/artisanbr/adminx-common/resources/templates/{$relativePath}/{$this->public_id}");
    }*/
    //endregion

    //region SET's
    //protected function setAttribute(){}

    //endregion
    //endregion

}
