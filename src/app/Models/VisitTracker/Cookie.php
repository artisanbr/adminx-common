<?php

namespace ArtisanBR\Adminx\Common\App\Models\VisitTracker;

use PragmaRX\Tracker\Vendor\Laravel\Models\Base;

class Cookie extends Base
{
    protected $table = 'tracker_cookies';

    protected $fillable = ['uuid', 'site_id'];
}
