<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Categorizable;
use Adminx\Common\Models\Category;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this ;
 */
trait HasCategoriesMorph
{
    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }
}
