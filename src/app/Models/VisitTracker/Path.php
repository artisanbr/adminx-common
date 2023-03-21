<?php

namespace ArtisanBR\Adminx\Common\App\Models\VisitTracker;

class Path extends TrackerBase
{
    protected $table = 'tracker_paths';

    protected $fillable = [
        'path',
    ];
}
