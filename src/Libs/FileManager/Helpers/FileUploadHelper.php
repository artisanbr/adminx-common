<?php

namespace Adminx\Common\Libs\FileManager\Helpers;

use Illuminate\Http\UploadedFile;

class FileUploadHelper
{

    public static function isImage(UploadedFile $file): bool
    {
        return FileHelper::isImageByMimeType($file->getMimeType());
    }

    public static function isWebpCompatible(UploadedFile $file)
    {
        return FileHelper::isWebpCompatibleByMimeType($file->getMimeType());
    }

    public static function isZip(UploadedFile $file): bool
    {
        return FileHelper::isZipByMimeType($file->getMimeType());
    }
}