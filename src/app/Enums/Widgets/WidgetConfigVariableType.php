<?php

namespace ArtisanBR\Adminx\Common\App\Enums\Widgets;

use ArtisanBR\Adminx\Common\App\Enums\Traits\EnumToArray;
use ArtisanBR\Adminx\Common\App\Models\Generics\Widgets\WidgetConfigVariable;
use Illuminate\Support\Facades\Blade;

enum WidgetConfigVariableType: string
{
    use EnumToArray;

    //FIELDS
    case TextField = 'text.field';
    case TextArea = 'text.area';
    case NumberField = 'number.field';
    case Html = 'html';
    case Image = 'image';
    case Boolean = 'bool';
    case Select = 'select';
    case SelectMultiple = 'select.multiple';

    public function configField(WidgetConfigVariable $variable): string
    {
        return self::getConfigFieldTo($this, $variable);
    }

    public static function getConfigFieldTo($type, WidgetConfigVariable $variable): string
    {
        $blade = match ($type) {
            self::TextField => '<x-field.text size="sm" :label="$variable->title" name="config[]" no-id group-class="mb-0"/>',
            self::TextArea => 'Area de Texto',
            self::NumberField => 'Campo de Número',
            self::Html => 'Código HTML',
            self::Image => 'Imagem',
            self::Boolean => 'Verdadeiro ou Falso (Checkbox)',
            self::Select => 'Lista de Seleção',
            self::SelectMultiple => 'Lista de Seleção Múltipla',
            default => "Nenhum",
        };

        return Blade::render(match ($type) {
            self::TextField => '<x-field.text size="sm" :label="$variable->title" name="config[]" no-id group-class="mb-0"/>',
            self::TextArea => 'Area de Texto',
            self::NumberField => 'Campo de Número',
            self::Html => 'Código HTML',
            self::Image => 'Imagem',
            self::Boolean => 'Verdadeiro ou Falso (Checkbox)',
            self::Select => 'Lista de Seleção',
            self::SelectMultiple => 'Lista de Seleção Múltipla',
            default => "Nenhum",
        });
    }


    public function title(): string
    {
        return self::getTitleTo($this);
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::TextField => 'Campo de Texto',
            self::TextArea => 'Area de Texto',
            self::NumberField => 'Campo de Número',
            self::Html => 'Código HTML',
            self::Image => 'Imagem',
            self::Boolean => 'Verdadeiro ou Falso (Checkbox)',
            self::Select => 'Lista de Seleção',
            self::SelectMultiple => 'Lista de Seleção Múltipla',
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
