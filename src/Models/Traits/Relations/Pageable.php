<?php
/*
 * Copyright (c) 2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Pages\Page;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this ;
 */
trait Pageable
{
    public function page()
    {
        return $this->morphOne(Page::class, 'pageable')
                    ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END ASC')
                    ->orderBy('created_at', 'ASC');
    }


    public function pages()
    {
        return $this->morphMany(Page::class, 'pageable')
                    ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END ASC')
                    ->orderBy('created_at', 'ASC');
    }
}
