<?php

namespace Adminx\Common\Models\Objects;


use Adminx\Common\Libs\FileManager\Helpers\FileHelper;
use ArtisanLabs\GModel\GenericModel;


/**
 * @property bool $is_image
 * @property bool $is_webp_compatible
 * @property bool $is_zip
 * @property bool $is_editable
 */
class FileObject extends GenericModel
{
    protected $fillable = [
        'name',
        'url',
        'path',
        'extension',
        'mime_type',
        'tag',
        'attrs',
    ];

    protected $attributes = [
        'attrs' => [],
    ];

    protected $casts = [
        'name'      => 'string',
        'url'       => 'string',
        'path'      => 'string',
        'extension' => 'string',
        'mime_type' => 'string',

        'relative_path' => 'string',
        'tag'           => 'string',

        'html'  => 'string',
        'attrs' => 'collection',

        'is_image'           => 'bool',
        'is_webp_compatible' => 'bool',
        'is_zip'             => 'bool',
        'is_editable'        => 'bool',

    ];

    protected $appends = [
        'url',
        'location',
    ];


    //region Attributes
    //region Gets
    protected function getIsImageAttribute()
    {
        return match (true) {
            FileHelper::isImageByExtension($this->extension),
            FileHelper::isImageByMimeType($this->mime_type),
            FileHelper::isImage($this->path) => true,
            default => false,
        };
    }

    protected function getIsWebpCompatibleAttribute()
    {
        return match (true) {
            FileHelper::isWebpCompatibleByExtension($this->extension),
            FileHelper::isWebpCompatibleByMimeType($this->mime_type),
            FileHelper::isWebpCompatible($this->path) => true,
            default => false,
        };
    }

    protected function getIsZipAttribute()
    {
        return match (true) {
            FileHelper::isZipByExtension($this->extension),
            FileHelper::isZipByMimeType($this->mime_type),
            FileHelper::isZip($this->path) => true,
            default => false,
        };
    }

    protected function getIsEditableAttribute()
    {
        return match (true) {
            FileHelper::isEditableByExtension($this->extension),
            FileHelper::isEditableByMimeType($this->mime_type),
            FileHelper::isEditable($this->path) => true,
            default => false,
        };
    }

    protected function getHtmlAttributesAttribute()
    {
        return collect([
                           'alt' => $this->attrs->get('title'),
                       ])->merge($this->attrs->toArray())
                         ->reduce(fn($carry, $value, $key) => $carry . $key . '="' . $value . '" ');
    }

    protected function getTagAttribute(): ?string
    {
        return $this->attributes['tag'] ?? ($this->is_image ? 'img' : null);
    }

    protected function getUrlAttribute(): string|null
    {
        return "/storage/{$this->path}";
    }

    protected function getLocationAttribute(): string|null
    {
        return $this->url;
    }

    protected function getHtmlAttribute(): string
    {
        return $this->tag ? "<{$this->tag} src=\"{$this->url}\" {$this->html_attributes} />" : '';
    }
    //endregion

    //endregion
}
