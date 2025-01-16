<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FileManager;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Objects\FileObject;
use Buglinjo\LaravelWebp\Webp;
use Illuminate\Http\UploadedFile;

class FileUploadManager extends FileManager
{

    public function upload(UploadedFile $requestFile, $uploadPath = '', $fileName = '', $imagesToWebp = true): ?FileObject
    {

        $this->fileObject = new FileObject();

        if (empty($fileName)) {
            $fileName = Str::ulid();
        }

        //Remover caracteres indesejados
        //Remover extensão e tratar para URL amigavel
        $fileName = str($fileName)->beforeLast('.'.$requestFile->getClientOriginalExtension())->slug();



        $this->onPath($uploadPath);

        $this->fileName = $fileName->toString() . '.' . $requestFile->getClientOriginalExtension();


        //Checkar se o arquivo existe atualmente e removê-lo
        if ($this->remoteStorage->exists($this->filePath())) {
            //Remove file
            $this->remoteStorage->delete($this->filePath());
        }


        //Salvar
        $this->fileObject->fill([
                                    'name'      => $this->fileName,
                                    'mime_type' => $requestFile->getClientMimeType(),
                                    'extension' => $requestFile->getClientOriginalExtension(),
                                ]);


        //$image->storePubliclyAs($uploadPath, $name); //$image->move(storage_path($uploadPath), $name);

        //Converter imagem para WebP caso o recurso esteja habilitado no site.
        if ($imagesToWebp && $this->fileObject->is_image && !Str::contains($this->fileObject->mime_type, 'webp')) {
            //Converter para Webp
            $imageRelativePath = $this->fullPathTo("{$fileName}.webp");

            $webpTempFileName = time() . random_int(1, 9999) . "-{$fileName}.webp";

            $webpTempPath = $this->tempStorage->path($webpTempFileName);

            $webp = Webp::make($requestFile);

            if ($webp->save($webpTempPath, 100)) {

                //Save on CDN
                $this->remoteStorage->put($imageRelativePath, file_get_contents($webpTempPath));

                // File is saved successfully
                $this->fileObject->fill([
                                            'name'      => "{$fileName}.webp",
                                            'mime_type' => $this->remoteStorage->mimeType($webpTempPath),
                                            'extension' => 'webp',
                                            'path'      => $imageRelativePath,
                                        ]);

                //Clear Temp
                $this->tempStorage->delete($webpTempFileName);

                return $this->fileObject;
            }
        }


        $finalPath = $this->remoteStorage->putFileAs($this->fullPathTo(), $requestFile, $this->fileName, 'public');

        $this->fileObject->fill([
                                    'path' => $finalPath,
                                ]);

        return $this->fileObject;
    }
}
