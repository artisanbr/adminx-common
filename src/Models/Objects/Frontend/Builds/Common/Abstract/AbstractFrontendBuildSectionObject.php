<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Builds\Common\Abstract;

use ArtisanBR\GenericModel\Model as GenericModel;

/**
 * @property string $html
 * @property string $minify
 */
abstract class AbstractFrontendBuildSectionObject extends GenericModel
{

    protected $fillable = [
        'before',
        'after',
    ];

    protected $casts = [
        'before'   => 'string',
        'after' => 'string',
    ];

    protected $attributes = [
        'before'   => '',
        'after' => '',
    ];



    public function addBefore($html){
        $this->before .= $html;
    }

    public function addAfter($html){
        $this->after .= $html;
    }

}
