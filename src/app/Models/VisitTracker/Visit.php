<?php

namespace ArtisanBR\Adminx\Common\App\Models\VisitTracker;


use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasOwners;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToSite;
use ArtisanLabs\LaravelVisitTracker\Models\Visit as VisitBase;

class Visit extends VisitBase implements OwneredModel
{
    use BelongsToSite, HasOwners;

    protected array $ownerTypes = ['site'];

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'site_id',
                             ]);

        parent::__construct($attributes);
    }

}
