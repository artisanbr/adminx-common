<?php

namespace Adminx\Common\Models\Objects\Abstract\Frontend\Builds;

use ArtisanLabs\GModel\GenericModel;

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
