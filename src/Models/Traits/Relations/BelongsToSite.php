<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Sites\Site;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this;
 */
trait BelongsToSite
{
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
