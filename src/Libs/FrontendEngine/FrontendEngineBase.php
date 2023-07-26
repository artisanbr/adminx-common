<?php

namespace Adminx\Common\Libs\FrontendEngine;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Site;

abstract class FrontendEngineBase
{

    protected Site|null $currentSite = null;

    protected int $cacheMinutes = 1; // 24 * 7;

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