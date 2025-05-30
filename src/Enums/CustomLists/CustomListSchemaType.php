<?php
/*
 * Copyright (c) 2023-2025. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Enums\CustomLists;

use Adminx\Common\Enums\Traits\EnumBase;
use Adminx\Common\Enums\Traits\EnumToArray;
use Adminx\Common\Enums\Traits\EnumWithTitles;
use Adminx\Common\Models\Casts\AsCollectionOf;
use Adminx\Common\Models\CustomLists\Object\Schemas\CustomListSchemaColumn;
use Adminx\Common\Models\CustomLists\Object\Values\ButtonValue;
use Adminx\Common\Models\CustomLists\Object\Values\ImageValue;
use Adminx\Common\Models\CustomLists\Object\Values\PDFValue;
use Adminx\Common\Models\Objects\Seo\Seo;

enum CustomListSchemaType: string
{
    use EnumBase, EnumToArray, EnumWithTitles;

    //FIELDS
    case Text = 'text';
    case Image = 'image';
    case ImageCollection = 'collection:image';
    case PDF = 'pdf';
    case PDFCollection = 'collection:pdf';
    case TextArea = 'textarea';
    case Html = 'html';
    //case Button = 'button';
    case ButtonCollection = 'collection:button';
    case SEO = 'seo';

    //Todo:
    //case File = 'file';
    //case FileCollection = 'collection:file';


    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::Text => 'Texto Simples',
            self::Image => 'Imagem',
            self::ImageCollection => 'Coleção de Imagens',
            self::PDF => 'Arquivo PDF',
            self::PDFCollection => 'Coleção de PDFs',
            self::TextArea => 'Caixa de Texto',
            self::Html => 'HTML',
            //self::Button => 'Botão',
            self::ButtonCollection => 'Coleção de Botões',
            self::SEO => 'Otimização de Busca (SEO)',
            //self::Rating => '<h3>Botão</h3> Botão geralmente usado em slides, banners e páginas internas',
        };
    }

    //region Views
    public function viewName(): string
    {
        return str($this->value)->replace('collection:', 'collection-');
    }

    public function optionsView(): string
    {
        return "livewire.elements.custom-lists.schema.options." . $this->viewName();
    }

    public function itemEditingView(): string
    {

        return match ($this) {
            self::ButtonCollection,
            self::ImageCollection,
            self::PDFCollection => "elements.custom-lists.custom-list-items.custom-fields." . $this->viewName(),

            default => "livewire.elements.custom-lists.custom-list-items.custom-fields." . $this->viewName()
        };
    }

    //endregion


    //region Value Casts
    public
    static function getValueCastTo($type): ?string
    {
        return match ($type) {
            self::Text, self::Html, self::TextArea => 'string',
            self::Image => ImageValue::class,
            self::ImageCollection => AsCollectionOf::class . ':' . ImageValue::class,
            self::PDF => PDFValue::class,
            self::PDFCollection => AsCollectionOf::class . ':' . PDFValue::class,

            //self::Button => ButtonValue::class,
            self::ButtonCollection => AsCollectionOf::class . ':' . ButtonValue::class . ',' . 'position',
            self::SEO => Seo::class,
            default => null,
            //self::Rating => '<h3>Botão</h3> Botão geralmente usado em slides, banners e páginas internas',
        };
    }

    public
    function valueCast(): ?string
    {
        return self::getValueCastTo($this);
    }

    public
    static function valueCasts(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->valueCast(),
            self::cases()
        ));
    }

    //endregion

    //region Label
    public
    static function getLabelTo($type): string
    {
        return match ($type) {
            self::Text => 'Texto',
            self::Image => 'Imagem',
            self::ImageCollection => 'Imagens',
            self::PDF => 'Arquivo PDF',
            self::PDFCollection => 'Coleção de PDFs',
            self::TextArea => 'Texto Longo',
            self::Html => 'HTML',
            //self::Button => 'Botão',
            self::ButtonCollection => 'Botões',
            self::SEO => 'Otimização de Busca (SEO)',
            //self::Rating => '<h3>Botão</h3> Botão geralmente usado em slides, banners e páginas internas',
        };
    }

    public
    function label(): string
    {
        return self::getLabelTo($this);
    }

    public
    static function labels(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->label(),
            self::cases()
        ));
    }

    //endregion

    //region Icon
    public
    static function getIconTo($type): string
    {
        return match ($type) {
            self::Text => 'text',
            self::Image => 'picture',
            self::ImageCollection => 'add-item',
            self::PDF => 'file',
            self::PDFCollection => 'some-files',
            self::TextArea => 'text-align-justify-center',
            self::Html => 'code',
            //self::Button => 'toggle-off',
            self::ButtonCollection => 'copy',
            self::SEO => 'magnifier',
            //self::Rating => '<h3>Botão</h3> Botão geralmente usado em slides, banners e páginas internas',
        };
    }

    public
    function icon(): string
    {
        return self::getIconTo($this);
    }

    public
    static function icons(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->icon(),
            self::cases()
        ));
    }

    //endregion

    //region Description
    public
    static function getDescriptionTo($type): string
    {
        return match ($type) {
            self::Text => 'Campo de texto curto inserido em uma tag <code>&lt;input /&gt;</code>',
            self::Image => 'Campo para Upload de uma <b>imagem</b> enviada com ou sem um tamanho definido.',
            self::ImageCollection => 'Campo para Upload de múltiplas imagens enviadas com ou sem um tamanho definido, útil para construção de <b>Galerias</b>',
            self::PDF => 'Campo para Upload de um <b>PDF</b> de no máximo 15mb. (Para arquivos maiores recomenda-se o armazenar em algum Drive para não prejudicar o desempenho do site)',
            self::PDFCollection => 'Campo para Upload de múltiplos PDFs de no máximo 15mb cada um. (Para arquivos maiores recomenda-se o armazenar em algum Drive para não prejudicar o desempenho do site)',
            self::TextArea => 'Caixa de texto longo inserido em uma tag <code>&lt;textarea /&gt;</code>',
            self::Html => 'Campo de HTML com editor visual "WYSIWYG" ou de código avançado',
            //self::Button => 'Botão geralmente usado em slides, banners e páginas internas',
            self::ButtonCollection => 'Lista de Botões geralmente usado em slides, banners e páginas internas, para usos avançados.',
            self::SEO => 'Adiciona os campos de meta título, palavras-chave, descrição e imagem para otimização de busca',
            //self::Rating => '<h3>Botão</h3> Botão geralmente usado em slides, banners e páginas internas',
        };
    }

    public
    function description(): string
    {
        return self::getDescriptionTo($this);
    }

    public
    static function descriptions(): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->description(),
            self::cases()
        ));
    }

    //endregion

    //region Example Codes
    public
    static function getExampleCodesTo(self $type, CustomListSchemaColumn $column, string $varPrefix = 'listItem'): array
    {
        $default = [
            "{{ {$varPrefix}.{$column->slug} }}",
            "{{ {$varPrefix}.data.{$column->slug} }}",
        ];

        return match ($type) {
            default => [
                ...$default,
            ],
            self::Image, self::PDF => [
                "{{ {$varPrefix}.{$column->slug}_url }}",
                "{{ {$varPrefix}.{$column->slug}.url }}",
            ],
            self::ImageCollection, self::PDFCollection => [
                "{{ {$varPrefix}.{$column->slug}[0].url }}",
            ],
            //self::TextArea => [],
            //self::Html => [],
            //self::Button => [],
            self::ButtonCollection => [
                "{{ {$varPrefix}.{$column->slug}[0].url }}",
                "{{ {$varPrefix}.{$column->slug}[0].content }}",
                "{{ {$varPrefix}.{$column->slug}[0].position }}",
                "{{ {$varPrefix}.{$column->slug}[0].external }}",
            ],
            self::SEO => [
                "{{ {$varPrefix}.{$column->slug}[0].title }}",
                "{{ {$varPrefix}.{$column->slug}[0].description }}",
                "{{ {$varPrefix}.{$column->slug}[0].keywords }}",
            ],
            //self::Rating => '<h3>Botão</h3> Botão geralmente usado em slides, banners e páginas internas',
        };
    }

    public
    function exampleCodes(CustomListSchemaColumn $column): array
    {
        return self::getExampleCodesTo($this, $column);
    }

    public
    static function allExampleCodes(CustomListSchemaColumn $column): array
    {
        return array_combine(array_column(self::cases(), 'value'), array_map(
            fn(self $item) => $item->exampleCodes($column),
            self::cases()
        ));
    }
    //endregion

}
