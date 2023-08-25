<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SiteUser extends Pivot
{
    protected $table = 'site_users';
    //
}
