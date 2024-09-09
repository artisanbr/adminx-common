<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Interfaces\PublicIdModel;
use Adminx\Common\Models\Objects\Frontend\Builds\FrontendBuildObject;
use Adminx\Common\Models\Themes\Enums\ThemeBundleDefaults;
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

class ThemeBuild extends EloquentModelBase implements PublicIdModel, OwneredModel
{
    use HasOwners, HasUriAttributes, BelongsToSite, BelongsToUser, HasPublicIdAttribute, HasPublicIdUriAttributes, HasVisitCounter, HasRelatedCache, HasMorphAssigns;

    protected $connection = 'mysql';

    protected $fillable = [
        'site_id',
        'user_id',
        'account_id',
        'theme_id',

        'head',
        'header',
        'footer',

        'bundles',
    ];

    protected $casts = [
        'head'        => 'string',
        'header'        => 'string',
        'footer'        => 'string',
        //'bundles'        => 'collection',
    ];

    /*protected $appends = [];*/

    protected $attributes = [
        //'bundles' => '[]',
    ];

    //protected $with = ['site'];

    public function renewPublicId(): void
    {
        $this->attributes['public_id'] = ($this->theme->public_id ?? '') . $this->generatePublicId();
    }

    //region HELPERS
    public function render(FrontendBuildObject $frontendBuild, $area = 'header'): string
    {
        if ($this->attributes[$area] ?? false) {
            return Blade::render($this->attributes[$area], compact('frontendBuild'), true);
        }

        return '';
    }

    public function renderHead(FrontendBuildObject $frontendBuild): string
    {
        return $this->render($frontendBuild, 'head');
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
    public function bundles()
    {
        return $this->hasMany(ThemeBundle::class, 'theme_build_id', 'id');
    }

    public function main_css_bundle()
    {
        return $this->hasOne(ThemeBundle::class, 'theme_build_id', 'id')
                    ->mainCss()
                    ->withDefault(ThemeBundleDefaults::CssMain->defaults());
    }

    public function defer_css_bundle()
    {
        return $this->hasOne(ThemeBundle::class, 'theme_build_id', 'id')
                    ->deferCss()
                    ->withDefault(ThemeBundleDefaults::CssDefer->defaults());
    }

    public function main_body_js_bundle()
    {
        return $this->hasOne(ThemeBundle::class, 'theme_build_id', 'id')
                    ->mainBodyJs()
                    ->withDefault(ThemeBundleDefaults::BodyJsMain->defaults());
    }

    public function defer_body_js_bundle()
    {
        return $this->hasOne(ThemeBundle::class, 'theme_build_id', 'id')
                    ->deferBodyJs()
                    ->withDefault(ThemeBundleDefaults::BodyJsDefer->defaults());
    }

    public function main_head_js_bundle()
    {
        return $this->hasOne(ThemeBundle::class, 'theme_build_id', 'id')
                    ->mainHeadJs()
                    ->withDefault(ThemeBundleDefaults::HeadJsMain->defaults());
    }

    public function defer_head_js_bundle()
    {
        return $this->hasOne(ThemeBundle::class, 'theme_build_id', 'id')
                    ->deferHeadJs()
                    ->withDefault(ThemeBundleDefaults::HeadJsDefer->defaults());
    }
    //endregion
}
