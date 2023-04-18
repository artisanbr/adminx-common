<?php

namespace Adminx\Common\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Categorizable extends Pivot
{
    protected $table = 'categorizables';

    public $timestamps = false;

    //region RELATIONS

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function model(){
        return $this->morphTo('categorizables', 'categorizable_type', 'categorizable_id');
    }

    //endregion
}
