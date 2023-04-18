<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Account;
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
