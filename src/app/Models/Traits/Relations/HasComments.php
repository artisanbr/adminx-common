<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Models\Comment;
use ArtisanBR\Adminx\Common\App\Models\File;
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
