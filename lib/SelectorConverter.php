<?php

namespace FastSimpleHTMLDom;

use Symfony\Component\CssSelector\CssSelectorConverter;

class SelectorConverter
{
    protected static $compiled = [];

    /**
     * @param $selector
     *
     * @return mixed|string
     * @throws \RuntimeException
     */
    public static function toXPath($selector)
    {
        if (isset(self::$compiled[$selector])){
            return self::$compiled[$selector];
        }

        if (!class_exists('Symfony\\Component\\CssSelector\\CssSelectorConverter')) {
            throw new \RuntimeException('Unable to filter with a CSS selector as the Symfony CssSelector 2.8+ is not installed (you can use filterXPath instead).');
        }

        $converter = new CssSelectorConverter(true);

        $xPathQuery = $converter->toXPath($selector);

        $xPathQuery = preg_replace_callback('/(:(\*\/)?|\/)(text|comment)(?=[\s]*[^.\w\[\]#]|$)/', static function (array $match) {
            return $match[0] . '()';
        }, $xPathQuery);

        self::$compiled[$selector] = $xPathQuery;

        return $xPathQuery;
    }
}
