<?php

namespace ArtisanBR\Adminx\Common\App\Enums\core;

use ArtisanBR\Adminx\Common\App\Enums\Traits\EnumToArray;

enum ReportType: string
{
    use EnumToArray;

    //FIELDS
    case ReleaseNote = 'release.note';
    case KnowledgeBase = 'knowledge.base';
    case News = 'news';
    case Tip = 'tip';
    case Info = 'info';
    case Announcement = 'announcement';
    case Notice = 'notice';


    public function title(): string
    {
        return self::getTitleTo($this);
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::ReleaseNote => 'Notas de Versão',
            self::KnowledgeBase => 'Base de Conhecimento',
            self::News => 'Notícias',
            self::Tip => 'Dica',
            self::Announcement => 'Anúncio',
            self::Notice => 'Aviso',
            default => "Informativo",
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
