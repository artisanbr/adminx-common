<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this;
 */
trait HasParent
{
    //region Scopes
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeChildrenOf(Builder $query, $parent_id): Builder
    {
        return $query->where('parent_id', $parent_id);
    }
    //endregion


    //region Relations
    public function parent()
    {
        return $this->belongsTo(__CLASS__);
    }

    public function children()
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id');
    }
    //endregion
}
