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
use ArtisanLabs\LaravelVisitTracker\Traits\Visitable;
use Illuminate\Support\Facades\Blade;

class ThemeBuild extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasOwners, HasUriAttributes, BelongsToSite, BelongsToUser, HasPublicIdAttribute, HasPublicIdUriAttributes, HasVisitCounter, Visitable, HasRelatedCache, HasMorphAssigns;

    protected $connection = 'mysql';

    protected $fillable = [
        'site_id',
        'user_id',
        'account_id',
        'theme_id',

        'header',
        'footer',
    ];

    protected $casts = [
        'header'        => 'string',
        'footer'        => 'string',
    ];

    protected $appends = [];

    protected $attributes = [];

    public function renewPublicId(): void
    {
        $this->attributes['public_id'] = ($this->theme->public_id ?? '') . $this->generatePublicId();
    }

    //region HELPERS
    public function render(Page $page, $area = 'header'): string
    {
        if ($this->attributes[$area] ?? false) {
            return Blade::render($this->attributes[$area], compact('page'));
        }

        return '';
    }

    public function renderHeader(Page $page): string
    {
        return $this->render($page);
    }

    public function renderFooter(Page $page): string
    {
        return $this->render($page, 'footer');
    }
    //endregion

    //region RELATIONS
    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }
    //endregion
}
