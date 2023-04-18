<?php

namespace Adminx\Common\Models\VisitTracker;


class Route extends TrackerBase
{
    protected $table = 'tracker_routes';

    protected $fillable = [
        'name',
        'action',
    ];

    public function paths()
    {
        return $this->hasMany($this->getConfig()->get('route_path_model'));
    }
}
