<?php

namespace ArtisanBR\Adminx\Common\App\Models\VisitTracker;


class Cookie extends TrackerBase
{
    protected $table = 'tracker_cookies';

    protected $fillable = ['uuid', 'site_id'];
}
