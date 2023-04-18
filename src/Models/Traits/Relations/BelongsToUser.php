<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this;
 */
trait BelongsToUser
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
