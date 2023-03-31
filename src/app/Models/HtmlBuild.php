<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use ArtisanBR\Adminx\Common\App\Models\Bases\EloquentModelBase;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\OwneredModel;
use ArtisanBR\Adminx\Common\App\Models\Interfaces\PublicIdModel;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasOwners;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdAttribute;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasPublicIdUriAttributes;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasRelatedCache;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasUriAttributes;
use ArtisanBR\Adminx\Common\App\Models\Traits\HasVisitCounter;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToSite;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\BelongsToUser;
use ArtisanBR\Adminx\Common\App\Models\Traits\Relations\HasMorphAssigns;
use ArtisanLabs\LaravelVisitTracker\Traits\Visitable;

class HtmlBuild extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasOwners, HasUriAttributes, BelongsToSite, BelongsToUser, HasPublicIdAttribute, HasPublicIdUriAttributes, HasVisitCounter, Visitable, HasRelatedCache, HasMorphAssigns;

    protected $connection = 'mysql';

    protected $fillable = [
        'site_id',
        'user_id',
        'account_id',

        'type',

        'url',
        'html',
        'html_data',

        'buildable_id',
        'buildable_type',
        'buildable_json',
    ];

    protected $casts = [
        'url'        => 'string',
        'html'        => 'string',
        'buildable_json'        => 'object',
        'type'        => 'object',
    ];

    protected $appends = [];

    protected $attributes = [];

    public function renewPublicId(): void
    {
        $this->attributes['public_id'] = ($this->buildable->public_id ?? '') . $this->generatePublicId();
    }

    //region RELATIONS
    public function buildable()
    {
        return $this->morphTo(__FUNCTION__, __FUNCTION__ . '_type', __FUNCTION__ . '_id');
    }

    //endregion

}
