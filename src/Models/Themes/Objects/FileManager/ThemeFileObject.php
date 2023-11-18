<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\FileManager;

use Adminx\Common\Libs\FileManager\Helpers\FileHelper;
use Adminx\Common\Models\Themes\Objects\FileManager\Abstract\ThemeFileManagerObject;
use Adminx\Common\Models\Themes\Theme;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;

class ThemeFileObject extends ThemeFileManagerObject
{

    public function __construct(array $attributes = [])
    {
        $this->addFillables([
                                'extension',
                                'icon',
                                'editable',
                                'is_bundle',
                                'can_bundle',
                                'content',
                            ]);
        $this->addCasts([
                            'extension'  => 'string',
                            'icon'       => 'string',
                            'content'    => 'string',
                            'editable'   => 'boolean',
                            'is_bundle'  => 'boolean',
                            'can_bundle' => 'boolean',
                        ]);
        parent::__construct($attributes);
    }

    //region Helpers
    public function isBundle(Theme $theme): bool
    {
        return match (true) {
            ($this->extension == 'css' && $theme->assets->resources->hasFile($this->theme_path, 'css')) => true,
            ($this->extension == 'js' && $theme->assets->resources->hasFile($this->theme_path, [
                    'js',
                    'head_js',
                ])) => true,
            default => false
        };
    }

    public static function fromThemePath(Theme $theme, string $theme_path): self
    {
        $file = parent::fromThemePath($theme, $theme_path);

        return $file->fill([
                               'is_bundle' => $file->isBundle($theme),
                               //'can_bundle' => $file->extension == 'css' || $file->extension == 'js',
                           ]);
    }

    public static function fromPath(Theme $theme, string $path): self
    {
        $file = parent::fromPath($theme, $path);

        return $file->fill([
                               'is_bundle' => $file->isBundle($theme),
                               //'can_bundle' => $file->extension == 'css' || $file->extension == 'js',
                           ]);
    }

    public static function fromThemePathArray(Theme $theme, array $theme_paths): Collection
    {
        $collection = collect();
        foreach ($theme_paths as $dir_path) {
            //$directory = ;
            $collection->add(self::fromThemePath($theme, $dir_path));
        }

        return $collection;
    }

    public static function fromPathArray(Theme $theme, array $paths): Collection
    {
        $collection = collect();
        foreach ($paths as $dir_path) {
            //$directory = ;
            $collection->add(self::fromPath($theme, $dir_path));
        }

        return $collection;
    }
    //endregion

    //region Attributes
    //region GET's
    protected function getExtensionAttribute()
    {
        if (empty($this->attributes["extension"] ?? null) && ($this->attributes["name"] ?? false)) {
            $this->setExtensionAttribute(FileHelper::getExtensionByFile($this->attributes["name"]));
        }

        return $this->attributes["extension"];
    }
    //endregion

    //region SET's
    protected function setNameAttribute($value)
    {
        if ($value != ($this->attributes['name'] ?? null)) {
            $this->setExtensionAttribute(FileHelper::getExtensionByFile($value));
            $this->attributes['name'] = $value;
        }

        return $this;

    }

    protected function setExtensionAttribute($value)
    {
        if ($value != ($this->attributes['extension'] ?? null)) {
            $this->attributes['extension'] = $value;

            /*$this->attributes['editable'] = match ($value) {
                'css', 'js', 'json' => true,
                default => false
            };*/

            $this->attributes['editable'] = collect(config('files.extensions.theme.editable'))->contains($value);
            $this->attributes['can_bundle'] = collect(config('files.extensions.theme.assets_bundle'))->contains($value);
            $this->attributes['is_image'] = collect(config('files.extensions.images'))->contains($value);

            //dd(collect(config('files.extensions.images'))->contains('png'));


            $iconBlade = match ($value) {
                'css' => '<x-kicon icon="css" size="2" />',
                'js' => '<x-kicon icon="js" size="2" />',
                'json' => '<x-kicon icon="scroll" paths="3" size="2" />',
                default => '<x-kicon icon="file" size="2" />'
            };


            $this->attributes["icon"] = Blade::render($iconBlade);
        }

        return $this;
    }
    //endregion
    //endregion
}
