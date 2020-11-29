<?php namespace Ssddanbrown\HtmlDiff;

class Utils
{
    // TODO - Check escaping here
    private static $openingTagRegex = '/^\s*<[^>]+>\s*$/';
    private static $closingTagRegex = '/^\s*<\/[^>]+>\s*$/';
    private static $tagWordRegex = '/<[^\s>]+/';
    private static $whitespaceRegex = '/^(\s|&nbsp;)+$/';
    private static $wordRegex = '/[\w\#@]+/';

    private static $specialCaseWordTags = ["<img"];

    public static function isTag(?string $item): bool
    {
        $specials = array_filter(static::$specialCaseWordTags, function($val) use ($item) {
            return !is_null($val) && strpos($item, $val) === 0;
        });
        if (count($specials) > 0) {
            return false;
        }

        return static::isOpeningTag($item) || static::isClosingTag($item);
    }

    private static function isOpeningTag(string $item): bool
    {
        return preg_match(static::$openingTagRegex, $item) === 1;
    }

    private static function isClosingTag(string $item): bool
    {
        return preg_match(static::$closingTagRegex, $item) === 1;
    }

    public static function stripTagAttributes(string $word): string
    {
        $matches = [];
        $tag = preg_match(static::$tagWordRegex, $word, $matches);
        $isClosing = substr($tag, -2) === '/>';
        return $tag . ($isClosing ? '/>' : '>');
    }

    public static function wrapText(string $text, string $tagName, string $cssClass): string
    {
        return "<{$tagName} class=\"{$cssClass}\">{$text}</{$tagName}>";
    }

    public static function isStartOfTag(string $val): bool
    {
        return $val === '<';
    }

    public static function isEndOfTag(string $val): bool
    {
        return $val === '>';
    }

    public static function isStartOfEntity(string $val): bool
    {
        return $val === '&';
    }

    public static function isEndOfEntity(string $val): bool
    {
        return $val === ';';
    }

    public static function isWhiteSpace(string $val): bool
    {
        return preg_match(static::$whitespaceRegex, $val) === 1;
    }

    public static function stripAnyAttributes(string $word): string
    {
        if (static::isTag($word)) {
            return static::stripTagAttributes($word);
        }
        return $word;
    }

    public static function isWord(string $text): bool
    {
        return preg_match(static::$wordRegex, $text) === 1;
    }
}