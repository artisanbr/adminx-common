<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Article;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this;
 */
trait HasArticles
{
    public function articles(){
        return $this->hasMany(Article::class)->ordered();
    }
}
