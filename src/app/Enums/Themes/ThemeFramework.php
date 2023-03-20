<?php

namespace ArtisanBR\Adminx\Common\App\Enums\Themes;

use ArtisanBR\Adminx\Common\App\Enums\Traits\EnumToArray;

enum ThemeFramework: string
{
    use EnumToArray;

    //FIELDS
    case Bootstrap5 = 'boostrap:5';
    case Bootstrap5_JS = 'boostrap:5-js';
    case Bootstrap5_CSS = 'boostrap:5-css';
    case Bootstrap4 = 'boostrap:4';
    case Bootstrap4_JS = 'boostrap:4-js';
    case Bootstrap4_CSS = 'boostrap:4-css';


    public function title(): string
    {
        return self::getTitleTo($this);
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::Bootstrap5 => 'Boostrap 5',
            self::Bootstrap5_JS => 'Boostrap 5 - Apenas o Javascript',
            self::Bootstrap5_CSS => 'Boostrap 5 - Apenas o Estilo CSS',
            self::Bootstrap4 => 'Boostrap 4',
            self::Bootstrap4_JS => 'Boostrap 4 - Apenas o Javascript',
            self::Bootstrap4_CSS => 'Boostrap 4 - Apenas o Estilo CSS',
            default => "Nenhum",
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
