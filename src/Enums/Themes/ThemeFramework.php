<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Enums\Themes;

use Adminx\Common\Enums\Traits\EnumToArray;

enum ThemeFramework: string
{
    use EnumToArray;

    //FIELDS
    case Bootstrap5 = 'bootstrap:5';
    case Bootstrap5_JS = 'bootstrap:5-js';
    case Bootstrap5_CSS = 'bootstrap:5-css';
    case Bootstrap4 = 'bootstrap:4';
    case Bootstrap4_JS = 'bootstrap:4-js';
    case Bootstrap4_CSS = 'bootstrap:4-css';

    public function isBootstrap(){
        return str_contains($this->value, 'bootstrap');
    }

    public function bootstrapVersion(){
        return $this->isBootstrap() ? (str_contains($this->value, '5') ? 5 : 4) : false;
    }

    public function title(): string
    {
        return self::getTitleTo($this);
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::Bootstrap5 => 'Bootstrap 5',
            self::Bootstrap5_JS => 'Bootstrap 5 - Apenas o Javascript',
            self::Bootstrap5_CSS => 'Bootstrap 5 - Apenas o Estilo CSS',
            self::Bootstrap4 => 'Bootstrap 4',
            self::Bootstrap4_JS => 'Bootstrap 4 - Apenas o Javascript',
            self::Bootstrap4_CSS => 'Bootstrap 4 - Apenas o Estilo CSS',
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
