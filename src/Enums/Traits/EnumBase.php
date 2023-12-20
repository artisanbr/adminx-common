<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Enums\Traits;

trait EnumBase
{
    use EnumToArray;

    public function is($type): bool
    {
        return $this->value === $type;
    }

}
