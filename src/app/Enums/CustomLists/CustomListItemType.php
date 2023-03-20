<?php

namespace ArtisanBR\Adminx\Common\App\Enums\CustomLists;

use ArtisanBR\Adminx\Common\App\Enums\Traits\EnumToArray;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItem;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItemHtml;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItemImageSlider;
use ArtisanBR\Adminx\Common\App\Models\CustomLists\CustomListItems\CustomListItemTestimonials;

enum CustomListItemType: string
{
    use EnumToArray;

    case Image = 'image';
    case Video = 'video';
    case File = 'file';
    case Post = 'post';
    case HTML = 'html';
    case ImageSlide = 'slide.image';
    case Testimonial = 'testimonial';

    public function mountClass(): string
    {
        return self::getMountClassTo($this);
    }

    public static function getMountClassTo($type): string
    {
        return match ($type) {
            self::ImageSlide => CustomListItemImageSlider::class,
            self::Testimonial => CustomListItemTestimonials::class,
            self::HTML => CustomListItemHtml::class,
            default => CustomListItem::class
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
            self::Video => 'Vídeo',
            self::File => 'Arquivo',
            self::HTML => 'HTML',
            self::ImageSlide => 'Slide de Imagem',
            self::Testimonial => 'Depoimento/Testemunho',
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
