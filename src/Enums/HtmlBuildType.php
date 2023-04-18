<?php

namespace Adminx\Common\Enums;

use Adminx\Common\Enums\Traits\EnumToArray;

enum HtmlBuildType: string
{
    use EnumToArray;

    //FIELDS
    case PageContent = 'raw_html';
    case AdvancedHtml = 'advanced_html';
    case Widget = 'widget';
    case Section = 'section';
    case Group = 'group';
    case Component = 'component';


}
