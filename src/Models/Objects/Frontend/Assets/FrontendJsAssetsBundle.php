<?php
namespace Adminx\Common\Models\Objects\Frontend\Assets;

use Adminx\Common\Models\Objects\Frontend\FrontendHtmlObject;
use ArtisanLabs\GModel\GenericModel;

class FrontendJsAssetsBundle extends GenericModel
{

    protected $fillable = [
        'head',
        'before_body',
        'after_body',
    ];

    protected $casts = [
        'head' => FrontendJsAssets::class,
        'before_body' => FrontendJsAssets::class,
        'after_body' => FrontendJsAssets::class,
    ];

    /*protected $attributes = [
        'head' => [],
        'before_body' => [],
        'after_body' => [],
    ];*/

    public function minify(){
        $this->head->minify();
        $this->before_body->minify();
        $this->after_body->minify();

        return $this;
    }
}
