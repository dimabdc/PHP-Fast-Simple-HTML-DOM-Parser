<?php

namespace Tests\FastSimpleHTMLDom;

use BadMethodCallException;
use FastSimpleHTMLDom\Document;
use FastSimpleHTMLDom\Element;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;
use FastSimpleHTMLDom\NodeList;

class DocumentTest extends TestCase
{
    /**
     * @expectException InvalidArgumentException
     */
    public function testConstructWithInvalidArgument()
    {
	      $this->expectException(InvalidArgumentException::class);
	      new Document(['foo']);
    }

    /**
     * @expectException InvalidArgumentException
     */
    public function testLoadHtmlWithInvalidArgument()
    {
	      $this->expectException(InvalidArgumentException::class);
        $document = new Document();
	      $document->loadHtml(['foo']);

    }

    /**
     * @expectException InvalidArgumentException
     */
    public function testLoadWithInvalidArgument()
    {
	      $this->expectException(InvalidArgumentException::class);
        $document = new Document();
	      $document->load(['foo']);
    }

    /**
     * @expectException InvalidArgumentException
     */
    public function testLoadHtmlFileWithInvalidArgument()
    {
	      $this->expectException(InvalidArgumentException::class);
        $document = new Document();
	      $document->loadHtmlFile(['foo']);
    }

    /**
     * @expectException InvalidArgumentException
     */
    public function testLoad_fileWithInvalidArgument()
    {
	      $this->expectException(InvalidArgumentException::class);
        $document = new Document();
	      $document->load_file(['foo']);
    }

    /**
     * @expectException RuntimeException
     */
    public function testLoadHtmlFileWithNotExistingFile()
    {
	      $this->expectException(RuntimeException::class);
        $document = new Document();
	      $document->loadHtmlFile('/path/to/file');
    }

    /**
     * @expectException RuntimeException
     */
    public function testLoadHtmlFileWithNotLoadFile()
    {
	      $this->expectException(RuntimeException::class);
        $document = new Document();
	      $document->loadHtmlFile('http://fobar');
    }

    /**
     * @expectException BadMethodCallException
     */
    public function testMethodNotExist()
    {
	      $this->expectException(BadMethodCallException::class);
        $document = new Document();
				$document->bar();
    }

    /**
     * @expectException BadMethodCallException
     */
    public function testStaticMethodNotExist()
    {
	      $this->expectException(BadMethodCallException::class);
				Document::bar();
    }

    public function testNotExistProperty()
    {
        $document = new Document();

        $this->assertNull($document->foo);
    }

    public function testConstruct()
    {
        $html = '<div>foo</div>';
        $document = new Document($html);

        $this->assertEquals($html, $document->outertext);
    }

    public function testLoadHtmlFile()
    {
        $file = __DIR__ . '/../fixtures/testpage.html';
        $document = new Document();

        $document->loadHtmlFile($file);
        $this->assertNotNull(count($document('div')));

        $document->load_file($file);
        $this->assertNotNull(count($document('div')));

        $document = Document::file_get_html($file);
        $this->assertNotNull(count($document('div')));
    }

    public function testLoadHtml()
    {
        $html = $this->loadFixture('testpage.html');
        $document = new Document();

        $document->loadHtml($html);
        $this->assertNotNull(count($document('div')));

        $document->load($html);
        $this->assertNotNull(count($document('div')));

        $document = Document::str_get_html($html);
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
        $document = new Document($html);
        $elements = $document->find($selector);

        $this->assertInstanceOf(NodeList::class, $elements);
        $this->assertCount($count, $elements);

        foreach ($elements as $element) {
            $this->assertInstanceOf(Element::class, $element);
        }

        if ($count !== 0) {
            $element = $document->find($selector, -1);
            $this->assertInstanceOf(Element::class, $element);
        }
    }

    public function findTests()
    {
        $html = $this->loadFixture('testpage.html');

        return [
            [$html, '.fake h2', 0],
            [$html, 'article', 16],
            [$html, '.radio', 3],
            [$html, 'input.radio', 3],
            [$html, 'ul li', 35],
            [$html, 'fieldset#forms__checkbox li, fieldset#forms__radio li', 6],
            [$html, 'input[id]', 23],
            [$html, 'input[id=in]', 1],
            [$html, '#in', 1],
            [$html, '*[id]', 52],
            [$html, 'text', 234],
            [$html, 'comment', 3],
        ];
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

    public function testClear()
    {
        $document = new Document();

        $this->assertNull($document->clear());
    }
}
