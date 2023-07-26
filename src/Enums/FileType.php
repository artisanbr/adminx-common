<?php

namespace Adminx\Common\Enums;

use Adminx\Common\Enums\Traits\EnumToArray;

enum FileType: string
{
    use EnumToArray;

    //Theme
    case Theme = 'theme';
    case ThemeAsset = 'theme.asset';
    case ThemeMedia = 'theme.media';

    //Page
    case PageSeo = 'page.seo';
    case PageUpload = 'page.upload';

    //Post
    case PostSeo = 'article.seo';
    case PostCover = 'article.cover';
    case PostUpload = 'article.upload';

    //CustomLists
    case CustomListItem = 'list.item';
    case CustomListContent = 'list.content';

}
