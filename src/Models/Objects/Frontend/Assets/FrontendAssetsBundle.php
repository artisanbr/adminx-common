<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets;

use Adminx\Common\Models\Objects\Frontend\Assets\Code\FrontendCssAssetsCode;
use Adminx\Common\Models\Objects\Frontend\Assets\Code\FrontendScssAssetsCode;
use Adminx\Common\Models\Objects\Frontend\FrontendHtmlObject;

/**
 * @property string $css_bundle_html
 */
class FrontendAssetsBundle extends GenericModel
{

    protected $fillable = [
        'js',
        'css',
        'scss',
        'head_script',
        'resources',
    ];

    protected $casts = [
        'js'              => FrontendJsAssetsBundle::class,
        'css'             => FrontendCssAssetsCode::class,
        'scss'            => FrontendScssAssetsCode::class,
        'head_script'     => FrontendHtmlObject::class,
        'css_bundle_html' => 'string',
        'resources'       => FrontendResourceAssetsBundle::class,
    ];

    protected $appends = ['css_bundle_html'];


    //region Helpers
    public function minify()
    {
        $this->css->minify();
        $this->scss->compile();
        $this->js->minify();
        $this->head_script->minify();

        return $this;
    }

    public function compile()
    {
        $this->scss->compile();

        return $this;
    }

    public function hasFile(string $file_path, array|string|bool $collections = false): bool
    {
        return $this->resources->hasFile($file_path, $collections);
    }

    public function whereIsFile(string $file_path, array|string|bool $collections = false): bool
    {
        return $this->resources->whereIsFile($file_path, $collections);
    }

    public function renameFile(string $file_path, string $new_file_path): bool
    {
        return $this->resources->renameFile($file_path, $new_file_path);
    }

    public function addFile(string $file_path, string|bool $collection = false)
    {
        return $this->resources->addFile($file_path, $collection);
    }

    public function removeFile(string $file_path, string|bool $collection = false)
    {
        return $this->resources->removeFile($file_path, $collection);

    }
    //endregion

    //region Attributes
    //region Gets
    protected function getCssBundleHtmlAttribute(): string
    {
        return $this->css->html . "\n" . $this->scss->html . "\n" . $this->resources->css->html;
    }
    //endregion
    //endregion
}
