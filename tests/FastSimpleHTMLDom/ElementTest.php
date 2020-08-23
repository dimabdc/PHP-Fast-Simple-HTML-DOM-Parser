<?php

namespace Tests\FastSimpleHTMLDom;

use FastSimpleHTMLDom\Document;
use FastSimpleHTMLDom\Element;
use RuntimeException;
use Tests\TestCase;
use FastSimpleHTMLDom\NodeList;

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

    /**
     * @dataProvider replaceNodeDataProvider
     *
     * @param string $replace
     */
    public function testReplaceNode($replace)
    {
        $html = '<div>foo</div>';

        $document = new Document($html);
        $node = $document->getDocument()->documentElement;
        $element = new Element($node);
        $element->outertext = $replace;

        $this->assertEquals($replace, $document->outertext);
        $this->assertEquals($replace, $element->outertext);
    }

    public function replaceNodeDataProvider()
    {
        return [
            [
                '<h1>bar</h1>',
            ],
            [
                '',
            ],
            [
                'foo',
            ],
        ];
    }

    public function testReplaceNodeManyRootNodesException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not valid HTML fragment. String contains more one root node');

        $html = '<div>foo</div>';
        $replace = 'foo<h1>bar</h1>';

        $document = new Document($html);
        $node = $document->getDocument()->documentElement;
        $element = new Element($node);
        $element->outertext = $replace;
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

    public function testReplaceText()
    {
        $html = '<div>foo</div>';
        $replace = '<h1>bar</h1>';

        $document = new Document($html);
        $node = $document->getDocument()->documentElement;
        $element = new Element($node);
        $element->plaintext = $replace;

        $this->assertEquals('&lt;h1&gt;bar&lt;/h1&gt;', $document->outertext);
        $this->assertEquals($replace, $document->plaintext);
        $this->assertEquals('&lt;h1&gt;bar&lt;/h1&gt;', $element->outertext);
        $this->assertEquals($replace, $element->plaintext);

        $element->plaintext = '';

        $this->assertEquals('', $document->outertext);
        $this->assertEquals('', $document->plaintext);
    }

    public function testGetDom()
    {
        $html = '<div><p>foo</p></div>';

        $document = new Document($html);
        $node = $document->getDocument()->documentElement;
        $element = new Element($node);

        $this->assertInstanceOf(Document::class, $element->getDom());
    }

    /**
     * @dataProvider findTestsDataProvider
     */
    public function testFind($html, $selector, $count)
    {
        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $elements = $element->find($selector);

        $this->assertInstanceOf(NodeList::class, $elements);
        $this->assertCount($count, $elements);

        foreach ($elements as $id => $node) {
            $this->assertInstanceOf(Element::class, $node);
        }

        $elements = $element($selector);

        $this->assertInstanceOf(NodeList::class, $elements);
    }

    public function findTestsDataProvider()
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
            array($html, 'text', 234),
            array($html, 'comment', 3),
        );
    }

    public function testGetElementById()
    {
        $html = $this->loadFixture('testpage.html');

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $node = $element->getElementById('in');

        $this->assertInstanceOf(Element::class, $node);
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

        $this->assertInstanceOf(Element::class, $node);
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

        $this->assertInstanceOf(NodeList::class, $elements);
        $this->assertCount(16, $elements);

        foreach ($elements as $node) {
            $this->assertInstanceOf(Element::class, $node);
        }
    }

    public function testChildNodes()
    {
        $html = '<div><p>foo</p><p>bar</p></div>';

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $nodes = $element->childNodes();

        $this->assertInstanceOf(NodeList::class, $nodes);
        $this->assertCount(2, $nodes);

        foreach ($nodes as $node) {
            $this->assertInstanceOf(Element::class, $node);
        }

        $node = $element->childNodes(1);

        $this->assertInstanceOf(Element::class, $node);

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

        $this->assertInstanceOf(NodeList::class, $nodes);
        $this->assertCount(2, $nodes);

        foreach ($nodes as $node) {
            $this->assertInstanceOf(Element::class, $node);
        }

        $node = $element->children(1);

        $this->assertInstanceOf(Element::class, $node);

        $this->assertEquals('<p>bar</p>', $node->outertext);
        $this->assertEquals('bar', $node->plaintext);
    }

    public function testFirstChild()
    {
        $html = '<div><p>foo</p><p></p></div>';

        $document = new Document($html);
        $element = new Element($document->getDocument()->documentElement);

        $node = $element->firstChild();

        $this->assertInstanceOf(Element::class, $node);
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

        $this->assertInstanceOf(Element::class, $node);
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

        $this->assertInstanceOf(Element::class, $sibling);
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

        $this->assertInstanceOf(Element::class, $sibling);
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

        $this->assertInstanceOf(Element::class, $node);
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
        $this->assertInstanceOf(Element::class, $element);
        /** @noinspection MissingIssetImplementationInspection */
        $this->assertTrue(isset($element->id));
    }
}
