<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Elements\Forms;

class FormElementDefines extends GenericModel
{

    protected $fillable = [
        'tag',
        'inline',
        'size_lg',
        'size_xl',
        'size',

    ];

    protected $attributes = [
        'size_sm' => 12,
        'size_md' => 12,
        'size_lg' => 12,
        'size_xl' => 12,
    ];

    protected $casts = [
        'size_sm' => 'int',
        'size_md' => 'int',
        'size_lg' => 'int',
        'size_xl' => 'int',
    ];

    protected function setSizeAttribute($value){
        $this->size_sm = $value;
        $this->size_md = $value;
        $this->size_lg = $value;
        $this->size_xl = $value;
    }
}
