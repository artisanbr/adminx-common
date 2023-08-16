<?php

namespace Adminx\Common\Models\Templates\Enums;

use Adminx\Common\Enums\Traits\EnumBase;
use Adminx\Common\Enums\Traits\EnumWithTitles;

enum TemplateRenderMode: string
{
    use EnumWithTitles, EnumBase;

    case Ajax = 'ajax';
    case Static = 'static';
    case PreRendered = 'pre-rendered';


    public function description(): string
    {
        return self::getDescriptionTo($this);
    }

    public static function getDescriptionTo($type): string
    {
        return match ($type) {
            self::Ajax => 'Renderização leve porém pode ser incompatível com alguns plugins JavaScript.',
            self::Static => 'Renderização médiana porém com muita compatibilidade.',
            self::PreRendered => 'Renderização muito leve, porém dados dinâmicos e tags serão armazenados no estado em que estiverem ao salvar o modelo (Não é recomendado o uso de tags dinâmicas)',
            default => 'Nenhum',
        };
    }

    public static function descriptions(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            static fn(self $item) => $item->title(),
            self::cases()
        ));
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::Ajax => 'Dinâmica (AJAX/axios)',
            self::Static => 'Estática (On-demand)',
            self::PreRendered => 'Pré Renderizado',
            default => 'Nenhum',
        };
    }

    //region Components
    /*public function bladeComponent(): string
    {
        return self::getBladeComponentTo($this);
    }

    public static function getBladeComponentTo($type): string
    {
        return match ($type) {
            self::Code => 'field.editor.code',
            default => 'field.editor.tinymce',
        };
    }*/
    //endregion

}
