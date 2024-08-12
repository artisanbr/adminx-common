<?php
/*
 * Copyright (c) 2023-2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Enums\CustomLists;

use Adminx\Common\Enums\Traits\EnumToArray;

enum CustomListConfigListMode: string
{
    use EnumToArray;

    //FIELDS
    case DataTables = 'datatables';
    case Draggable = 'draggable';

    public function viewName(): string
    {
        return $this->value.'-list';
    }

    public function title(): string
    {
        return self::getTitleTo($this);
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::DataTables => '<h3>Tabela Dinâmica</h3> Recomendado para listagens com muitos itens que requerem paginação',
            self::Draggable => '<h3>Listagem Simplificada</h3> Recomendado para listas menores, possui reposicionamento de itens no formato "arrastar e soltar".',
        };
    }

    public static function titles(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->title(),
            self::cases()
        ));
    }

}
