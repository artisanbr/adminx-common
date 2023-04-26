<?php

namespace Adminx\Common\Models\Bases\Generic;

use Adminx\Common\Libs\FrontendEngine\AdvancedHtmlEngine;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Site;
use ArtisanLabs\GModel\GenericModel;

abstract class GenericHtmlBase extends GenericModel
{
    protected $fillable = [
        'raw',
        'html',
        'html_minified',
    ];

    protected $attributes = [
        'raw'     => '',
        //'html'     => '',
    ];

    protected $casts = [
        'raw'              => 'string',
        'html'             => 'string',
        'html_minified'             => 'string',
    ];

    protected $appends = [
        //'elements_html',
    ];

    //region HELPERS
    public function builtHtml(Site $site, HtmlModel $model, $viewTemporaryName = 'element-html'): string
    {
        return AdvancedHtmlEngine::start($site, $model, $viewTemporaryName)->html($this->raw)->buildHtml();
    }

    public function flushHtmlCache(Site $site, HtmlModel $model, $viewTemporaryName = 'element-html')
    {
        $this->attributes['html'] = $this->builtHtml($site, $model, $viewTemporaryName);

    }
    //endregion

    //region ATTRIBUTES
    //region GETS

    //endregion

    //region SETS

    //endregion

    //endregion


}
