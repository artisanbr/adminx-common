<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\FileManager;

use Adminx\Common\Models\Objects\FileObject;
use Adminx\Common\Models\Sites\Site;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileManager
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
        return str($this->uploadPathBase)->when(!empty($path), fn($str) => $str->finish('/'))->toString();
    }

    protected function fullPathTo($path = ''): string
    {
        return str($this->uploadPathBase)
            ->append($this->uploadPath)
            ->finish('/')
            ->when(!empty($path), fn($str) => $str->append(str($path)->replaceStart('/', '')))
            ->toString();
    }

    protected function filePath(): string
    {
        return $this->uploadPathBase . $this->uploadPath . $this->fileName;
    }

    public function onPath(string $uploadPath): static
    {
        $this->uploadPath = str($uploadPath)->finish('/')->toString();

        return $this;
    }

    public function rename($path, $rename_to): false|string
    {
        if ($this->remoteStorage->exists($path)) {

            $path = str($path);

            $extension = $path->afterLast('.')->toString();
            $rename_to = str($rename_to)->start('/')->finish(".{$extension}")->toString();


            $newPath = $path->beforeLast('/')->append($rename_to);


            if ($this->remoteStorage->move($path->toString(), $newPath->toString())) {
                return str('/storage')->append($newPath->start('/'))->toString();
            }

        }

        return false;
    }
}
