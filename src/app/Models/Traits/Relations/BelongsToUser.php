<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Models\User;
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
