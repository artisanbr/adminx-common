<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models;

use Adminx\Common\Enums\FileType;
use Adminx\Common\Libs\Helpers\FileHelper;
use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Generics\Configs\FileConfig;
use Adminx\Common\Models\Interfaces\OwneredModel;
use Adminx\Common\Models\Traits\HasOwners;
use Adminx\Common\Models\Traits\HasUriAttributes;
use Adminx\Common\Models\Traits\Relations\BelongsToSite;
use Adminx\Common\Models\Traits\Relations\BelongsToUser;
use Adminx\Common\Models\Traits\Relations\HasMorphAssigns;
use Adminx\Common\Models\Traits\Relations\MorphToUploadable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Query\Builder as DBBuilder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Rolandstarke\Thumbnail\Facades\Thumbnail;

/**
 *
 */
class File extends EloquentModelBase implements OwneredModel
{
    use HasUriAttributes, HasMorphAssigns, BelongsToSite, BelongsToUser, HasOwners, MorphToUploadable;

    protected $fillable = [
        'site_id',
        'user_id',
        'folder_id',
        'account_id',
        'uploadable_id',
        'uploadable_type',
        'name',
        'type',
        'mime_type',
        'extension',
        'path',
        'title',
        'description',
        'config',
        'editable',
    ];

    protected $casts = [
        'name'               => 'string',
        'type'               => FileType::class,
        'mime_type'          => 'string',
        'extension'          => 'string',
        'path'               => 'string',
        'title'              => 'string',
        'description'        => 'string',
        'config'             => FileConfig::class,
        'editable'           => 'boolean',
        'is_editable_source' => 'boolean',
        'is_theme_bundle' => 'boolean',
        'can_be_theme_bundle' => 'boolean',
        'order'              => 'integer',
    ];

    protected $appends = [
        'url',
        'storage_path',
        //'thumb_url',
        'is_image',
        'order',
        'is_theme_bundle',
        'can_be_theme_bundle',
    ];

    //region SCOPES
    public function scopeImages(Builder $query, array|null $imageTypes = null): Builder|DBBuilder
    {
        return $query->whereLike('mime_type', $imageTypes ?: 'image')->orWhereIn('extension', config('adminx.defines.files.types.image'));
    }

    public function scopeThemeMedia(Builder $query): Builder|DBBuilder
    {
        return $query->where('type', FileType::ThemeMedia);
    }

    public function scopeAsset(Builder $query): Builder|DBBuilder
    {
        return $query->where('type', FileType::ThemeAsset);
    }

    public function scopeThemeBundle(Builder $query): Builder|DBBuilder
    {
        return $query->asset()->where('config->is_theme_bundle', 'true');
    }

    public function scopeThemeBundleSortened(Builder $query): Collection{
        return $query->themeBundle()->get()->sortBy('config.theme_bundle_position');
    }
    //endregion

    //region HELPERS
    public function upload(UploadedFile $requestFile, $path = "", $fileName = null)
    {

        if (!$this->id) {
            $this->save();
            $this->refresh();
        }

        return FileHelper::saveRequestToSite(Auth::user()->site, $requestFile, Str::plural($this->uploadable_type) . "/$path", $fileName, $this);
    }

    public function relativePathTo($path): string
    {
        $relativePath = Str::of($this->path)->replace($path, '');

        if ($relativePath->startsWith(['/', '\\'])) {
            $relativePath = $relativePath->substr(1);
        }

        return $relativePath;
    }
    //endregion

    //region ATTRIBUTES
    protected function order(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->config->theme_bundle_position,
            set: function ($value) {
                $this->config->theme_bundle_position = $value;
            },
        );
    }

    protected function isImage(): Attribute
    {
        $imageTypes = collect(config('adminx.defines.files.types.image'));

        return Attribute::make(
            get: fn() => $imageTypes->contains($this->extension) || $imageTypes->contains($this->mime_type)
        );
    }

    protected function isTheme(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->type === FileType::Theme
        );
    }

    //Todo
    protected function isThemeBundle(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->config->is_theme_bundle
        );
    }

    protected function canBeThemeBundle(): Attribute
    {
        return Attribute::make(
            get: fn($value) => collect(config('adminx.defines.files.types.theme_bundle'))->contains($this->extension)
        );
    }

    protected function isEditableSource(): Attribute
    {
        return Attribute::make(
            get: fn() => collect(config('adminx.defines.files.types.editable_sources'))->contains($this->extension)
        );
    }

    protected function icon(): Attribute
    {
        $icon = 'file';

        if ($this->extension === 'js') {
            $icon = 'fa-brands fa-square-js';
        }

        if ($this->extension === 'css') {
            $icon = 'fa-brands fa-css3';
        }

        if ($this->extension === 'json') {
            $icon = 'fa-regular fa-file-code';
        }

        if ($this->is_image) {
            $icon = 'fa-regular fa-file-image';
        }


        return Attribute::make(
            get: fn($value) => Blade::render(<<<blade
<x-icon icon="$icon" size="2" />
blade)
        );
    }

    protected function size(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Storage::drive('public')->fileSize($this->path)
        );
    }

    //region GETS
    protected function getDirectoryAttribute(): string|null
    {
        return Str::replaceNative("/{$this->name}", '', $this->path);
    }
    protected function getNameWhitoutExtensionAttribute(): string|null
    {
        return (collect(explode('.', $this->name))->shift() ?? collect())->implode('.');
    }

    protected function getUrlAttribute(): string|null
    {
        return "/storage/{$this->path}";
    }

    protected function getUriAttribute(): string|null
    {
        return $this->storage_path;
    }

    protected function getStoragePathAttribute(): string|null
    {
        return empty($this->path) ? null : Storage::url($this->path);
    }
    //endregion
    //endregion

    //region OVERRIDES
    public function deleteWithFile()
    {
        //Remover arquivo local
        Storage::drive('ftp')->delete($this->path);

        return parent::delete(); // TODO: Change the autogenerated stub
    }
    //endregion

    //region RELATIONS


    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
    //endregion
}
