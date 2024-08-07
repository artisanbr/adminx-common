<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Menus;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Menuable extends Pivot
{
    protected $table = 'menuables';

    public $timestamps = false;

    //region RELATIONS

    public function menu_item(){
        return $this->belongsTo(MenuItem::class);
    }

    public function model(){
        return $this->morphTo('menuables', 'menuable_type', 'menuable_id');
    }

    //endregion
}
