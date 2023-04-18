<?php

namespace Adminx\Common\Enums\Themes;

use Adminx\Common\Enums\Traits\EnumToArray;

enum BreadcrumbSeparator: string
{
    use EnumToArray;

    //FIELDS
    case default = '/';
    case gt = '>';
    case rsaquo = '&rsaquo;';
    case raquo = '&raquo;';
    case bar = '|';
    case dot = '.';


    public function title(): string
    {
        return self::getTitleTo($this);
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::default => '/',
            self::gt => '>',
            self::rsaquo => '&rsaquo;',
            self::raquo => '&raquo;',
            self::bar => '|',
            self::dot => '•',
            default => "Nenhum",
        };
    }

    public function css(): string
    {
        return self::getCssTo($this);
    }

    public static function getCssTo($type): string
    {
        return '--bs-breadcrumb-divider: ' . match ($type) {
            self::default => "'/'",
            self::gt => "url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;)",
            self::rsaquo => "&rsaquo;",
            self::raquo => "&raquo;",
            self::bar => "'|'",
            self::dot => "'•'",
            default => "''",
        } . ';';
    }

    public static function titles(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->title(),
            self::cases()
        ));
    }

}
