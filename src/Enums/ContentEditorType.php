<?php

namespace Adminx\Common\Enums;

use Adminx\Common\Enums\Traits\EnumToArray;

enum ContentEditorType: string
{
    use EnumToArray;

    case TinyMCE = 'tinymce';
    case Code = 'code';


    public function is($type): bool
    {
        return $this->value === $type;
    }

    public function title(): string
    {
        return self::getTitleTo($this);
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::TinyMCE => 'Editor Visual (TinyMCE)',
            self::Code => 'Editor Avançado de Código (Ace)',
            default => 'Nenhum',
        };
    }

    public static function titles(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->title(),
            self::cases()
        ));
    }

    //region Components
    public function bladeComponent(): string
    {
        return self::getBladeComponentTo($this);
    }

    public static function getBladeComponentTo($type): string
    {
        return match ($type) {
            self::Code => 'field.editor.code',
            default => 'field.editor.tinymce',
        };
    }
    //endregion

}
