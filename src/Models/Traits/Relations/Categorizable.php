<?php
/*
 * Copyright (c) 2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Category;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this ;
 */
trait Categorizable
{
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }
}
