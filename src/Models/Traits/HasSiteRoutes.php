<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

use Adminx\Common\Models\Sites\SiteRoute;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;


trait HasSiteRoutes
{

    /**
     * Rotas próprias da Página
     */
    public function routes(): MorphMany
    {
        return $this->morphMany(SiteRoute::class, 'model');
    }

    public function canonical_route(): MorphOne
    {
        return $this->morphOne(SiteRoute::class, 'model')->where('canonical', true);
    }


}
