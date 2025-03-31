<?php
/*
 * Copyright (c) 2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasSlugAttribute
{
    protected function slug(): Attribute
    {
        return Attribute::make(
            get: static fn($value) => $value,
            set: static fn($value) => !blank($value) ? str($value)->lower()->slug()->toString() : null,
        );
    }

    /*protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? Str::slug(Str::lower($this->title ?? $this->name ?? '')),
            set: static fn ($value) => Str::slug(Str::lower($value)),
        );
    }*/
}
