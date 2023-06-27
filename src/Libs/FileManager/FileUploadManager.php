<?php

namespace Adminx\Common\Libs\FileManager;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Objects\FileObject;
use Adminx\Common\Models\Site;
use Buglinjo\LaravelWebp\Webp;
use Delight\Random\Random;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileUploadManager
{

    protected Filesystem $remoteStorage, $tempStorage;

    public function __construct(
        protected ?Site      $site = null,
        protected string     $uploadPathBase = '',
        protected string     $uploadPath = '',
        protected string     $fileName = '',
        protected FileObject $fileObject = new FileObject(),
    )
    {
        $this->remoteStorage = Storage::disk('ftp');
        $this->tempStorage = Storage::disk('temp');

        if (!$this->site && @Auth::check() && Auth::user()->site) {
            $this->onSite(Auth::user()->site);
        }
    }

    public function site(Site $site): static
    {

        $this->site = $site;

        return $this;
    }

    public function onSite(Site $site): static
    {

        $this->site($site);

        $this->uploadPathBase = "sites/{$site->public_id}/";

        return $this;
    }

    protected function basePathTo($path): string
    {
        return $this->uploadPathBase . $path;
    }

    protected function fullPathTo($path = ''): string
    {
        return $this->uploadPathBase . $this->uploadPath . $path;
    }

    protected function filePath(): string
    {
        return $this->uploadPathBase . $this->uploadPath . $this->fileName;
    }

    public function onPath(string $uploadPath): static
    {
        $this->uploadPath = $uploadPath . (Str::endsWith($uploadPath, '/') ? '' : '/');
        return $this;
    }

    public function upload(UploadedFile $requestFile, $uploadPath = '', $fileName = '', $imagesToWebp = true): ?FileObject
    {

        $this->fileObject = new FileObject();

        if (empty($fileName)) {
            $fileName = Random::uuid4();
        }

        $this->onPath($uploadPath);

        $this->fileName = Str::contains($fileName, '.') ? $fileName : "{$fileName}." . $requestFile->getClientOriginalExtension();


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

            if ($webp->save($webpTempPath, 90)) {

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
