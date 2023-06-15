<?php

namespace Adminx\Common\Models\Objects\Frontend\Builds;

use Adminx\Common\Models\Objects\Frontend\Builds\Common\FrontendBuildBodyObject;
use Adminx\Common\Models\Objects\Frontend\Builds\Common\FrontendBuildHeadObject;
use ArtisanLabs\GModel\GenericModel;

/**
 * @property string $html
 * @property string $minify
 */
class FrontendBuildObject extends GenericModel
{

    protected $fillable = [
        'lang',
        'head',
        'body',
    ];

    protected $casts = [
        'lang'   => 'string',
        'head'   => FrontendBuildHeadObject::class,
        'body' => FrontendBuildBodyObject::class,
    ];

    protected $attributes = [
        /*body' => [],*/
    ];

    protected function getLangAttribute(){
        return $this->attributes['lang'] ?? str_replace('_', '-', app()->getLocale());
    }

}
