<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\Helpers;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\File;
use Adminx\Common\Models\Sites\Site;
use Buglinjo\LaravelWebp\Webp;
use Delight\Random\Random;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHelper
{


    public static function
    saveRequestToSite(Site $site, UploadedFile $requestFile, $path = 'images', $fileName = null, File|null $fileModel = null): ?File
    {
        if (!$fileModel) {
            $fileModel = new File();
        }

        $uploadPath = str($path)->start("sites/{$site->public_id}/")->toString();


        $fileModel->site_id = $site->id;

        return self::saveRequest($requestFile, $uploadPath, $fileName, $fileModel, (bool) $site->config->performance->enable_image_optimize);
    }

    public static function saveRequest(UploadedFile $requestFile, $path = '', $fileName = '', ?File $fileModel = null, bool $imagesToWebp = true): ?File
    {
        if (!$fileModel) {
            $fileModel = new File();
        }

        //$storage = Storage::disk('public');
        $storage = Storage::disk('ftp');

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

        if (Storage::exists("{$uploadPath}/{$name}")) {
            $storage->delete("{$uploadPath}/{$name}");
        }


        //Salvar
        $fileModel->fill([
                             'name'      => $name,
                             'mime_type' => $requestFile->getClientMimeType(),
                             'extension' => $requestFile->getClientOriginalExtension(),
                         ]);


        //$image->storePubliclyAs($uploadPath, $name); //$image->move(storage_path($uploadPath), $name);

        //Converter imagem para WebP caso o recurso esteja habilitado no site.
        if ($fileModel->is_image && !Str::contains($fileModel->mime_type, 'webp') && $imagesToWebp) {
            //Converter para Webp
            $imageRelativePath = "{$uploadPath}/{$fileName}.webp";
            $webpTempFileName = time().random_int(1, 999999)."{$fileModel->site->public_id}-{$fileName}.webp";
            $webpTempPath = Storage::disk('temp')->path($webpTempFileName);
            $webp = Webp::make($requestFile);

            if ($webp->save($webpTempPath, 90)) {

                //Save on CDN
                $storage->put($imageRelativePath, file_get_contents($webpTempPath));

                // File is saved successfully
                $fileModel->fill([
                                     'name'      => "{$fileName}.webp",
                                     'mime_type' => $storage->mimeType($webpTempPath),
                                     'extension' => 'webp',
                                     'path'      => $imageRelativePath,
                                 ]);

                //Clear Temp
                Storage::disk('temp')->delete($webpTempFileName);

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
