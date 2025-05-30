<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FrontendEngine;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Models\Themes\Theme;

abstract class FrontendEngineBase
{

    protected ?Site $currentSite = null;
    protected ?Theme $currentTheme = null;

    protected int $cacheMinutes = 60 * 24 * 2;
    protected int $cacheHours   = 6;

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