<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Tag;
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
