<?php

namespace Adminx\Common\Models\Traits;

trait HasGenericConfig
{
    protected function getHasConfigAttribute(): bool{
        return isset($this->attributes['config']) && !empty($this->attributes['config']);
    }
}
