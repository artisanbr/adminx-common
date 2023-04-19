<?php

namespace Adminx\Common\Models;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasRelatedCache;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasVisitCounter;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;

class HtmlBuild extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasOwners, HasUriAttributes, BelongsToSite, BelongsToUser, HasPublicIdAttribute, HasPublicIdUriAttributes, HasVisitCounter, HasRelatedCache, HasMorphAssigns;

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
