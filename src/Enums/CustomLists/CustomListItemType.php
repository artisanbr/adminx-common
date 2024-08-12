<?php
/*
 * Copyright (c) 2024. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

namespace Adminx\Common\Enums\CustomLists;

use Adminx\Common\Enums\Traits\EnumToArray;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\CustomListItemHtmlData;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\CustomListItemImageData;
use Adminx\Common\Models\CustomLists\Object\CustomListItemDatas\CustomListItemTestimonialData;

enum CustomListItemType: string
{
    use EnumToArray;

    //case ImageSlide = 'slide.image': to: image;
    case Image = 'image';
    case Testimonial = 'testimonial';
    case HTML = 'html';

    //Todo:
    /*case Video = 'video';
    case File = 'file';
    case Article = 'article';*/

    public function dataClass(): string
    {
        return self::getDataClassTo($this);
    }

    public static function getDataClassTo($type): string
    {
        return match ($type) {
            self::Image => CustomListItemImageData::class,
            self::Testimonial => CustomListItemTestimonialData::class,
            self::HTML => CustomListItemHtmlData::class,
            //default => CustomListItem::class
        };
    }


    public function title(): string
    {
        return self::getTitleTo($this);
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::Image => 'Imagem',
            self::Testimonial => 'Depoimento/Testemunho',
            self::HTML => 'HTML',
            /*self::Video => 'Vídeo',
            self::File => 'Arquivo',*/
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
