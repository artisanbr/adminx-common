<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Post;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this;
 */
trait HasPosts
{
    public function posts(){
        return $this->hasMany(Post::class)->ordered();
    }
}
