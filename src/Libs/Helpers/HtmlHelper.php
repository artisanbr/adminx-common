<?php
/**
 * Created by PhpStorm.
 * User: renalcio
 * Date: 26/12/17
 * Time: 10:42
 */

namespace Adminx\Common\Libs\Helpers;


use Illuminate\Support\Str;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use ScssPhp\ScssPhp\OutputStyle;

class HtmlHelper
{

    public static function isHTML($string)
    {
        return $string != strip_tags($string);
    }

    public static function hasTags(string $string, $tags, bool $all = false): bool
    {
        $tags = is_array($tags) && !is_string($tags) ? $tags : [$tags];

        return $all ? Str::containsAll($string, $tags) : Str::contains($string, $tags);
    }

    public static function hasAllTags(string $string, $tags): bool
    {
        return self::hasTags($string, $tags, true);
    }

    public static function removeTags(string $string, array|string $tags): string
    {
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        foreach ($tags as $tag) {

            $tag_name = str_replace(['<', '>'], '', $tag);

            $string = preg_replace("/<" . $tag_name . ".*?>(.*)?<\/" . $tag_name . ">/im", "$1", $string);
        }

        return $string;

    }

    // HTML Minifier
    public static function minifyHtml($input)
    {
        if (trim($input) === "") return $input;
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        },                             str_replace("\r", "", $input));
        // Minify inline CSS declaration(s)
        if (strpos($input, ' style=') !== false) {
            $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
                return '<' . $matches[1] . ' style=' . $matches[2] . self::minifyCss($matches[3]) . $matches[2];
            },                             $input);
        }
        if (strpos($input, '</style>') !== false) {
            $input = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function ($matches) {
                return '<style' . $matches[1] . '>' . self::minifyCss($matches[2]) . '</style>';
            },                             $input);
        }
        if (strpos($input, '</script>') !== false) {
            $input = preg_replace_callback('#<script(.*?)>(.*?)</script>#is', function ($matches) {
                return '<script' . $matches[1] . '>' . self::minifyJs($matches[2]) . '</script>';
            },                             $input);
        }

        return preg_replace(
            [
                // t = text
                // o = tag open
                // c = tag close
                // Keep important white-space(s) after self-closing HTML tag(s)
                '#<(img|input)(>| .*?>)#s',
                // Remove a line break and two or more white-space(s) between tag(s)
                '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s',
                // t+c || o+t
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s',
                // o+o || c+c
                '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s',
                // c+t || t+o || o+t -- separated by long white-space(s)
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s',
                // empty tag
                '#<(img|input)(>| .*?>)<\/\1>#s',
                // reset previous fix
                '#(&nbsp;)&nbsp;(?![<\s])#',
                // clean up ...
                '#(?<=\>)(&nbsp;)(?=\<)#',
                // --ibid
                // Remove HTML comment(s) except IE comment(s)
                '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s',
            ],
            [
                '<$1$2</$1>',
                '$1$2$3',
                '$1$2$3',
                '$1$2$3$4$5',
                '$1$2$3$4$5$6$7',
                '$1$2$3',
                '<$1$2',
                '$1 ',
                '$1',
                "",
            ],
            $input);
    }

    // CSS Minifier => http://ideone.com/Q5USEF + improvement(s)
    public static function minifyCss($input)
    {
        if (trim($input) === "") return $input;

        return preg_replace(
            [
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                '#(?<=[\s:,\-])0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s',
            ],
            [
                '$1',
                '$1$2$3$4$5$6$7',
                '$1',
                ':0',
                '$1:0 0',
                '.$1',
                '$1$3',
                '$1$2$4$5',
                '$1$2$3',
                '$1:0',
                '$1$2',
            ],
            $input);
    }

    // JavaScript Minifier
    public static function minifyJs($input)
    {
        if (trim($input) === "") return $input;

        return preg_replace(
            [
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i',
            ],
            [
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3',
            ],
            $input);
    }

    /**
     * Compile Sass/Scss
     *
     * @throws SassException
     */
    public static function compileSCSS($sass, $compress = false): string
    {
        $compiler = new Compiler();

        $compiler->setOutputStyle($compress ? OutputStyle::COMPRESSED : OutputStyle::EXPANDED);

        return $compiler->compileString($sass)->getCss();
    }

    public static function ldJsonScript($schema): string{

        if ($schema) {
            return '<script type="application/ld+json">' . json_encode($schema) . '</script>';
        }

        return '';
    }


}
