<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Sites\Objects\Config\Import;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Sites\Site;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class FtpMediaImportConfig extends GenericModel
{

    protected $fillable = [
        'checked',
        'host',
        'username',
        'password',
        'port',
        'path',
    ];

    protected $attributes = [
        'checked'  => false,
        'host'     => 'fvconsultoriaetreinamento.com',
        'username' => 'import@fvconsultoriaetreinamento.com',
        'password' => 'i1VHe6sCDN',
        'port'     => 21,
        'path'     => 'public_html/',
    ];

    protected $casts = [
        'checked'  => 'bool',
        'host'     => 'string',
        'password' => 'string',
        'username' => 'string',
        'port'     => 'int',
        'path'     => 'string',
    ];

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
            ...$this->toArray(),
        ]);

        $this->ftpStorage = Storage::disk($storageName);

        return $this->ftpStorage;
    }

    public function getFtpMediaFiles($remotePath = 'wp-content/uploads'): Collection
    {
        $this->prepareFtpStorage();

        return collect($this->ftpStorage->allFiles($this->remotePathTo($remotePath)))->filter(static fn($file) => preg_match('/\.(jpg|jpeg|png|gif|pdf|webp|ico)$/', $file));
    }

    public function checkConnection()
    {

        if ($this->host && $this->username && $this->password && $this->port) {

            try {

                $this->prepareFtpStorage();
                $this->checked = $this->ftpStorage->has($this->path);

            } catch (Exception $e) {
                $this->checked = false;
            }
        }
        else {
            $this->checked = false;
        }

        return $this->checked;
    }

    /**
     * Importar Mídia
     */
    public function importTo(Site $site, $step = 1, $step_items_number = 50, $remotePath = 'wp-content/uploads', $localPath = 'wp'): Collection
    {

        /**
         * FTP da Plataforma (não confundir)
         */
        $localStorage = Storage::disk('ftp');

        $remoteFinalPath = $this->remotePathTo($remotePath);

        $importFiles = $this->getFtpMediaFiles($remotePath)->forPage($step, $step_items_number);

        $resultCollection = collect();

        foreach ($importFiles as $importFile) {
            $newFile = Str::replace($remoteFinalPath, $site->uploadPathTo($localPath), $importFile);

            if(!$localStorage->exists($newFile)) {
                $contents = $this->ftpStorage->get($importFile);
                $result = $localStorage->put($newFile, $contents);
            }else{
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

    public function remotePathTo($path = ''): string
    {
        return $this->path . $path;
    }

    public function lockPassword(): ?string
    {
        if (!empty($this->password ?? null)) {
            $this->password = Crypt::encrypt($this->password);
        }

        return $this->password;
    }

    public function password_decrypt(): string
    {
        return !empty($this->password ?? null) ? Crypt::decrypt($this->password) : '';
    }


    //region Attributes
    //region GET's
    protected function getPathAttribute()
    {
        $path = $this->attributes["path"] ?? '';

        return empty($path) || Str::endsWith($path, '/') ? $path : "{$path}/";
    }
    //endregion

    //region SET's
    //protected function setAttribute($value){}

    //endregion
    //endregion


}
