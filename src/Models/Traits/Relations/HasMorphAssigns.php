<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Libs\Helpers\MorphHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this;
 */
trait HasMorphAssigns
{
    public function scopeAssignedToBy(Builder $query, $pivot_table, $type_column, $id_column, $model_type, $model_id = null): Builder
    {

        $model_type = MorphHelper::resolveMorphType($model_type);

        return $query->whereHas($pivot_table, function (Builder $q) use ($type_column, $id_column, $model_type, $model_id) {

            $q->where($type_column, $model_type);

            if ($model_id) {
                $q->where($id_column, $model_id);
            }
        });
    }

    public function assignTo(Model $model, $morph_name, $morph_type_column = null, $morph_id_column = null): bool
    {
        return $this->assignToId($model::class, $model->id ?? null, $morph_name, $morph_type_column, $morph_id_column);
    }

    public function assignToId($model_type, $model_id, $morph_name, $morph_type_column = null, $morph_id_column = null): bool
    {
        $morph_type = MorphHelper::resolveMorphType($model_type);

        $morph_type_column = $morph_type_column ?? $morph_name.'_type';
        $morph_id_column = $morph_id_column ?? $morph_name.'_id';

        $this->attributes[$morph_type_column] = $morph_type;
        $this->attributes[$morph_id_column] = $model_id;

        return $this->save();
    }
}
