<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Comment;
use Adminx\Common\Models\File;
use Illuminate\Database\Eloquent\Model;


/**
 * @var Model $this
 * @var Model self
 */
trait HasComments
{

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
