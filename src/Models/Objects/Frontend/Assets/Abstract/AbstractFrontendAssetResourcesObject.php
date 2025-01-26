<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
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

    protected $mergeAttributesOnSet = false;

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

    /**
     * @throws JsonException
     */
    public function get($model, $key, $value, $attributes)
    {
        try {

            return ($this->isNullable() && is_null($value)) ? null : new static($this->traitRawValue($value));

        } catch (\Exception $e) {
            //dump("exception get: $key", $value, $attributes[$key]);
            dump([
                     'exception type' => 'get',
                     'key'            => $key,
                     'value'          => $value,

                     //'result' => ($this->isNullable() && empty($value)) ? null : new static($this->castRawValue($value)),

                     'attributes' => $attributes,
                     //'model'      => $model,
                     //'trace'      => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10),
                     'error'      => $e->getMessage(),
                 ]);
            throw $e;
        }
    }


    /**
     * @throws JsonException
     */
    public function set($model, $key, $value, $attributes)
    {
        /*if ($key == 'separator') {
            dd("gModel set: $key", $value, $attributes[$key], $attributes, $model);
            dd(static::make($currentAttributes)->fill($this->buildCastAttributes($value))->jsonSerialize());
        }*/

        /* dd([
                  'type'              => 'set',
                  'key'               => $key,
                  'value'             => $value,
                  //'currentAttributes' => $currentAttributes,
                  'model_current'             => $model->{$key} ?? null,
                  //'merge'             => $mergeResult,
                  //'result'            => [$key => json_encode($mergeResult)],

                  //'attributes' => $attributes,
                  'attributes_current' => $attributes[$key] ?? null,
                  //'this'      => $this,
                  //'trace'      => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 25),
                  'model_array'             => $model->toArray(),
                  'this_array'             => $this->toArray(),
              ]);*/

        try {

            //Se o valor for nulo e a model atual for nullable
            if (is_null($value) && $this->isNullable()) {
                return [$key => null]; //null;
            }

            $currentAttributes = $this->traitRawValue($attributes[$key] ?? []);

            $mergeResult = array_replace($currentAttributes, $this->traitRawValue($value));
            //$mergeResult = array_replace($currentAttributes, $this->castRawValue($value));

            //$mergeResult = collect($currentAttributes)->replaceRecursive($this->castRawValue($value))->toArray();
            //$mergeResult = $this->castRawValue($value);

            /*if ((Str::contains(get_class($model), 'Resource') && $key == 'css') ||  $key == 'items') {
                dump([
                         'type'              => 'set',
                         'key'               => $key,
                         'value'             => $value,
                         //'currentAttributes' => $currentAttributes,
                         'current'             => $model->{$key},
                         //'merge'             => $mergeResult,
                         //'result'            => [$key => json_encode($mergeResult)],

                         //'attributes' => $attributes,
                         //'attributes_current' => $attributes[$key] ?? null,
                         //'model_array'      => $this->toArray(),
                         //'model'      => $this,
                         //'trace'      => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 25),
                     ]);
            }*/

            return [$key => json_encode(self::make($this->traitRawValue($value))->jsonSerialize())];

        } catch (\Exception $e) {
            dump("exception set: $key", $value, $attributes);
            throw $e;
        }
    }


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

        if (!$this->hasFile($itemArray['url'])) {
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
        })/*->unique('url')*/->sortBy('position')->values();
    }

    public function bundleList(): Collection
    {
        return $this->listToOrder()->where('bundle', true);
    }

    public function bundleMainList(): Collection
    {
        return $this->bundleList()->where('defer', '!=', true);
    }

    public function bundleDeferList(): Collection
    {
        return $this->bundleList()->where('defer', true);
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
