<?php
/*
 * Copyright (c) 2023. Tanda Interativa - Todos os Direitos Reservados
 * Desenvolvido por Renalcio Carlos Jr.
 */

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

    public static function removeHTML(string $text, $allowed_tags = null): string
    {
        //dd($text,html_entity_decode($text));
        return strip_tags($text, $allowed_tags);
    }

    public static function mostFrequentWords(string|array $string, $limitWords = 40, $minWordChars = 3): array
    {

        // Converte a string em um array de palavras
        $words = self::of(self::removeHTML($string))->explode(' ')->toArray();

        // Cria um array associativo com as palavras como chaves e o número de repetições como valores
        $word_counts = array_count_values($words);

        // Ordena o array decrescentemente por número de repetições
        arsort($word_counts);

        // Limita o array às 20 palavras mais repetidas
        $word_counts = array_slice($word_counts, 0, $limitWords);

        // Retorna o array
        return collect($word_counts)->filter(static fn($count, $word) => Str::length($word) > $minWordChars)->toArray();
    }

    public static function removeBlankLines($text): string{
        return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", PHP_EOL, $text);
    }
}
