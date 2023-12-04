<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\Abstract;

use ArtisanLabs\GModel\GenericModel;
use Butschster\Head\Packages\Package;
use Illuminate\Support\Collection;

abstract class ThemeConfigLibraryAbstract extends GenericModel
{

    protected $fillable = [
        'enable',
        'version',
        'strict',
    ];

    /*protected $attributes = [
        'enable' => true,
    ];*/

    protected $casts = [
        'enable'       => 'bool',
        'version'      => 'string',
        'cdn_base_uri' => 'string',
        //'strict'       => 'string',
    ];

    /**
     * @var array<string>
     */
    protected array $js_files = [];

    /**
     * @var array<string>
     */
    protected array $css_files = [];

    const strictTypes = [
        false => 'Completo',
        'js' => 'Apenas o Javascript',
        'css' => 'Apenas o CSS',
    ];

    public function strictTypes(): Collection
    {
        return collect(self::strictTypes);
    }

    //region Helpers
    public function cdnFile($file): string
    {
        return $this->getCdnBaseUriAttribute().$file;
    }

    public function registerMetaPackage(Package &$package): Package
    {
        if ($this->enable) {

            $strict = $this->strict;
            if (!$strict || $strict === 'js') {
                foreach ($this->js_files as $file) {
                    $package->addScript($file, $this->cdnFile($file), [
                        'crossorigin'    => 'anonymous',
                        'referrerpolicy' => 'no-referrer',

                    ]);
                }
            }

            if (!$strict || $strict === 'css') {
                foreach ($this->css_files as $file) {
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
    //endregion

    //region SET's
    //protected function setAttribute($value){}

    //endregion
    //endregion

}
