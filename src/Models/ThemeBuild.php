<?php

namespace Adminx\Common\Models;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasPublicIdAttribute;
use Adminx\Common\Models\Traits\HasPublicIdUriAttributes;
use Adminx\Common\Models\Traits\HasRelatedCache;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\HasVisitCounter;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Illuminate\Support\Facades\Blade;
use Adminx\Common\Models\Pages\Page;

class ThemeBuild extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasOwners, HasUriAttributes, BelongsToSite, BelongsToUser, HasPublicIdAttribute, HasPublicIdUriAttributes, HasVisitCounter, HasRelatedCache, HasMorphAssigns;

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
    public function render(FrontendBuildObject $frontendBuild, $area = 'header'): string
    {
        if ($this->attributes[$area] ?? false) {
            return Blade::render($this->attributes[$area], compact('frontendBuild'));
        }

        return '';
    }

    public function renderHeader(FrontendBuildObject $frontendBuild): string
    {
        return $this->render($frontendBuild);
    }

    public function renderFooter(FrontendBuildObject $frontendBuild): string
    {
        return $this->render($frontendBuild, 'footer');
    }
    //endregion

    //region RELATIONS
    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }
    //endregion
}
