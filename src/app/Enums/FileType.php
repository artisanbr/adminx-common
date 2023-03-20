<?php

namespace ArtisanBR\Adminx\Common\App\Enums;

use ArtisanBR\Adminx\Common\App\Enums\Traits\EnumToArray;

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
    case PostSeo = 'post.seo';
    case PostCover = 'post.cover';
    case PostUpload = 'post.upload';

    //CustomLists
    case CustomListItem = 'list.item';
    case CustomListContent = 'list.content';

}
