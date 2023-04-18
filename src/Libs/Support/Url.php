<?php

namespace Adminx\Common\Libs\Support;

use Illuminate\Support\Facades\URL as CoreURL;

class Url extends CoreURL
{

    public static function previousDomain(): string
    {
        return self::getDomain(self::previous());
    }

    public static function getDomain($Address): string
    {
        $parseUrl = parse_url(trim($Address));
        return trim($parseUrl['host'] ?: array_shift(explode('/', $parseUrl['path'], 2)));
    }

    public static function previous(){
        return self::getUrlPath(self::previousPath());
    }

    public static function getUrlPath($Url){
        $parseUrl = parse_url(trim($Url));
        return $parseUrl['path'];
    }

}
