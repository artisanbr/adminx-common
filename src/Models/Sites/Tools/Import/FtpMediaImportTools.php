<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites\Tools\Import;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Sites\Objects\Config\Import\FtpMediaImportConfig;
use Adminx\Common\Models\Sites\Site;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class FtpMediaImportTools
{

    public function __construct(
        protected ?Site                 $site = null,
        protected ?FtpMediaImportConfig $ftpImportConfig = null,
    ) {}

    public function setSite(Site $site): self
    {
        $this->site = $site;

        return $this->setFtpConfig($site->config->import->wordpress->media_ftp);
    }

    public function setFtpConfig(FtpMediaImportConfig $ftpImportConfig): self
    {
        $this->ftpImportConfig = $ftpImportConfig;

        return $this;
    }

    protected function traitFileEncoding($file): string
    {
        if (!mb_check_encoding($file, 'UTF-8')) {
            // A string is not encoded in UTF-8
            $file = mb_convert_encoding($file, 'UTF-8');
        }

        return $file;
    }

    protected ?Filesystem $ftpStorage;

    public function prepareFtpStorage(): Filesystem|FilesystemAdapter|null
    {
        $storageName = 'ftp_import';

        Config::set("filesystems.disks.{$storageName}", [
            ...config("filesystems.disks.{$storageName}"),
            ...$this->ftpImportConfig->toArray(),
        ]);

        $this->ftpStorage = Storage::disk($storageName);

        return $this->ftpStorage;
    }


    public function getFtpMediaFiles($remotePath = 'wp-content/uploads'): Collection
    {
        if ($this->ftpImportConfig) {

            $this->prepareFtpStorage();

            return collect($this->ftpStorage->allFiles($this->ftpImportConfig->remotePathTo($remotePath)))->filter(static fn($file) => preg_match('/\.(jpg|jpeg|png|gif|pdf|webp|ico)$/', $file));
        }

        return collect();
    }

    public function checkConnection()
    {

        if ($this->ftpImportConfig?->host && $this->ftpImportConfig?->username && $this->ftpImportConfig?->password && $this->ftpImportConfig?->port) {

            try {
                $this->prepareFtpStorage();
                $this->ftpImportConfig->checked = $this->ftpStorage->has($this->ftpImportConfig->path);

            } catch (Exception $e) {
                $this->ftpImportConfig->checked = false;
            }
        }
        else {
            $this->ftpImportConfig->checked = false;
        }

        return $this->ftpImportConfig->checked;
    }

    /**
     * Importar Mídia
     */
    public function importTo(Site $site, $step = 1, $step_items_number = 50, $remotePath = 'wp-content/uploads', $localPath = 'wp'): Collection
    {
        $this->setSite($site);

        /**
         * FTP da Plataforma (não confundir)
         */
        $localStorage = Storage::disk('ftp');

        $remoteFinalPath = $this->ftpImportConfig->remotePathTo($remotePath);

        $importFiles = $this->getFtpMediaFiles($remotePath)->forPage($step, $step_items_number);

        $resultCollection = collect();

        foreach ($importFiles as $importFile) {
            $newFile = Str::replace($remoteFinalPath, $site->uploadPathTo($localPath), $importFile);

            if (!$localStorage->exists($newFile)) {
                $contents = $this->ftpStorage->get($importFile);
                $result = $localStorage->put($newFile, $contents);
            }
            else {
                $result = true;
            }

            $resultCollection->add([
                                       'old_file' => $importFile,
                                       'new_file' => $result ? "/storage/{$newFile}" : '',
                                       'result'   => $result,
                                       'moment'   => date('d/m/Y - H:i:s'),
                                   ]);

        }

        return $resultCollection;

    }


}
