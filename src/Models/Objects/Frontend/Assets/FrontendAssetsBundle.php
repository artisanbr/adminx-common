<?php
namespace Adminx\Common\Models\Objects\Frontend\Assets;

use Adminx\Common\Models\Objects\Frontend\FrontendHtmlObject;
use ArtisanLabs\GModel\GenericModel;

class FrontendAssetsBundle extends GenericModel
{

    protected $fillable = [
        'js',
        'css',
        'head_script',
    ];

    protected $casts = [
        'js' => FrontendJsAssets::class,
        'css' => FrontendCssAssets::class,
        'head_script' => FrontendHtmlObject::class,
    ];
}
