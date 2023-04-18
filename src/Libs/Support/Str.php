<?php

namespace Adminx\Common\Libs\Support;

use Illuminate\Support\Str as CoreStr;

class Str extends CoreStr
{
    public static function initials($string): string{
        return preg_replace('/(?<!\s|^)[a-zA-Z\ ]/mi', '', (string) $string);
    }

    public static function replaceNative($search, $replace, $string): string{
        return str_replace($search, $replace, $string);
    }

    public static function ucwords($string, $separators = " \t\r\n\f\v"): string
    {
        return ucwords($string, $separators);
    }

    public static function removeHTML($text, $allowed_tags = null): string
    {
        return strip_tags($text, $allowed_tags);
    }

    public static function mostFrequentWords(string|array $content): array
    {
        if(is_string($content)){
            //Remove HTML
            $content = self::removeHTML($content);

            $content = explode(" ", $content);
        }

        $counts = array_count_values(str_word_count(implode(" ",$content),1));
        arsort($counts);
        return $counts;
    }

    public static function removeBlankLines($text): string{
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", PHP_EOL, $text);
    }
}
