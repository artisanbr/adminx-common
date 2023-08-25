<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

trait HasGenericConfig
{
    protected function getHasConfigAttribute(): bool{
        return (isset($this->attributes['config']) && !empty($this->attributes['config'])) || !empty($this->config ?? null);
    }
}
