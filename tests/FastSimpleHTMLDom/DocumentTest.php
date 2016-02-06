<?php

namespace Tests\FastSimpleHTMLDom;

use FastSimpleHTMLDom\Document;
use FastSimpleHTMLDom\Element;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructWithInvalidArgument()
    {
        new Document(array('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLoadHtmlWithInvalidArgument()
    {
        $document = new Document();
        $document->loadHtml(array('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLoadWithInvalidArgument()
    {
        $document = new Document();
        $document->load(array('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLoadHtmlFileWithInvalidArgument()
    {
        $document = new Document();
        $document->loadHtmlFile(array('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLoad_fileWithInvalidArgument()
    {
        $document = new Document();
        $document->load_file(array('foo'));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testLoadHtmlFileWithNotExistingFile()
    {
        $document = new Document();
        $document->loadHtmlFile('/path/to/file');
    }

    /**
     * @expectedException RuntimeException
     */
    public function testLoadHtmlFileWithNotLoadFile()
    {
        $document = new Document();
        $document->loadHtmlFile('http://fobar');
    }

    public function testConstruct()
    {
        $html = '<div>foo</div>';
        $document = new Document($html);

        $element = new Element($document->getDocument()->documentElement);

        $this->assertEquals($html, $element->outertext);
    }

    public function testLoadHtmlFile()
    {
        $file = __DIR__ . '/../fixtures/testpage.html';
        $document = new Document();
        $document->loadHtmlFile($file);

        $this->assertNotNull(count($document('div')));
    }

    public function testGetDocument()
    {
        $document = new Document();
        $this->assertInstanceOf('DOMDocument', $document->getDocument());
    }

    /**
     * @dataProvider findTests
     */
    public function testFind($html, $selector, $count)
    {
        $document = new Document($html, false);
        $elements = $document->find($selector);

        $this->assertInstanceOf('FastSimpleHTMLDom\NodeList', $elements);
        $this->assertEquals($count, count($elements));

        foreach ($elements as $element) {
            $this->assertInstanceOf('FastSimpleHTMLDom\Element', $element);
        }

        if ($count !== 0){
            $element = $document->find($selector, -1);
            $this->assertInstanceOf('FastSimpleHTMLDom\Element', $element);
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

    public function testHtml()
    {
        $html = $this->loadFixture('testpage.html');
        $document = new Document($html);

        $this->assertTrue(is_string($document->html()));
        $this->assertTrue(is_string($document->outertext));
        $this->assertTrue(strlen($document) > 0);


        $html = '<div>foo</div>';
        $document = new Document($html);

        $this->assertEquals($html, $document->html());
        $this->assertEquals($html, $document->outertext);
        $this->assertEquals($html, $document);
    }

    public function testInnerHtml()
    {
        $html = '<div><div>foo</div></div>';
        $document = new Document($html);

        $this->assertEquals('<div>foo</div>', $document->innerHtml());
        $this->assertEquals('<div>foo</div>', $document->innertext());
        $this->assertEquals('<div>foo</div>', $document->innertext);
    }

    public function testText()
    {
        $html = '<div>foo</div>';
        $document = new Document($html);

        $this->assertEquals('foo', $document->text());
        $this->assertEquals('foo', $document->plaintext);
    }

    public function testSave()
    {
        $html = $this->loadFixture('testpage.html');
        $document = new Document($html);

        $this->assertTrue(is_string($document->save()));
    }
}