<?php

namespace Adminx\Common\Enums;

use Adminx\Common\Enums\Traits\EnumBase;
use Adminx\Common\Enums\Traits\EnumToArray;
use Adminx\Common\Enums\Traits\EnumWithTitles;
use Illuminate\Support\Facades\Blade;

/**
 * Tipos de Itens de Menu
 */
enum MenuItemType: string
{
    use EnumBase, EnumWithTitles;

    //Relacionado a outra model (Page, Article, CustomListItem)
    case Morph = 'morph';
    //Possui sub-itens
    case Submenu = 'submenu';
    //Apenas um link simples
    case Link = 'link';


    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::Morph => 'Link Dinâmico',
            self::Submenu => 'Sub-menu',
            self::Link => 'Link Personalizado',
            default => 'Nenhum',
        };
    }


    public static function getDescriptionTo($type): string
    {
        return match ($type) {
            self::Morph => 'Vincular a uma fonte de dados como uma Página ou Artigo.',
            self::Submenu => 'Preencher com sub-itens ou gerar automaticamente à partir de uma fonte de dados.',
            self::Link => 'Defina um URL personalizado para este item.',
        };
    }
    public function description(): string
    {
        return self::getDescriptionTo($this);
    }
    public static function descriptions(): array
    {
        return array_combine(array_column(self::cases(), 'name'), array_map(
            fn (self $item) => $item->description(),
            self::cases()
        ));
    }


    public static function getIconTo($type): string
    {
        return Blade::render('<x-kicon size="2x" class="me-4" ' . (match ($type) {
                                 self::Morph => 'icon="data" paths="5"',
                                 self::Submenu => 'icon="abstract-14"',
                                 self::Link => 'icon="fasten"',
                             } ?? ''). '/>');
    }
    public function icon(): string
    {
        return self::getIconTo($this);
    }
    public static function icons(): array
    {
        return array_combine(array_column(self::cases(), 'name'), array_map(
            fn (self $item) => $item->icon(),
            self::cases()
        ));
    }

    public static function select2(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn (self $item) => '<div class="d-flex align-items-center">'.$item->icon().'<span class="d-block fw-bold text-start"><span class="text-dark fw-bolder d-block fs-5">'.$item->title().'</span><span class="text-muted fw-bold fs-7">'.$item->description().'</span>
    </span></div>',
            self::cases()
        ));
    }

}
