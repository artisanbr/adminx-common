<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits;

use ArtisanBR\Adminx\Common\App\Libs\FrontendEngine\AdvancedHtmlEngine;
use ArtisanBR\Adminx\Common\App\Models\Bases\EloquentModelBase;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\HtmlModel;
use ArtisanBR\Adminx\Common\App\Models\Page;


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

        $this->attributes[$this->htmlCacheAttribute] = $this->builtHtml();

        if($save){
            $this->save();
        }

        return $this->html;
    }

    public function builtHtml(): string
    {
        $htmlRawAttribute = $this->htmlRawAttribute;
        return $this->htmlBuilder()->html($this->{$htmlRawAttribute})->buildHtml();
    }


}
