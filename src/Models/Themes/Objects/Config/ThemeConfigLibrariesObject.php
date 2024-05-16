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
use ArtisanLabs\GModel\GenericModel;
use Butschster\Head\Packages\Package;

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

    protected function getCssCompileFilesAttribute(): array
    {

        $collect = collect();

        foreach ($this->attributes as $attribute => $value){
            $collect = $collect->merge($this->{$attribute}->css_compile_files ?? []);
        }


        return $collect->filter()->toArray();
    }

    protected function getJsCompileFilesAttribute(): array
    {
        $collect = collect();

        foreach ($this->attributes as $attribute => $value){
            $collect = $collect->merge($this->{$attribute}->js_compile_files ?? []);
        }

        return $collect->filter()->toArray();
    }


    protected function getCssFilesAttribute(): array
    {

        $collect = collect();

        foreach ($this->attributes as $attribute => $value){
            $collect = $collect->merge($this->{$attribute}->css_files ?? []);
        }


        return $collect->filter()->toArray();
    }

    protected function getJsFilesAttribute(): array
    {
        $collect = collect();

        foreach ($this->attributes as $attribute => $value){
            $collect = $collect->merge($this->{$attribute}->js_files ?? []);
        }

        return $collect->filter()->toArray();
    }

    protected function getCdnCssCompileFilesAttribute(): array
    {

        $collect = collect();

        foreach ($this->attributes as $attribute => $value){
            $collect = $collect->merge($this->{$attribute}->cdn_css_compile_files ?? []);
        }


        return $collect->filter()->toArray();
    }

    protected function getCdnJsCompileFilesAttribute(): array
    {
        $collect = collect();

        foreach ($this->attributes as $attribute => $value){
            $collect = $collect->merge($this->{$attribute}->cdn_js_compile_files ?? []);
        }

        return $collect->filter()->toArray();
    }

    protected function getCdnCssFilesAttribute(): array
    {
        $collect = collect();

        foreach ($this->attributes as $attribute => $value){
            $collect = $collect->merge($this->{$attribute}->cdn_css_files ?? []);
        }

        return $collect->filter()->toArray();
    }

    protected function getCdnJsFilesAttribute(): array
    {
        $collect = collect();

        foreach ($this->attributes as $attribute => $value){
            $collect = $collect->merge($this->{$attribute}->cdn_js_files ?? []);
        }

        return $collect->filter()->toArray();
    }
}
