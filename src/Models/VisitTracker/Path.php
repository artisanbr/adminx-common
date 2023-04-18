<?php

namespace Adminx\Common\Models\VisitTracker;

class Path extends TrackerBase
{
    protected $table = 'tracker_paths';

    protected $fillable = [
        'path',
    ];
}
