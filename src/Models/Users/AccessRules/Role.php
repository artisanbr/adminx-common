<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Users\AccessRules;


use Adminx\Common\Libs\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Role extends \Spatie\Permission\Models\Role
{

    protected $appends = [
        //"permissions_list"
    ];


    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn($value = null) => !empty($value) ? $value : Str::ucwords($this->name)
        );
    }

    protected function permissionsList(): Attribute{
        return Attribute::make(
            get: fn() => $this->permissions->pluck("name")
        );
    }

}
