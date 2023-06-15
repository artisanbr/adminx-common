<?php
namespace Adminx\Common\Models\Pages\Objects;

use Adminx\Common\Models\Objects\Frontend\FrontendHtmlObject;
use ArtisanLabs\GModel\GenericModel;

class PageContent extends GenericModel
{

    protected $fillable = [
        'main',
        'internal',
    ];

    protected $casts = [
        'main' => FrontendHtmlObject::class,
        'internal' => FrontendHtmlObject::class,
    ];

    protected $attributes = [
        //'main' => [],
        //'internal' => [],
    ];
}
