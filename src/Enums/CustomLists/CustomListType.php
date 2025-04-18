<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Enums\CustomLists;

use Adminx\Common\Enums\Traits\EnumBase;
use Adminx\Common\Enums\Traits\EnumWithTitles;

enum CustomListType: string
{
    use EnumBase, EnumWithTitles;

    //FIELDS
    //'slider.image', 'gallery.image' to: 'images'
    case Images = 'images';
    case Testimonials = 'testimonials';
    case HTML = 'html';

    //Todo:
    /*case Clients = 'clients';
    case Events = 'events';
    case Gallery = 'gallery';
    case Videos = 'videos';
    case Files = 'files';
    case Articles = 'articles';*/

    public function formView(): string
    {
        return 'adminx.Elements.CustomLists.cadastro.CustomListItem.Forms.' . $this->name;
    }

    public function listItemView(): string
    {
        return 'adminx.Elements.CustomLists.cadastro.CustomListItem.ListItem.' . $this->name;
    }

    public function listView(): string
    {
        return self::getListViewTo($this);
    }

    public static function getListViewTo($type): string
    {
        return match ($type) {
            self::HTML => 'adminx.Elements.CustomLists.cadastro.CustomListItem.list-draggable',
            default => 'adminx.Elements.CustomLists.cadastro.CustomListItem.list-datatables'
        };
    }

    public function itemTypes(): array
    {
        return self::getItemTypesTo($this);
    }

    public static function getItemTypesTo($type): array
    {
        return match ($type) {
            self::Images => [
                CustomListItemType::Image->value,
            ],
            self::Testimonials => [
                CustomListItemType::Testimonial->value,
            ],
            self::HTML => [
                CustomListItemType::HTML->value,
            ],
            default => CustomListItemType::values()
        };
    }

    /*public function dataClass(): string
    {
        return self::getDataClassTo($this);
    }

    public static function getDataClassTo($type): string
    {
        return match ($type) {
            self::Images => CustomListImageSlider::class,
            self::Testimonials => CustomListTestimonials::class,
            self::HTML => CustomListHtml::class,
            default => CustomList::class
        };
    }*/

    public function itemHasImageField(): bool
    {
        return self::getItemHasImageFieldTo($this);
    }

    public static function getItemHasImageFieldTo($type): bool
    {
        return match ($type) {
            self::HTML,
            self::Images => true,
            default => false,
        };
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::HTML => 'HTML\'s Personalizados',
            self::Images => 'Coleção de Imagens',
            self::Testimonials => 'Depoimentos ou Testemunhos',
            /*self::Gallery => 'Galeria de Imagens e Vídeos (em breve)',
            self::Videos => 'Coleção de Vídeos (em breve)',
            self::Files => 'Arquivos (em breve)',
            self::Articles => 'Artigos (em breve)',
            self::Clients => 'Clientes (em breve)',
            self::Events => 'Eventos (em breve)',*/
        };
    }

    public function schemaColumns(): array
    {
        return self::getSchemaColumnsTo($this);
    }

    public static function getSchemaColumnsTo($type): array
    {
        return match ($type) {
            self::HTML => [
                [
                    'position' => 1,
                    'name'     => 'Descrição',
                    'slug'     => 'description',
                    'type'     => CustomListSchemaType::TextArea->value,
                ],
                [
                    'position' => 2,
                    'name'     => 'Conteúdo',
                    'slug'     => 'content',
                    'type'     => CustomListSchemaType::Html->value,
                ],
                [
                    'position' => 3,
                    'name'     => 'Imagem',
                    'slug'     => 'image',
                    'type'     => CustomListSchemaType::Image->value,
                ],
                [
                    'position' => 4,
                    'name'     => 'Otimização de Busca (SEO)',
                    'slug'     => 'seo',
                    'type'     => CustomListSchemaType::SEO->value,
                ],
            ],
            self::Images => [
                [
                    'position' => 1,
                    'name'     => 'Imagem',
                    'slug'     => 'image',
                    'type'     => CustomListSchemaType::Image->value,
                ],
                [
                    'position' => 2,
                    'name'     => 'Imagem Mobile',
                    'slug'     => 'mobile_image',
                    'type'     => CustomListSchemaType::Image->value,
                ],
                [
                    'position' => 3,
                    'name'     => 'Conteúdo',
                    'slug'     => 'content',
                    'type'     => CustomListSchemaType::Html->value,
                ],
                [
                    'position' => 4,
                    'name'     => 'Descrição',
                    'slug'     => 'description',
                    'type'     => CustomListSchemaType::Html->value,
                ],
                [
                    'position' => 5,
                    'name'     => 'Botões',
                    'slug'     => 'buttons',
                    'type'     => CustomListSchemaType::ButtonCollection->value,
                ],
            ],
            self::Testimonials => [
                [
                    'position' => 1,
                    'name'     => 'Sub-título',
                    'slug'     => 'subtitle',
                    'type'     => CustomListSchemaType::Text->value,

                ],
                [
                    'position' => 2,
                    'name'     => 'Classificação do Depoimento',
                    'slug'     => 'rating',
                    'default_value' => '5',
                    'type'     => CustomListSchemaType::Text->value,
                    'data' => [
                        'help_text' => 'Insira um número menor ou igual que a Classificação Máxima'
                    ]
                ],
                [
                    'position' => 3,
                    'name'     => 'Classificação Máxima',
                    'slug'     => 'max_rating',
                    'type'     => CustomListSchemaType::Text->value,
                    'default_value' => '5',
                    'data' => [
                        'help_text' => 'Deve ser maior ou igual que a Classificação do Depoimento'
                    ]
                ],
                [
                    'position' => 4,
                    'name'     => 'Imagem',
                    'slug'     => 'image',
                    'type'     => CustomListSchemaType::Image->value,
                ],
                [
                    'position' => 5,
                    'name'     => 'Depoimento',
                    'slug'     => 'content',
                    'type'     => CustomListSchemaType::Html->value,
                ],
            ],

            /*self::Gallery => 'Galeria de Imagens e Vídeos (em breve)',
            self::Videos => 'Coleção de Vídeos (em breve)',
            self::Files => 'Arquivos (em breve)',
            self::Articles => 'Artigos (em breve)',
            self::Clients => 'Clientes (em breve)',
            self::Events => 'Eventos (em breve)',*/
        };
    }

    public static function allSchemasColumns(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->schemaColumns(),
            self::cases()
        ));
    }


}
