<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this ;
 */
trait HasTagsMorph
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
