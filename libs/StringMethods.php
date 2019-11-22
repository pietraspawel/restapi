<?php

namespace pietras;

/**
 * Some methods to handle strings.
 */
class StringMethods
{
    /**
     * Convert polish letters to latin.
     *
     * @param  string $string String to convert.
     * @return string Converted string.
     */
    public static function plToLatin(string $string): string
    {
        $string = preg_replace('/Ę/', 'E', $string);
        $string = preg_replace('/ę/', 'e', $string);
        $string = preg_replace('/Ó/', 'O', $string);
        $string = preg_replace('/ó/', 'o', $string);
        $string = preg_replace('/Ą/', 'A', $string);
        $string = preg_replace('/ą/', 'a', $string);
        $string = preg_replace('/Ś/', 'S', $string);
        $string = preg_replace('/ś/', 's', $string);
        $string = preg_replace('/Ł/', 'L', $string);
        $string = preg_replace('/ł/', 'l', $string);
        $string = preg_replace('/Ż/', 'Z', $string);
        $string = preg_replace('/ż/', 'z', $string);
        $string = preg_replace('/Ź/', 'Z', $string);
        $string = preg_replace('/ź/', 'z', $string);
        $string = preg_replace('/Ć/', 'C', $string);
        $string = preg_replace('/ć/', 'c', $string);
        $string = preg_replace('/Ń/', 'N', $string);
        $string = preg_replace('/ń/', 'n', $string);

        return $string;
    }

    /**
     * Convert polish letters to latin and change string to lowercase.
     *
     * @param  string $string String to convert.
     * @return string Converted string.
     */
    public static function smallPlToLatin(string $string): string
    {
        $string = self::plToLatin($string);
        $string = strtolower($string);
        return $string;
    }

    /**
     * Convert all applicable characters to HTML entities including quotes.
     *
     * @param  string $string String to convert.
     * @return string Converted string.
     */
    public static function safeString(string $string): string
    {
        return htmlentities($string, ENT_QUOTES | ENT_HTML401);
    }
}
