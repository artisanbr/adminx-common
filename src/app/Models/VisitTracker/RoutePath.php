<?php

namespace ArtisanBR\Adminx\Common\App\Models\VisitTracker;


class RoutePath extends TrackerBase
{
    protected $table = 'tracker_route_paths';

    protected $fillable = [
        'route_id',
        'path',
    ];

    public function parameters()
    {
        return $this->hasMany($this->getConfig()->get('route_path_parameter_model'));
    }

    public function route()
    {
        return $this->belongsTo($this->getConfig()->get('route_model'), 'route_id');
    }
}