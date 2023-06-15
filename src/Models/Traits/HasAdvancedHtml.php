<?php

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Libs\FrontendEngine\AdvancedHtmlEngine;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Interfaces\HtmlModel;
use Adminx\Common\Models\Pages\Page;


trait HasAdvancedHtml
{

    public function htmlBuilder(): AdvancedHtmlEngine
    {
        if(!$this->id && !$this->site){
            abort(500, 'Salve a model antes de montar seu HTML');
        }

        return AdvancedHtmlEngine::start($this->site, $this, 'page-html');
    }

    public function flushHtmlCache($save = false){
        /**
         * @var EloquentModelBase|Page|HtmlModel $this
         */

        $htmlCacheAttr = $this->htmlCacheAttribute;

        $this->attributes[$htmlCacheAttr] = $this->builtHtml();

        if($save){
            $this->save();
        }

        return $this->{$htmlCacheAttr};
    }

    public function builtHtml(): string
    {
        $htmlRawAttribute = $this->htmlRawAttribute;

        return $this->htmlBuilder()->html($this->{$htmlRawAttribute})->buildHtml();
    }


}
