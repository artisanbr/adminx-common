<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\FileManager\Abstract;

use Adminx\Common\Libs\Support\Str;
use Adminx\Common\Models\Themes\Theme;
use ArtisanLabs\GModel\GenericModel;
use Delight\Random\Random;
use Illuminate\Support\Collection;

abstract class ThemeFileManagerObject extends GenericModel
{
    protected $fillable = [
        'id',
        'name',
        'path',
        'theme_path',
        'url',
        'uri',
        'cdn_url',
        'cdn_uri',
    ];

    protected $casts = [
        'id'         => 'string',
        'name'       => 'string',
        'path'       => 'string',
        'theme_path' => 'string',
        'url'        => 'string',
        'uri'        => 'string',
        'cdn_url'    => 'string',
        'cdn_uri'    => 'string',
    ];

    /**
     * Create model from theme path
     */
    public static function fromThemePath(Theme $theme, string $theme_path): self
    {
        $fullPath = "{$theme->upload_path}/{$theme_path}";
        return self::make([
                              'id'         => Random::alphanumericString(3) . base_convert(time(), 10, 32),
                              'name'       => collect(explode('/', $theme_path))->last(),
                              'path'       => $fullPath,
                              'theme_path' => $theme_path,
                              'url'        => $theme->cdnUrlTo($fullPath),
                              'uri'        => $theme->cdnUriTo($fullPath),
                              'cdn_url'    => $theme->cdnProxyUrlTo($fullPath),
                              'cdn_uri'    => $theme->cdnProxyUriTo($fullPath),
                          ]);
    }

    public static function fromPath(Theme $theme, string $path): self
    {
        return self::make([
                              'id'         => Random::alphanumericString(3) . base_convert(time(), 10, 32),
                              'name'       => collect(explode('/', $path))->last(),
                              'path'       => $path,
                              'theme_path' => Str::remove($theme->upload_path . "/", $path),
                              'url'        => $theme->cdnUrlTo($path),
                              'uri'        => $theme->cdnUriTo($path),
                              'cdn_url'    => $theme->cdnProxyUrlTo($path),
                              'cdn_uri'    => $theme->cdnProxyUriTo($path),
                          ]);
    }

    /**
     * Create model collection from theme path array
     */
    public static function fromThemePathArray(Theme $theme, array $paths): Collection
    {
        $collection = collect();
        foreach ($paths as $dir_path) {
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
}
