<?php

namespace Adminx\Common\Libs\FileManager\Helpers;

use Adminx\Common\Libs\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class FileHelper
{

    protected static function isInExtensionGroup($term, $groupName = 'images'){
        return collect(config("files.extensions.{$groupName}"))->contains($term);
    }
    protected static function isInMimeTypeGroup($term, $groupName = 'images'){
        return collect(config("files.mime_types.{$groupName}"))->contains($term);
    }

    public static function getExtensionByFile(string $fileOrExtension){
        return Str::contains($fileOrExtension, ['.']) ? collect(explode('.',$fileOrExtension))->last() : $fileOrExtension;
    }

    public static function getMimeType(string $file, $storageDisk = 'ftp'): false|string
    {
        $storage = Storage::disk($storageDisk);

        return $storage->exists($file) ? $storage->mimeType($file) : false;
    }

    public static function isImage(string $fileOrExtension): bool
    {
        $mimeType = self::getMimeType($fileOrExtension);
        return ($mimeType && self::isImageByMimeType($mimeType)) || self::isImageByExtension($fileOrExtension);
    }
    public static function isImageByExtension(string $fileOrExtension): bool
    {
        return self::isInExtensionGroup(self::getExtensionByFile($fileOrExtension));
    }
    public static function isImageByMimeType(string $mimeType): bool
    {
        return self::isInMimeTypeGroup($mimeType);
    }

    public static function isWebpCompatible(string $fileOrExtension): bool
    {
        $mimeType = self::getMimeType($fileOrExtension);

        return ($mimeType && self::isWebpCompatibleByExtension($mimeType)) || self::isWebpCompatibleByMimeType($fileOrExtension);


    }
    public static function isWebpCompatibleByExtension(string $fileOrExtension): bool
    {
        return self::isInExtensionGroup(self::getExtensionByFile($fileOrExtension), 'webp_convert');
    }
    public static function isWebpCompatibleByMimeType(string $mimeType): bool
    {
        return self::isInMimeTypeGroup($mimeType, 'webp_convert');
    }

    public static function isZip(string $fileOrExtension): bool
    {
        $mimeType = self::getMimeType($fileOrExtension);

        return ($mimeType && self::isZipByMimeType($mimeType)) || self::isZipByExtension($fileOrExtension);
    }
    public static function isZipByExtension(string $fileOrExtension): bool
    {
        return self::getExtensionByFile($fileOrExtension) === 'zip';
    }
    public static function isZipByMimeType(string $mimeType): bool
    {
        return self::isInMimeTypeGroup($mimeType, 'zip');
    }

    public static function isEditable(string $fileOrExtension): bool
    {
        $mimeType = self::getMimeType($fileOrExtension);

        return ($mimeType && self::isEditableByExtension($mimeType)) || self::isEditableByMimeType($fileOrExtension);
    }
    public static function isEditableByExtension(string $fileOrExtension): bool
    {
        return self::getExtensionByFile($fileOrExtension) === 'zip';
    }
    public static function isEditableByMimeType(string $mimeType): bool
    {
        return self::isInMimeTypeGroup($mimeType, 'zip');
    }
}