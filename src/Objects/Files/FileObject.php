<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Objects\Files;


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
        'rename_to',
        //'url',
        'path',
        'extension',
        'mime_type',
        'file_upload',
    ];

    protected $attributes = [];

    protected $casts = [
        'name'      => 'string',
        'url'       => 'string',
        'path'      => 'string',
        'extension' => 'string',
        'mime_type' => 'string',

        'relative_path' => 'string',

        'is_image'           => 'bool',
        'is_webp_compatible' => 'bool',
        'is_zip'             => 'bool',
        'is_editable'        => 'bool',

    ];

    protected $temporary = [
        'url',
        'rename_to',
        'location',
        'file_name',
        'relative_path',
        'is_image',
        'is_webp_compatible',
        'is_zip',
        'is_editable',
        'file_upload',
    ];


    //region Attributes
    //region Gets
    protected function getIsImageAttribute(): bool
    {
        return match (true) {
            FileHelper::isImageByExtension($this->extension),
            FileHelper::isImageByMimeType($this->mime_type),
            FileHelper::isImage($this->path) => true,
            default => false,
        };
    }

    protected function getIsWebpCompatibleAttribute(): bool
    {
        return match (true) {
            FileHelper::isWebpCompatibleByExtension($this->extension),
            FileHelper::isWebpCompatibleByMimeType($this->mime_type),
            FileHelper::isWebpCompatible($this->path) => true,
            default => false,
        };
    }

    protected function getIsZipAttribute(): bool
    {
        return match (true) {
            FileHelper::isZipByExtension($this->extension),
            FileHelper::isZipByMimeType($this->mime_type),
            FileHelper::isZip($this->path) => true,
            default => false,
        };
    }

    protected function getIsEditableAttribute(): bool
    {
        return match (true) {
            FileHelper::isEditableByExtension($this->extension),
            FileHelper::isEditableByMimeType($this->mime_type),
            FileHelper::isEditable($this->path) => true,
            default => false,
        };
    }

    protected function getDirectoryAttribute(): ?string
    {
        return str($this->path)->beforeLast($this->name)->start('/')->finish('/')->toString();
    }

    protected function getUrlAttribute(): string|null
    {
        return "/storage/{$this->path}";
    }

    protected function getLocationAttribute(): string|null
    {
        return $this->url;
    }


    //endregion

    //endregion
}
