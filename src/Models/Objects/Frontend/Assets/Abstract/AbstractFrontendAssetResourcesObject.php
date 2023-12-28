<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Objects\Frontend\Assets\Abstract;

use Adminx\Common\Libs\Helpers\HtmlHelper;
use ArtisanLabs\GModel\GenericModel;
use Illuminate\Support\Collection;

/**
 * @property Collection $items
 * @property string     $resources_html
 * @property string     $raw
 * @property string     $raw_minify
 * @property string     $raw_html
 * @property string     $html
 */
abstract class AbstractFrontendAssetResourcesObject extends GenericModel
{

    protected $fillable = [
        'items',
    ];

    protected $casts = [
        'items'       => 'collection',
        'html'        => 'string',
        'html_minify' => 'string',
    ];

    protected $appends = ['html'];

    protected $attributes = [
        'items' => [],
    ];


    //region Helpers
    public function minify(): static
    {
        $this->html_minify = HtmlHelper::minifyHtml($this->html);

        return $this;
    }

    public function hasFile($file_path): bool
    {
        return $this->items->where('url', $file_path)->count() > 0;
    }

    public function addFile(string|array $file_path): static
    {

        $itemArray = is_string($file_path) ? ['url' => $file_path] : $file_path;

        if(!$this->hasFile($itemArray['url'])){
            if (!isset($itemArray['position'])) {
                $itemArray['position'] = $this->items->count();
            }

            //$this->items = $this->items->add($itemArray);
            $this->items->add($itemArray);
        }

        return $this;
    }

    public function setFiles(array $files): static
    {

       $this->items = $files;

        return $this;
    }

    public function renameFile(string $file_path, string $new_file_path): bool
    {

        $renameItems = $this->items->map(function ($file) use ($file_path, $new_file_path) {

            if (is_array($file) && $file['url'] === $file_path) {

                $file['url'] = $new_file_path;


            }
            else if (is_object($file) && $file?->path === $file_path) {
                if (method_exists($file, 'fill')) {
                    $file->fill([
                                    'url' => $new_file_path,
                                ]);
                }
                else {
                    $file->path = $new_file_path;
                }
            }

            return $file;
        });

        $renamed = $this->items->toArray() === $renameItems->toArray();

        $this->items = $renameItems;

        return $renamed;
    }

    public function removeFile($file_path)
    {

        $this->items = $this->items->where('url', '!=', $file_path);

        return $this;
    }

    public function listToOrder(): Collection
    {
        return $this->items->map(function (AbstractFrontendAssetsResourceScript $file) {
            $file->append('id');
            return $file;
        })->unique('url')->sortBy('position')->values();
    }
    //endregion

    //region ATTRIBUTES
    //region GETS

    abstract protected function getHtmlAttribute();

    protected function getHtmlMinifyAttribute(): string
    {
        return HtmlHelper::minifyHtml($this->html);
    }

    //endregion
    //region SETS
    /*protected function setRawAttribute($value): static
    {
        $this->attributes['raw'] = $value;

        $this->minify();

        return $this;
    }*/
    //endregion
    //endregion
}
