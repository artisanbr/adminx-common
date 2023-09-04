<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Models\Templates\Enums;

use Adminx\Common\Enums\Traits\EnumBase;
use Adminx\Common\Enums\Traits\EnumWithTitles;

enum TemplateRenderEngine: string
{
    use EnumWithTitles, EnumBase;

    case Twig = 'twig';
    case Blade = 'blade';


    public function description(): string
    {
        return self::getDescriptionTo($this);
    }

    public static function getDescriptionTo($type): string
    {
        return match ($type) {
            self::Blade => 'Renderização alternativa para casos especificos da plataforma',
            self::Twig => 'Renderização padrão da plataforma.',
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
            self::Blade => 'Alternativo',
            self::Twig => 'Padrão',
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
