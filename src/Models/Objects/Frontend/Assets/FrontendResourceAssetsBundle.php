<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets;

use Adminx\Common\Libs\FileManager\Helpers\FileHelper;
use Adminx\Common\Models\Objects\Frontend\Assets\Resources\FrontendCssAssetsResources;
use Adminx\Common\Models\Objects\Frontend\Assets\Resources\FrontendJsAssetsResources;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

class FrontendResourceAssetsBundle extends GenericModel
{

    protected $fillable = [
        'head_js',
        'js',
        'css',
    ];

    protected $casts = [
        'head_js' => FrontendJsAssetsResources::class,
        'js'      => FrontendJsAssetsResources::class,
        'css'     => FrontendCssAssetsResources::class,
    ];

    /*protected $attributes = [
        'head' => [],
        'before_body' => [],
        'after_body' => [],
    ];*/

    //region Helpers
    public function minify()
    {
        $this->head_js->minify();
        $this->js->minify();
        $this->css->minify();

        return $this;
    }

    /**
     * Verifica se um arquivo existe em alguma das collections
     */
    public function hasFile(string $file_path, array|string|bool $collections = false): bool
    {
        return (bool) $this->whereIsFile($file_path, $collections);
    }

    /**
     * Retorna o nome da primeira collection que contiver o arquivo, se não existir retorna 'false'
     */
    public function whereIsFile(string $file_path, array|string|bool $collections = false): string|bool
    {
        $whereCheck = Collection::wrap($collections ?: ['head_js', 'js', 'css']);

        foreach ($whereCheck->toArray() as $collection) {
            if (($this->{$collection} ?? false) && method_exists($this->{$collection}, 'hasFile') && $this->{$collection}->hasFile($file_path)) {
                return $collection;
            }
        }

        return false;
    }

    public function renameFile(string $file_path, string $new_file_path, string|bool $collection = false): bool
    {
        if(!$collection){
            $collection = $this->whereIsFile($file_path);

            if(!$collection){
                return false;
            }
        }

        return $this->{$collection}->renameFile($file_path, $new_file_path);
    }

    /**
     * Adiciona um arquivo a uma das collections possiveis, se não informar uma collection será definida uma baseada no
     * tipo de arquivo considerando apenas JS e CSS.
     */
    public function addFile(string $file_path, string|bool $collection = false)
    {

        if ($collection && is_string($collection) && ($this->{$collection} ?? false)) {
            return $this->{$collection}->addFile($file_path);
        }

        $extension = FileHelper::getExtensionByFile($file_path);

        return match ($extension) {
            'css' => $this->css->addFile($file_path),
            'js' => $this->js->addFile($file_path),
            default => false,
        };
    }

    /**
     * Excluir um arquvio de uma collection, se não informar a collection excluirá de todas
     */
    public function removeFile(string $file_path, string|bool $collection = false)
    {
        if ($collection && is_string($collection) && ($this->{$collection} ?? false)) {
            return $this->{$collection}->removeFile($file_path);
        }

        $this->css->removeFile($file_path);
        $this->js->removeFile($file_path);
        $this->head_js->removeFile($file_path);

        return $this;
    }
    //endregion

    //region Attributes
    //region GET's
    protected function getHtmlCssAttribute()
    {
        return $this->css->html;
    }

    protected function getHtmlJsAttribute()
    {
        return $this->js->html;
    }

    protected function getHtmlHeadJsAttribute()
    {
        return $this->head_js->html;
    }
    //endregion

    //region SET's
    //protected function setAttribute($value){}

    //endregion
    //endregion
}
