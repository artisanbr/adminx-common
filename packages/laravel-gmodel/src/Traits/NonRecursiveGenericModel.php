<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace ArtisanLabs\GModel\Traits;

trait NonRecursiveGenericModel
{
    /**
     * @throws JsonException
     */
    public function get($model, $key, $value, $attributes)
    {
        try {

            return ($this->isNullable() && is_null($value)) ? null : new static($this->castRawValue($value));

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
                     'error' => $e->getMessage()
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

            $currentAttributes = $this->castRawValue($attributes[$key] ?? []);

            $mergeResult = array_replace($currentAttributes, $this->castRawValue($value));
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

            return [$key => json_encode(self::make($this->castRawValue($value))->jsonSerialize())];

        } catch (\Exception $e) {
            dump("exception set: $key", $value, $attributes);
            throw $e;
        }
    }
}
