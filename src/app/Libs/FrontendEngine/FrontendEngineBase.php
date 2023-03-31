<?php

namespace ArtisanBR\Adminx\Common\App\Libs\FrontendEngine;

use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanBR\Adminx\Common\App\Models\Site;

abstract class FrontendEngineBase
{

    protected Site|null $currentSite = null;

    protected int $cacheMinutes = 60 * 24 * 7;

    protected string|null $cacheName = null;

    protected string|null $currentDomain = null;

    public function isForwarderd()
    {
        return request()->server('HTTP_X_FORWARDED_HOST') || false;
    }

    public function currentDomain(): array|string|null
    {
        if(!$this->currentDomain){
            $this->currentDomain = Str::of($this->fwDomain())->replace('www.', '');
        }

        return $this->currentDomain;
    }

    public function fwDomain(): array|string|null
    {
        return request()->server('HTTP_X_FORWARDED_HOST') ?? request()->getHost() ?? null;
    }
}