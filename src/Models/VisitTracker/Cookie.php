<?php

namespace Adminx\Common\Models\VisitTracker;


class Cookie extends TrackerBase
{
    protected $table = 'tracker_cookies';

    protected $fillable = ['uuid', 'site_id'];
}
