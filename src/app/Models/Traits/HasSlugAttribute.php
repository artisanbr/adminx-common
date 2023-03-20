<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits;

use ArtisanBR\Adminx\Common\App\Libs\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasSlugAttribute
{
    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? Str::slug(Str::lower($this->title ?? $this->name ?? '')),
            set: static fn ($value) => Str::slug(Str::lower($value)),
        );
    }
}
