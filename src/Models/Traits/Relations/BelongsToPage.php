<?php

namespace Adminx\Common\Models\Traits\Relations;

use Adminx\Common\Models\Pages\Page;
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
