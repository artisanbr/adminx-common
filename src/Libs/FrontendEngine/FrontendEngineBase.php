<?php

namespace Adminx\Common\Libs\FrontendEngine;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Site;

abstract class FrontendEngineBase
{

    protected ?Site $currentSite = null;

    protected int $cacheMinutes = 24 * 7;

    protected ?string $cacheName = null;

    protected ?string $currentDomain = null;

    public function isForwarded(): bool
    {
        return (bool) request()?->server('HTTP_X_FORWARDED_HOST');
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
        return request()?->server('HTTP_X_FORWARDED_HOST') ?? request()?->getHost() ?? null;
    }

    public function forwardDomain(): array|string|null
    {
        return $this->fwDomain();
    }
}