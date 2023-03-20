<?php

namespace ArtisanBR\Adminx\Common\App\Enums;

use ArtisanBR\Adminx\Common\App\Enums\Traits\EnumToArray;

enum ElementType: string
{
    use EnumToArray;

    //FIELDS
    case RawHtml = 'raw_html';
    case AdvancedHtml = 'advanced_html';
    case Widget = 'widget';
    case Section = 'section';
    case Group = 'group';
    case Component = 'component';


}
