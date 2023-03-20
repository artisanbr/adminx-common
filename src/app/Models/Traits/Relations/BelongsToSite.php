<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Models\Site;
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
