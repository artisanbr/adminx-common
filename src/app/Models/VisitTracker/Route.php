<?php

namespace ArtisanBR\Adminx\Common\App\Models\VisitTracker;


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
