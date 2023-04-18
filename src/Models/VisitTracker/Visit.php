<?php

namespace Adminx\Common\Models\VisitTracker;


use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Scopes\WhereSiteScope;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;

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

    protected static function booted()
    {
        static::addGlobalScope(new WhereSiteScope);
    }

}
