<?php

namespace ArtisanBR\Adminx\Common\App\Models\VisitTracker;

use PragmaRX\Tracker\Vendor\Laravel\Models\Base;

class Path extends Base
{
    protected $table = 'tracker_paths';

    protected $fillable = [
        'path',
    ];
}
