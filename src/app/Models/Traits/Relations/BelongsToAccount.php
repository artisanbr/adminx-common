<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Models\Account;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this;
 */
trait BelongsToAccount
{
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
