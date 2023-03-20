<?php

namespace ArtisanBR\Adminx\Common\App\Models\Traits\Relations;

use ArtisanBR\Adminx\Common\App\Models\Page;
use Illuminate\Database\Eloquent\Model;

/**
 * @var Model $this;
 */
trait BelongsToPage
{
    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
