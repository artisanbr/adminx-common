<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Site;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this;
 */
trait BelongsToSite
{
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
