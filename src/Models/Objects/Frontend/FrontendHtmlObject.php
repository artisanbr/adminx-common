<?php
namespace Adminx\Common\Models\Objects\Frontend;


use Adminx\Common\Libs\FrontendEngine\AdvancedHtmlEngine;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Objects\Abstract\AbstractHtmlObject;
use Adminx\Common\Models\Site;
use ArtisanLabs\GModel\GenericModel;

class FrontendHtmlObject extends AbstractHtmlObject
{

    /*protected $fillable = [
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
    ];*/

    //region HELPERS
    public function builtHtml(Site $site, HtmlModel $model, $viewTemporaryName = 'element-html'): string
    {
        return AdvancedHtmlEngine::start($site, $model, $viewTemporaryName)->html($this->html)->buildHtml();
    }

}
