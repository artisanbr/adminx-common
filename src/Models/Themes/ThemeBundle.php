<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Themes\Enums\ThemeBundleDefaults;
use Adminx\Common\Models\Themes\Enums\ThemeBundlePlacement;
use Adminx\Common\Models\Traits\HasRelatedCache;
use Butschster\Head\MetaTags\Meta;
use Butschster\Head\Packages\Package;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeBundle extends EloquentModelBase
{
    use HasRelatedCache;

    protected $connection = 'mysql';

    protected $fillable = [
        'theme_build_id',

        'defer',
        'placement',

        'name',
        'url',
        'path',

        'content',
        'files',
    ];

    protected $casts = [
        'name'         => 'string',
        'placement'    => ThemeBundlePlacement::class,
        'url'          => 'string',
        'url_minified' => 'string',
        'storage_path' => 'string',
        'content'      => 'string',
        'defer'        => 'boolean',
        'files'        => 'collection',
    ];

    protected $attributes = [];

    //protected $with = ['site'];

    /*public function renewPublicId(): void
    {
        $this->attributes['public_id'] = ($this->theme_build->theme->public_id ?? '') . $this->generatePublicId();
    }*/

    //region Helpers
    public function registerMetaPackages(Package $package): void
    {

        if ($this->id && $this->placement && !blank($this->content)) {
            if ($this->placement === ThemeBundlePlacement::Css) {
                $package->addStyle($this->file_name_minified,
                                   $this->url_minified,
                                   $this->defer ? [
                                       'rel'    => 'preload',
                                       'as'     => 'style',
                                       //'media'  => 'print',
                                       'onload' => "this.onload=null;this.rel='stylesheet'",
                                   ] : []);
            }
            else {
                $package->addScript($this->file_name_minified,
                                    $this->url_minified,
                                    $this->defer ? ['defer'] : [],
                                    $this->placement === ThemeBundlePlacement::HeadJs ? Meta::PLACEMENT_HEAD : Meta::PLACEMENT_FOOTER);
            }
        }

    }
    //endregion

    //region Scopes


    public function scopeCss(Builder $query): Builder
    {
        return $query->where('placement', ThemeBundlePlacement::Css->value);
    }

    public function scopeJs(Builder $query): Builder
    {
        return $query->whereAny(['placement'], [
            ThemeBundlePlacement::BodyJs->value,
            ThemeBundlePlacement::HeadJs->value,
        ]);
    }

    public function scopeBodyJs(Builder $query): Builder
    {
        return $query->where('placement', ThemeBundlePlacement::BodyJs->value);
    }

    public function scopeHeadJs(Builder $query): Builder
    {
        return $query->where('placement', ThemeBundlePlacement::HeadJs->value);
    }


    public function scopeMainCss(Builder $query): Builder
    {
        return $query->css()->where('name', ThemeBundleDefaults::CssMain->name());
    }

    public function scopeDeferCss(Builder $query): Builder
    {
        return $query->css()->where('name', ThemeBundleDefaults::CssDefer->name());
    }

    public function scopeMainBodyJs(Builder $query): Builder
    {
        return $query->bodyJs()->where('name', ThemeBundleDefaults::BodyJsMain->name());
    }

    public function scopeDeferBodyJs(Builder $query): Builder
    {
        return $query->bodyJs()->where('name', ThemeBundleDefaults::BodyJsDefer->name());
    }

    public function scopeMainHeadJs(Builder $query): Builder
    {
        return $query->headJs()->where('name', ThemeBundleDefaults::HeadJsMain->name());
    }

    public function scopeDeferHeadJs(Builder $query): Builder
    {
        return $query->headJs()->where('name', ThemeBundleDefaults::HeadJsDefer->name());
    }


    //endregion

    //region Attributes
    protected function fileName(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => !blank($this->name) ? $this->name . '.bundle' . $this->placement?->extension() : null,
        );
    }

    protected function fileNameMinified(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => !blank($this->name) ? $this->name . '.bundle.min' . $this->placement?->extension() : null,
        );
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => !blank(str($value)->trim()->toString()) ? $value : $this->theme_build->theme->cdnUrlTo('bundles/' . $this->file_name),
            set: fn($value) => $value,
        );
    }

    protected function urlMinified(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => !blank($value) ? $value : $this->theme_build->theme->cdnUrlTo('bundles/' . $this->file_name_minified),
            set: fn($value) => $value,
        );
    }

    protected function storagePath(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {

                $finalPath = str($value ?? $this->theme_build?->theme?->cdnProxyUrlTo('bundles/') ?? null);

                return !$finalPath->trim()->isEmpty() ? $finalPath->finish('/') : null;
            },
            set: fn($value) => $value,
        );
    }

    protected function filePath(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->storage_path . $this->file_name,
        );
    }

    protected function filePathMinified(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $this->storage_path . $this->file_name_minified,
        );
    }
    //endregion

    //region RELATIONS

    public function theme_build(): BelongsTo
    {
        return $this->belongsTo(ThemeBuild::class);
    }
    //endregion
}
