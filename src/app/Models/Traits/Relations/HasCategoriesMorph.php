<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Models\Categorizable;
use ArtisanBR\Adminx\Common\App\Models\Category;
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
