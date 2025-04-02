<?php
/*
 * Copyright (c) 2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Enums\Pages;

use Adminx\Common\Enums\Traits\EnumWithTitles;
use ArtisanBR\Goodies\Enums\Traits\EnumBase;

enum PageType: string
{
    use EnumBase, EnumWithTitles;

    case CustomPage = 'custom';
    case ArticlesPage = 'articles';
    case Blog = 'blog'; //Todo: remove
    case Form = 'form'; //Todo: remove


    public function useArticles(): bool
    {
        return match ($this) {
            self::ArticlesPage => true,
            default => false
        };
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::CustomPage => 'Página Personalizada',
            self::ArticlesPage => 'Página com Artigos',
            default => 'Nenhum',
        };
    }


    public function description(): string
    {
        return self::getTitleTo($this);
    }

    public static function getDescriptionTo($type): string
    {
        return match ($type) {
            self::ArticlesPage => 'Blog, notícias e artigos diversos com gerenciamento de postagens, categorias, tags, comentários e funcionalidades de SEO.',
            default => 'Página com conteúdo HTML personalizado.',
        };
    }

    public static function titles(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->title(),
            self::cases()
        ));
    }

    public static function selectOptions(): array
    {
        return collect(self::titles())->except(['blog','form'])->toArray();
    }

}
