<?php

namespace Tests\FastSimpleHTMLDom;


use FastSimpleHTMLDom\Document;
use FastSimpleHTMLDom\Element;
use Tests\TestCase;

class ElementTest extends TestCase
{
    public function testConstructor()
    {
        $html = '<input name="username" value="John">User name</input>';

        $document = new Document($html);
        $node = $document->getDocument()->documentElement;

        $element = new Element($node);

        $this->assertEquals('input', $element->tag);
        $this->assertEquals('User name', $element->plaintext);
        $this->assertEquals('username', $element->name);
        $this->assertEquals('John', $element->value);
    }

    public function testGetNode()
    {
        $html = '<div>foo</div>';

        $document = new Document($html);
        $node = $document->getDocument()->documentElement;
        $element = new Element($node);

        $this->assertInstanceOf('DOMNode', $element->getNode());
    }

    public function testReplaceNode()
    {
        $html = '<div>foo</div>';
        $replace = '<h1>bar</h1>';

        $document = new Document($html);
        $node = $document->getDocument()->documentElement;
        $element = new Element($node);
        $element->outertext = $replace;

        $this->assertEquals($replace, $document->outertext);
        $this->assertEquals($replace, $element->outertext);

        $element->outertext = '';

        $this->assertNotEquals($replace, $document->outertext);
    }

    public function testReplaceChild()
    {
        $html = '<div><p>foo</p></div>';
        $replace = '<h1>bar</h1>';

        $document = new Document($html);
        $node = $document->getDocument()->documentElement;
        $element = new Element($node);
        $element->innertext = $replace;

        $this->assertEquals('<div><h1>bar</h1></div>', $document->outertext);
        $this->assertEquals('<div><h1>bar</h1></div>', $element->outertext);
    }

    public function testGetDom()
    {
        $html = '<div><p>foo</p></div>';

        $document = new Document($html);
        $node = $document->getDocument()->documentElement;
        $element = new Element($node);

        $this->assertInstanceOf('FastSimpleHTMLDom\Document', $element->getDom());
    }

    /**
     * @dataProvider findTests
     */
    public function testFind($html, $selector, $count)
    {
        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $elements = $element->find($selector);

        $this->assertInstanceOf('FastSimpleHTMLDom\NodeList', $elements);
        $this->assertEquals($count, count($elements));

        foreach ($elements as $node) {
            $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);
        }

        $elements = $element($selector);

        $this->assertInstanceOf('FastSimpleHTMLDom\NodeList', $elements);
    }

    public function findTests()
    {
        $html = $this->loadFixture('testpage.html');
        return array(
            array($html, '.fake h2', 0),
            array($html, 'article', 16),
            array($html, '.radio', 3),
            array($html, 'input.radio', 3),
            array($html, 'ul li', 35),
            array($html, 'fieldset#forms__checkbox li, fieldset#forms__radio li', 6),
            array($html, 'input[id]', 23),
            array($html, 'input[id=in]', 1),
            array($html, '#in', 1),
            array($html, '*[id]', 52),
            array($html, 'text', 462),
            array($html, 'comment', 3),
        );
    }

    public function testGetElementById()
    {
        $html = $this->loadFixture('testpage.html');

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $node = $element->getElementById('in');

        $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);
        $this->assertEquals('input', $node->tag);
        $this->assertEquals('number', $node->type);
        $this->assertEquals('5', $node->value);
    }

    public function testGetElementByTagName()
    {
        $html = $this->loadFixture('testpage.html');

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $node = $element->getElementByTagName('div');

        $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);
        $this->assertEquals('div', $node->tag);
        $this->assertEquals('top', $node->id);
        $this->assertEquals('page', $node->class);
    }

    public function testGetElementsByTagName()
    {
        $html = $this->loadFixture('testpage.html');

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $elements = $element->getElementsByTagName('div');

        $this->assertInstanceOf('FastSimpleHTMLDom\NodeList', $elements);
        $this->assertEquals(16, count($elements));

        foreach ($elements as $node) {
            $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);
        }
    }

    public function testChildNodes()
    {
        $html = '<div><p>foo</p><p>bar</p></div>';

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $nodes = $element->childNodes();

        $this->assertInstanceOf('FastSimpleHTMLDom\NodeList', $nodes);
        $this->assertEquals(2, count($nodes));

        foreach ($nodes as $node) {
            $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);
        }

        $node = $element->childNodes(1);

        $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);

        $this->assertEquals('<p>bar</p>', $node->outertext);
        $this->assertEquals('bar', $node->plaintext);

        $node = $element->childNodes(2);
        $this->assertNull($node);
    }

    public function testChildren()
    {
        $html = '<div><p>foo</p><p>bar</p></div>';

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $nodes = $element->children();

        $this->assertInstanceOf('FastSimpleHTMLDom\NodeList', $nodes);
        $this->assertEquals(2, count($nodes));

        foreach ($nodes as $node) {
            $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);
        }

        $node = $element->children(1);

        $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);

        $this->assertEquals('<p>bar</p>', $node->outertext);
        $this->assertEquals('bar', $node->plaintext);
    }

    public function testFirstChild()
    {
        $html = '<div><p>foo</p><p></p></div>';

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $node = $element->firstChild();

        $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);
        $this->assertEquals('<p>foo</p>', $node->outertext);
        $this->assertEquals('foo', $node->plaintext);

        $node = $element->lastChild();

        $this->assertNull($node->firstChild());
        $this->assertNull($node->first_child());
    }

    public function testLastChild()
    {
        $html = '<div><p></p><p>bar</p></div>';

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $node = $element->lastChild();

        $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);
        $this->assertEquals('<p>bar</p>', $node->outertext);
        $this->assertEquals('bar', $node->plaintext);

        $node = $element->firstChild();

        $this->assertNull($node->lastChild());
        $this->assertNull($node->last_child());
    }

    public function testNextSibling()
    {
        $html = '<div><p>foo</p><p>bar</p></div>';

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $node = $element->firstChild();
        $sibling = $node->nextSibling();

        $this->assertInstanceOf('FastSimpleHTMLDom\Element', $sibling);
        $this->assertEquals('<p>bar</p>', $sibling->outertext);
        $this->assertEquals('bar', $sibling->plaintext);

        $node = $element->lastChild();

        $this->assertNull($node->nextSibling());
        $this->assertNull($node->next_sibling());
    }

    public function testPreviousSibling()
    {
        $html = '<div><p>foo</p><p>bar</p></div>';

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $node = $element->lastChild();
        $sibling = $node->previousSibling();

        $this->assertInstanceOf('FastSimpleHTMLDom\Element', $sibling);
        $this->assertEquals('<p>foo</p>', $sibling->outertext);
        $this->assertEquals('foo', $sibling->plaintext);

        $node = $element->firstChild();

        $this->assertNull($node->previousSibling());
        $this->assertNull($node->prev_sibling());
    }

    public function testParentNode()
    {
        $html = '<div><p>foo</p><p>bar</p></div>';

        $document = new Document($html);
        $element = $document->find('p', 0);

        $node = $element->parentNode();

        $this->assertInstanceOf('FastSimpleHTMLDom\Element', $node);
        $this->assertEquals('div', $node->tag);
        $this->assertEquals('div', $element->parent()->tag);
    }

    public function testHtml()
    {
        $html = '<div>foo</div>';
        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $this->assertEquals($html, $element->html());
        $this->assertEquals($html, $element->outertext());
        $this->assertEquals($html, $element->outertext);
        $this->assertEquals($html, (string)$element);
    }

    public function testInnerHtml()
    {
        $html = '<div><div>foo</div></div>';
        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $this->assertEquals('<div>foo</div>', $element->innerHtml());
        $this->assertEquals('<div>foo</div>', $element->innertext());
        $this->assertEquals('<div>foo</div>', $element->innertext);
    }

    public function testText()
    {
        $html = '<div>foo</div>';
        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $this->assertEquals('foo', $element->text());
        $this->assertEquals('foo', $element->plaintext);
    }

    public function testGetAllAttributes()
    {
        $attr = ['class' => 'post', 'id' => 'p1'];
        $html = '<html><div class="post" id="p1">foo</div><div>bar</div></html>';

        $document = new Document($html);

        $element = $document->find('div', 0);
        $this->assertEquals($attr, $element->getAllAttributes());

        $element = $document->find('div', 1);
        $this->assertNull($element->getAllAttributes());
    }

    public function testGetAttribute()
    {
        $html = '<div class="post" id="p1">foo</div>';

        $document = new Document($html);
        $element = $document->find('div', 0);

        $this->assertEquals('post', $element->getAttribute('class'));
        $this->assertEquals('post', $element->class);
        $this->assertEquals('p1', $element->getAttribute('id'));
        $this->assertEquals('p1', $element->id);
    }

    public function testSetAttribute()
    {
        $html = '<div class="post" id="p1">foo</div>';

        $document = new Document($html);
        $element = $document->find('div', 0);

        $element->setAttribute('id', 'bar');
        $element->data = 'value';
        unset($element->class);

        $this->assertEquals('bar', $element->getAttribute('id'));
        $this->assertEquals('value', $element->getAttribute('data'));
        $this->assertEmpty($element->getAttribute('class'));
    }

    public function testHasAttribute()
    {
        $html = '<div class="post" id="p1">foo</div>';

        $document = new Document($html);
        $element = $document->find('div', 0);

        $this->assertTrue($element->hasAttribute('class'));
        $this->assertTrue(isset($element->id));
    }
}
