<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models;

use Adminx\Common\Models\Bases\EloquentModelBase;
use Adminx\Common\Models\Sites\Site;
use Adminx\Common\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ramsey\Uuid\Uuid;

class Account extends EloquentModelBase
{
    use HasFactory;

    public const ACCOUNT_TYPE_CLIENT = 0;
    public const ACCOUNT_TYPE_AGENCY = 1;
    public const ACCOUNT_TYPE_DEV    = 2;


    //region Overrides

    public function save(array $options = [])
    {
        if (!$this->id || !$this->public_id) {
            $this->public_id = Uuid::uuid1();
        }

        return parent::save($options);
    }

    //endregion

    //region RELATIONS

    public function users()
    {
        return $this->belongsToMany(User::class, 'account_users', 'account_id', 'user_id')->using(AccountUser::class);
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'account_sites', 'account_id', 'site_id')->using(AccountSite::class);
    }

    public function own_sites()
    {
        return $this->hasMany(Site::class, 'account_id', 'id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'account_id', 'id');
    }

    public function tags()
    {
        return $this->hasMany(Tag::class, 'account_id', 'id');
    }

    //endregion
}
