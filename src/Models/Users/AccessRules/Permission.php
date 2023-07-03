<?php

namespace Adminx\Common\Models\Users\AccessRules;


use Adminx\Common\Libs\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Permission extends \Spatie\Permission\Models\Permission
{

    protected $appends = [
        //'roles_list',
    ];

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn($value = null) => !empty($value) ? $value : Str::ucwords($this->name)
        );
    }

    protected function rolesList(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->roles->pluck("id")
        );
    }
}
