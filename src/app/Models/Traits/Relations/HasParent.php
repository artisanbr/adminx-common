<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Models\Site;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this;
 */
trait HasParent
{
    public function parent()
    {
        return $this->belongsTo(__CLASS__);
    }

    public function children()
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id');
    }
}
