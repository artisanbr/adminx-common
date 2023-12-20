<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Pages\Enums;

use Adminx\Common\Enums\Traits\EnumBase;
use Adminx\Common\Enums\Traits\EnumWithTitles;

enum PageStatus: string
{
    use EnumBase, EnumWithTitles;

    case Published = 'published';
    case Draft = 'draft';
    case Archived = 'archived';

    //region Title
    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::Published => 'Publicado',
            self::Archived => 'Arquivado',
            self::Draft => 'Rascunho',
        };
    }
    //endregion

    //region Color
    public function color(): string
    {
        return self::getColorTo($this);
    }

    public static function colors(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->color(),
            self::cases()
        ));
    }
    public static function getColorTo($type): string
    {
        return match ($type) {
            self::Published => 'success',
            self::Archived => 'danger',
            self::Draft => 'primary',
        };
    }
    //endregion
}
