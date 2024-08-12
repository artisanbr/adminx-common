<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\CustomLists\Object\Schemas;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class CustomListSchemaColumnSynth extends Synth
{
    public static string $key = 'custom-list-schema-column';

    public static function match($target): bool
    {
        return $target instanceof CustomListSchemaColumn;
    }

    public function dehydrate($target): array
    {
        return [$target->toArray(), []];
    }

    public function hydrate($value): CustomListSchemaColumn
    {
        return new CustomListSchemaColumn($value);
    }

    public function get(&$target, $key)
    {
        return $target->{$key};
    }

    public function set(&$target, $key, $value)
    {
        $target->{$key} = $value;
    }
}
