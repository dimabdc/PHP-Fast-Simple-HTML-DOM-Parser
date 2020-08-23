<?php

namespace Tests\FastSimpleHTMLDom;

use FastSimpleHTMLDom\SelectorConverter;
use Tests\TestCase;

class SelectorConverterTest extends TestCase
{
    /**
     * @dataProvider selectorsContainsDataProvider
     */
    public function testToXPath($cssSelector, $needle)
    {
        $this->assertContains($needle, SelectorConverter::toXPath($cssSelector));
    }

    public function selectorsContainsDataProvider()
    {
        return [
            [
                'text',
                'text()',
            ],
            [
                'div text',
                'text()',
            ],
            [
                'div span, text',
                'text()',
            ],
            [
                'div|text',
                'text()',
            ],
            [
                'div>text',
                'text()',
            ],
            [
                'div > text',
                'text()',
            ],
            [
                'div,text',
                'text()',
            ],
            [
                'div, text',
                'text()',
            ],
            [
                'comment',
                'comment()',
            ],
            [
                'div comment',
                'comment()',
            ],
            [
                'div span, comment',
                'comment()',
            ],
            [
                'div|comment',
                'comment()',
            ],
            [
                'div>comment',
                'comment()',
            ],
            [
                'div > comment',
                'comment()',
            ],
            [
                'div,comment',
                'comment()',
            ],
            [
                'div, comment',
                'comment()',
            ],
        ];
    }

    /**
     * @dataProvider selectorsNotContainsDataProvider
     */
    public function testToXPathNotElements($cssSelector, $needle)
    {
        $this->assertNotContains($needle, SelectorConverter::toXPath($cssSelector));
    }

    public function selectorsNotContainsDataProvider()
    {
        return [
            [
                '.text',
                'text()',
            ],
            [
                '#text',
                'text()',
            ],
            [
                'foo[text]',
                'text()',
            ],
            [
                'div[foo|text]',
                'text()',
            ],
            [
                'div[text|foo]',
                'text()',
            ],
            [
                'div[class*=text]',
                'text()',
            ],
            [
                'div[class*="text"]',
                'text()',
            ],
            [
                '.comment',
                'comment()',
            ],
            [
                '#comment',
                'comment()',
            ],
            [
                'foo[comment]',
                'comment()',
            ],
            [
                'div[foo|comment]',
                'comment()',
            ],
            [
                'div[comment|foo]',
                'comment()',
            ],
            [
                'div[class*=comment]',
                'comment()',
            ],
            [
                'div[class*="comment"]',
                'comment()',
            ],
        ];
    }
}
