<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
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


    /**
     * @param array<string|self> $types
     *
     * @return bool
     */
    public function isAny(array $types): bool
    {
        return collect($types)->map(fn(string|self $type) => is_string($type) ? $type : ($type->value ?? $type))->contains($this->value);
    }

}
