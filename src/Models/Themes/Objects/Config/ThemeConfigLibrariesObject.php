<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Themes\Objects\Config;

use Adminx\Common\Models\Themes\Objects\Config\Libraries\BoostrapLibrary;
use Adminx\Common\Models\Themes\Objects\Config\Libraries\FontAwesomeLibrary;
use Adminx\Common\Models\Themes\Objects\Config\Libraries\JQueryLibrary;
use Adminx\Common\Models\Themes\Objects\Config\Libraries\JQueryUiLibrary;
use Butschster\Head\Packages\Package;
use ArtisanBR\GenericModel\GenericModel;

class ThemeConfigLibrariesObject extends GenericModel
{

    protected $fillable = [
        'jquery',
        'jquery_ui',
        'bootstrap',
        'fontawesome',
    ];

    protected $attributes = [
        'jquery' => [],
        'jquery_ui' => [],
        'bootstrap' => [],
        'fontawesome' => [],
    ];

    protected $casts = [
        'jquery' => JQueryLibrary::class,
        'jquery_ui' => JQueryUiLibrary::class,
        'bootstrap' => BoostrapLibrary::class,
        'fontawesome' => FontAwesomeLibrary::class,
    ];

    public function registerMetaPackage(Package &$package): Package{
        $this->jquery->registerMetaPackage($package);
        $this->jquery_ui->registerMetaPackage($package);
        $this->bootstrap->registerMetaPackage($package);
        $this->fontawesome->registerMetaPackage($package);

        return $package;
    }
}
