<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Models\Post;
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
