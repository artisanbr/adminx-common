<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Repositories\Themes;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Themes\Objects\FileManager\ThemeDirectoryObject;
use Adminx\Common\Models\Themes\Objects\FileManager\ThemeFileObject;
use Adminx\Common\Models\Themes\Theme;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ThemeFileManagerRepository
{
    protected ?FilesystemAdapter $storageTemp, $storageRemote;

    public function __construct()
    {
        $this->storageTemp = Storage::disk('temp');
        $this->storageRemote = Storage::disk('ftp');
    }

    //region Getters
    public function getFile(Theme $theme, string $relative_path): ThemeFileObject
    {
        return ThemeFileObject::fromThemePath($theme, $relative_path);
    }

    public function getFileWithContent(Theme $theme, string $relative_path): ThemeFileObject
    {
        $file = $this->getFile($theme, $relative_path);

        $file->fill([
                        'content' => $this->storageRemote->get($file->path) ?? '',
                    ]);

        return $file;
    }

    public function getDirectory(Theme $theme, ?string $relative_path = null) {}
    //endregion

    //region Lists

    /**
     * Listar diretórios/pastas nos arquivos enviados de um Tema num determinado path
     *
     * @param Theme       $theme
     * @param string|null $relative_path
     *
     * @return Collection
     */
    public function listDirectories(Theme $theme, ?string $relative_path = null): Collection
    {
        $currentPath = $theme->upload_path . ($relative_path ? "/$relative_path" : '');
        $directoriesArray = $this->storageRemote->directories($currentPath);

        return ThemeDirectoryObject::fromPathArray($theme, $directoriesArray);
    }

    /**
     * Listar arquivos enviados de um Tema num determinado path
     *
     * @param Theme       $theme
     * @param string|null $relative_path
     *
     * @return Collection
     */
    public function listFiles(Theme $theme, ?string $relative_path = null): Collection
    {
        $currentPath = $theme->upload_path . ($relative_path ? "/$relative_path" : '');
        $filesArray = $this->storageRemote->files($currentPath);

        return ThemeFileObject::fromPathArray($theme, $filesArray);
    }

    //endregion

    //region Actions
    public function rename(Theme $theme, string|ThemeFileObject $fileOrRelativePath, string $newName): false|ThemeFileObject
    {
        $file = $this->traitFileArgument($theme, $fileOrRelativePath);

        $fileNewPath = Str::remove($file->name, $file->path) . $newName;

        //dd($theme->assets->hasFile($file->theme_path), ThemeFileObject::fromPath($theme, $fileNewPath));


        //$renamesFile = ThemeFileObject::fromPath($theme, $fileNewPath);
        //dd($theme->assets->renameFile($file->theme_path, $renamesFile->theme_path), $theme->assets->resources->js->toArray());

        //Rename file
        if ($this->storageRemote->move($file->path, $fileNewPath)) {

            $renamedFile = ThemeFileObject::fromPath($theme, $fileNewPath);

            //Rename on Theme Bundle
            if ($theme->assets->hasFile($file->theme_path) && $theme->assets->renameFile($file->theme_path, $renamedFile->theme_path)) {
                $theme->save();
            }

            return $renamedFile;
        }

        return false;

    }

    public function destroy(Theme $theme, string|ThemeFileObject $fileOrRelativePath)
    {

        $file = $this->traitFileArgument($theme, $fileOrRelativePath);

        if ($this->storageRemote->delete($file->path)) {

            if ($theme->assets->hasFile($file->theme_path)) {
                $theme->assets->removeFile($file->theme_path);
                $theme->save();
            }

            return true;
        }


        return false;
    }
    //endregion

    //region Helpers

    protected function traitFileArgument(Theme $theme, string|ThemeFileObject $fileOrPath): ThemeFileObject
    {
        return is_string($fileOrPath) ? $this->getFile($theme, $fileOrPath) : $fileOrPath;
    }

    /**
     * Gerar Backtrace de um path do tema
     *
     * @param Theme       $theme
     * @param string|null $relative_path
     *
     * @return Collection
     */
    public function getBacktraceFrom(Theme $theme, ?string $relative_path = null): Collection
    {
        $backtrace = collect();

        if ($relative_path) {
            $relative_path_array = explode("/", $relative_path);

            $currentBTpath = '';

            $lastRpItem = array_pop($relative_path_array);

            if (count($relative_path_array) > 0) {

                /*$backtrace->add(ThemeDirectoryObject::make([
                                                               'name'       => 'Pasta Raiz',
                                                               'path'       => $theme->upload_path,
                                                               'theme_path' => null,
                                                               'url'        => $theme->cdnUrlTo(),
                                                               'uri'        => $theme->cdnUriTo(),
                                                               'cdn_url'    => $theme->cdnProxyUrlTo(),
                                                               'cdn_uri'    => $theme->cdnProxyUriTo(),
                                                           ]));*/


                foreach ($relative_path_array as $k => $rpItem) {
                    $currentBTpath .= ($k > 0 ? '/' : '') . $rpItem;
                    $backtrace->add(ThemeDirectoryObject::fromPath($theme, "{$theme->upload_path}/$currentBTpath"));
                }

            }


        }

        return $backtrace;
    }

    //endregion


}
