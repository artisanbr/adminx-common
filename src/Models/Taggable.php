<?php

namespace Adminx\Common\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Taggable extends Pivot
{

    //region RELATIONS

    public function tag(){
        return $this->belongsTo(Tag::class);
    }

    //endregion
}
