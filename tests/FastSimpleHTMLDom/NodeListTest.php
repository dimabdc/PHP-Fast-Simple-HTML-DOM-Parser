<?php

namespace Tests\FastSimpleHTMLDom;


use FastSimpleHTMLDom\Document;
use Tests\TestCase;
use FastSimpleHTMLDom\NodeList;
use FastSimpleHTMLDom\Element;

class NodeListTest extends TestCase
{
    /**
     * @dataProvider findTests
     */
    public function testFind($html, $selector, $count)
    {
        $document = new Document($html);
        $nodeList =$document->find('section');

        $elements = $nodeList->find($selector);

        $this->assertInstanceOf(NodeList::class, $elements);
        $this->assertCount($count, $elements);

        foreach ($elements as $node) {
            $this->assertInstanceOf(Element::class, $node);
        }
    }

    public function findTests()
    {
        $html = $this->loadFixture('testpage.html');
        return array(
            array($html, '.fake h2', 0),
            array($html, 'article', 16),
            array($html, '.radio', 3),
            array($html, 'input.radio', 3),
            array($html, 'ul li', 9),
            array($html, 'fieldset#forms__checkbox li, fieldset#forms__radio li', 6),
            array($html, 'input[id]', 23),
            array($html, 'input[id=in]', 1),
            array($html, '#in', 1),
            array($html, '*[id]', 51),
            array($html, 'text', 200),
        );
    }

    public function testInnerHtml()
    {
        $html = '<div><p>foo</p><p>bar</p></div>';
        $document = new Document($html);
        $element = $document->find('p');

        $this->assertEquals('<p>foo</p><p>bar</p>', $element->innerHtml());
        $this->assertEquals('<p>foo</p><p>bar</p>', $element->innertext);
    }

    public function testText()
    {
        $html = '<div><p>foo</p><p>bar</p></div>';
        $document = new Document($html);
        $element = $document->find('p');

        $this->assertEquals('foobar', $element->text());
        $this->assertEquals('foobar', $element->plaintext);
    }
}
