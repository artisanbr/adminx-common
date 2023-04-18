<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Site;
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
