<?php

namespace Adminx\Common\Enums\CustomLists;

use App\DataTables\AdminX\Elements\CustomLists\CustomListItems\CustomListItemDataTable;
use App\DataTables\AdminX\Elements\CustomLists\CustomListItems\CustomListItemTestimonialDataTable;
use Adminx\Common\Enums\Traits\EnumToArray;
use Adminx\Common\Models\CustomLists\CustomList;
use Adminx\Common\Models\CustomLists\CustomListHtml;
use Adminx\Common\Models\CustomLists\CustomListImageSlider;
use Adminx\Common\Models\CustomLists\CustomListTestimonials;

enum CustomListType: string
{
    use EnumToArray;

    //FIELDS
    case ImageSlider = 'slider.image';
    case Testimonials = 'testimonials';
    case Clients = 'clients';
    case Events = 'events';
    case Gallery = 'gallery';
    case ImageGallery = 'gallery.image';
    case VideoGallery = 'gallery.video';
    case Files = 'files';
    case Posts = 'articles';
    case HTML = 'html';
    //todo: Carousel

    public function formView(): string
    {
        return 'adminx.Elements.CustomLists.cadastro.CustomListItem.Forms.'.$this->name;
    }

    public function listItemView(): string
    {
        return 'adminx.Elements.CustomLists.cadastro.CustomListItem.ListItem.'.$this->name;
    }

    public function listView(): string
    {
        return self::getListViewTo($this);
    }

    public static function getListViewTo($type): string
    {
        return match ($type) {
            self::ImageSlider,
            self::HTML => 'adminx.Elements.CustomLists.cadastro.CustomListItem.list-draggable',
            default => 'adminx.Elements.CustomLists.cadastro.CustomListItem.list-datatables'
        };
    }

    public function datatableClass(): string
    {
        return self::getDatatableClassTo($this);
    }

    public static function getDatatableClassTo($type): string
    {
        return match ($type) {
            self::Testimonials => CustomListItemTestimonialDataTable::class,
            default => CustomListItemDataTable::class
        };
    }

    public function itemTypes(): array
    {
        return self::getItemTypesTo($this);
    }

    public static function getItemTypesTo($type): array
    {
        return match ($type) {
            self::ImageSlider => [
                CustomListItemType::ImageSlide->value
            ],
            self::Testimonials => [
                CustomListItemType::Testimonial->value
            ],
            self::HTML => [
                CustomListItemType::HTML->value
            ],
            default => CustomListItemType::values()
        };
    }

    public function mountClass(): string
    {
        return self::getMountClassTo($this);
    }

    public static function getMountClassTo($type): string
    {
        return match ($type) {
            self::ImageSlider => CustomListImageSlider::class,
            self::Testimonials => CustomListTestimonials::class,
            self::HTML => CustomListHtml::class,
            default => CustomList::class
        };
    }

    public function title(): string
    {
        return self::getTitleTo($this);
    }

    public static function getTitleTo($type): string
    {
        return match ($type) {
            self::Gallery => 'Galeria',
            self::ImageGallery => 'Galeria de Imagens',
            self::VideoGallery => 'Galeria de vídeos',
            self::Files => 'Arquivos',
            self::Posts => 'Postagens',
            self::HTML => 'HTML\'s Personalizados',
            self::ImageSlider => 'Slider de Imagens',
            self::Testimonials => 'Depoimentos ou Testemunhos',
            self::Clients => 'Clientes',
            self::Events => 'Eventos',
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
