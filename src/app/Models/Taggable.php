<?php

namespace ArtisanBR\Adminx\Common\App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Taggable extends Pivot
{

    //region RELATIONS

    public function tag(){
        return $this->belongsTo(Tag::class);
    }

    //endregion
}
