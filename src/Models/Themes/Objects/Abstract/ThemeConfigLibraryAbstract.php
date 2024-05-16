<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\Abstract;

use Adminx\Common\Models\Themes\Enums\ThemeAssetCompile;
use ArtisanLabs\GModel\GenericModel;
use Butschster\Head\Packages\Package;
use Illuminate\Support\Collection;

/**
 * @property ThemeAssetCompile $compile
 */
abstract class ThemeConfigLibraryAbstract extends GenericModel
{

    protected $fillable = [
        'enable',
        'compile',
        'version',
        'strict',
    ];

    /*protected $attributes = [
        'enable' => true,
    ];*/

    protected $casts = [
        'enable'       => 'bool',
        'compile'       => ThemeAssetCompile::class,
        'version'      => 'string',
        'cdn_base_uri' => 'string',
        //'strict'       => 'string',
    ];

    /**
     * @var array<string>
     */
    protected array $included_js_files = [];

    /**
     * @var array<string>
     */
    protected array $included_css_files = [];

    const strictTypes = [
        false => 'Completo',
        'js'  => 'Apenas o Javascript',
        'css' => 'Apenas o CSS',
    ];

    public function strictTypes(): Collection
    {
        return collect(self::strictTypes);
    }

    //region Helpers
    public function cdnFile($file): string
    {
        return $this->getCdnBaseUriAttribute() . $file;
    }

    public function registerMetaPackage(Package &$package): Package
    {
        if ($this->enable) {

            $strict = $this->strict;
            if ((!$strict || $strict === 'js') && !$this->compile->isAny([
                ThemeAssetCompile::All,
                ThemeAssetCompile::Js
                                                                         ])) {
                foreach ($this->included_js_files as $file) {
                    $package->addScript($file, $this->cdnFile($file), [
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',

                    ]);
                }
            }

            if ((!$strict || $strict === 'css') && !$this->compile->isAny([
                ThemeAssetCompile::All,
                ThemeAssetCompile::Css
                                                                          ])) {
                foreach ($this->included_css_files as $file) {
                    $package->addStyle($file, $this->cdnFile($file), [
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',

                    ]);
                }
            }
        }

        return $package;

    }
    //endregion

    //region Attributes
    //region GET's
    abstract protected function getCdnBaseUriAttribute(): string;

    protected function getCssCompileFilesAttribute(): array
    {
        return $this->enable && $this->compile->isAny([
            ThemeAssetCompile::All,
            ThemeAssetCompile::Css
                                                      ]) ? $this->included_css_files : [];
    }

    protected function getJsCompileFilesAttribute(): array
    {
        return $this->enable && $this->compile->isAny([
            ThemeAssetCompile::All,
            ThemeAssetCompile::Js
                                                      ]) ? $this->included_js_files : [];
    }

    protected function getCssFilesAttribute(): array
    {
        return $this->enable ? $this->included_css_files : [];
    }

    protected function getJsFilesAttribute(): array
    {
        return $this->enable ? $this->included_js_files : [];
    }


    protected function getCdnCssCompileFilesAttribute(): array
    {
        return $this->enable && $this->compile->isAny([
                                                          ThemeAssetCompile::All,
                                                          ThemeAssetCompile::Css
                                                      ]) ? $this->getCdnCssFilesAttribute() : [];
    }

    protected function getCdnJsCompileFilesAttribute(): array
    {
        return $this->enable && $this->compile->isAny([
                                                          ThemeAssetCompile::All,
                                                          ThemeAssetCompile::Js
                                                      ]) ? $this->getCdnJsFilesAttribute() : [];
    }

    protected function getCdnCssFilesAttribute(): array
    {
        return collect($this->getCssFilesAttribute())->map(fn($file) => $this->cdnFile($file))->toArray();
    }

    protected function getCdnJsFilesAttribute(): array
    {
        return collect($this->getJsFilesAttribute())->map(fn($file) => $this->cdnFile($file))->toArray();
    }
    //endregion

    //region SET's
    //protected function setAttribute($value){}

    //endregion
    //endregion

}
