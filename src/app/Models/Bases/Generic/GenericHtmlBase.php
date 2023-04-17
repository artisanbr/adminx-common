<?php

namespace ArtisanBR\Adminx\Common\App\Models\Bases\Generic;

use ArtisanBR\Adminx\Common\App\Enums\ElementType;
use ArtisanBR\Adminx\Common\App\Libs\FrontendEngine\AdvancedHtmlEngine;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\HtmlModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\WidgeteableModel;
use ArtisanBR\Adminx\Common\App\Models\Site;
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
    public function builtHtml(Site $site, WidgeteableModel|HtmlModel $model, $viewTemporaryName = 'element-html'): string
    {
        return AdvancedHtmlEngine::start($site, $model, $viewTemporaryName)->html($this->raw)->buildHtml();
    }

    public function flushHtmlCache(Site $site, WidgeteableModel|HtmlModel $model, $viewTemporaryName = 'element-html')
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
