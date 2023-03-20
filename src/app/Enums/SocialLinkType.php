<?php

namespace ArtisanBR\Adminx\Common\App\Enums;

use ArtisanBR\Adminx\Common\App\Enums\Traits\EnumToArray;

enum SocialLinkType: string
{
    use EnumToArray;

    //FIELDS
    case Facebook = 'facebook';
    case Youtube = 'youtube';
    case Instagram = 'instagram';
    case Twitter = 'twitter';
    case Linkedin = 'linkedin';
    case Telegram = 'telegram';


    /*public function _title(): string
    {
        return self::title($this);
    }

    public static function title($type): string
    {
        return match ($type) {
        };
    }

    public static function titles(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            function (self $item) {
                return $item->_title();
            },
            self::cases()
        ));
    }*/

    public function icon(): string
    {
        return self::getIconTo($this);
    }


    public function iconHtml(): string
    {
        return self::iconHtmlTo($this);
    }

    public static function getIconTo($type): string
    {
        return match ($type) {
            self::Youtube => 'fab fa-youtube',
            default => "fab fa-{$type->value}",
        };
    }
    public static function iconHtmlTo($type): string
    {
        return '<i class="'.self::getIconTo($type).'"></i>';
    }

    public static function icons(): array
    {
        return array_combine(array_column(self::cases(), 'name'), array_map(
            fn (self $item) => $item->icon(),
            self::cases()
        ));
    }
}
