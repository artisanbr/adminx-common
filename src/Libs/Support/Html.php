<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Libs\Support;

class Html
{
    public static function attributesFromArray(array $array){
        return collect($array)->filter()->reduce(fn($carry, $value, $key) => $carry . $key . '="' . $value . '" ');
    }
}