<?php

namespace ArtisanBR\Adminx\Common\App\Libs\Helpers;

use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use ArtisanBR\Adminx\Common\App\Models\File;
use ArtisanBR\Adminx\Common\App\Models\Site;
use Buglinjo\LaravelWebp\Webp;
use Delight\Random\Random;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHelper
{


    public static function saveRequestToSite(Site $site, UploadedFile $requestFile, $path = 'images', $fileName = null, File|null $fileModel = null): ?File
    {
        if (!$fileModel) {
            $fileModel = new File();
        }

        $uploadPath = "sites/{$site->public_id}/{$path}";


        $fileModel->site_id = $site->id;

        return self::saveRequest($requestFile, $uploadPath, $fileName, $fileModel);
    }

    public static function saveRequest(UploadedFile $requestFile, $path = '', $fileName = '', File|null $fileModel = null): ?File
    {
        if (!$fileModel) {
            $fileModel = new File();
        }

        $storage = Storage::disk('public');

        $uploadPath = $path;
        if (empty($fileName)) {
            $fileName = Random::uuid4();
        }
        $name = Str::contains($fileName, '.') ? $fileName : "{$fileName}." . $requestFile->getClientOriginalExtension();

        //Checkar imagem atual
        if ($fileModel->path && Storage::exists($fileModel->path)) {
            //Remove file
            $storage->delete($fileModel->path);
        }
        $storage->delete("$uploadPath/{$name}");


        //Salvar
        $fileModel->fill([
                             'name'      => $name,
                             'mime_type' => $requestFile->getClientMimeType(),
                             'extension' => $requestFile->getClientOriginalExtension(),
                         ]);


        //$image->storePubliclyAs($uploadPath, $name); //$image->move(storage_path($uploadPath), $name);

        if ($fileModel->is_image) {
            //Converter para Webp
            $imageRelativePath = "{$uploadPath}/{$fileName}.webp";
            $imagePath = $storage->path($imageRelativePath);
            $webp = Webp::make($requestFile);
            $imageQuality = 90;
            if ($webp->save($imagePath, $imageQuality)) {
                // File is saved successfully
                $fileModel->fill([
                                     'name'      => "{$fileName}.webp",
                                     'mime_type' => $storage->mimeType($imageRelativePath),
                                     'extension' => 'webp',
                                     'path'      => $imageRelativePath,
                                 ]);

                return $fileModel;
            }
        }

        $finalPath = $storage->putFileAs($uploadPath, $requestFile, $name, 'public');
        $fileModel->fill([
                             'path' => $finalPath,
                         ]);

        return $fileModel;
    }
}
