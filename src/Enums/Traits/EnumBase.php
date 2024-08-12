<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Enums\Traits;

trait EnumBase
{
    use EnumToArray;

    public function is(string|int|self $type): bool
    {
        return $this == $type || $this?->value === $type;
    }


    /**
     * @param array<string|self|array> $types
     *
     * @return bool
     */
    public function isAny(...$types): bool
    {
        if(is_array($types[0] ?? null)){

            $types = $types[0];
        }else{
            //dd(Collection::wrap($types));
        }
        return collect($types)->map(fn(string|self $type) => is_string($type) ? $type : ($type->value ?? $type))->contains($this->value);
    }

}
