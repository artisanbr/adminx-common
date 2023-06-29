<?php

namespace Adminx\Common\Models;

use Adminx\Common\Exceptions\FrontendException;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Casts\AsFormElementCollection;
use Adminx\Common\Models\Generics\Configs\FormConfig;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Carbon\Carbon;
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
