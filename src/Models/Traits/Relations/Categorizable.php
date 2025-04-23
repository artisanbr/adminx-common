<?php
/*
 * Copyright (c) 2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this
 */
trait Categorizable
{
    public function scopeHasCategory(Builder $query, string $category): Builder
    {
        return $query->whereHas('categories', fn(Builder $q) => $q
            ->whereUrl('slug', $category));
    }

    public function scopeHasAnyCategory(Builder $query, array $categories): Builder
    {
        return $query->whereHas(
            'categories',
            fn(Builder $q) => $q
                ->whereIn('slug', $categories)
                ->orWhereIn('id', $categories)
        );
    }

    public function scopeHasAllCategories(Builder $query, array $categories): Builder
    {
        return $query->whereHas(
            'categories',
            function (Builder $query) use ($categories) {

                foreach ($categories as $category) {
                    $query = $query->whereUrl($category);
                }

                return $query;
            }
        );
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

}
