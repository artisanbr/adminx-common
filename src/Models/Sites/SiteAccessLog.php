<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites;

use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SiteAccessLog extends Pivot
{
    use BelongsToSite, BelongsToUser, HasOwners;

    protected $table = 'site_access_log';

    public $incrementing = true;

    protected $fillable = [
        'site_id',
        'user_id',
        'ip_address',
    ];

    protected $casts = [
        'ip_address' => 'string',
        'created_at' => 'datetime:d/m/Y H:i:s',
    ];

    public    $timestamps = true;
    protected $guarded    = [];
    protected $hidden     = [];


}
