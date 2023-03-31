<?php

namespace ArtisanBR\Adminx\Common\App\Models;

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
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;

class ThemeBuild extends Model
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
        return $this->belongsTo(Theme::class, 'id', 'theme_id');
    }
    //endregion
}
