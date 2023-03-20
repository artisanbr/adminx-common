<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AccountUser extends Pivot
{
    protected $table = 'account_users';
}
