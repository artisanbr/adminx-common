<?php

namespace Adminx\Common\Models\Objects\Abstract;

use ArtisanLabs\GModel\GenericModel;

/**
 * @property string $html
 * @property string $minify
 */
abstract class AbstractHtmlObject extends GenericModel
{

    protected $fillable = [
        'html',
        'minify',
        //'elements',
        //'use_elements',
    ];

    protected $casts = [
        //'elements' => 'collection',
        //'use_elements' => 'bool',
        'html'   => 'string',
        'minify' => 'string',
    ];

    protected $attributes = [
        'html' => '',
    ];

}
