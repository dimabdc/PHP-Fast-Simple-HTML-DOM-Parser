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

        static::assertNull($document->foo);
    }

    public function testConstruct()
    {
        $html = '<div>foo</div>';
        $document = new Document($html);

        static::assertEquals($html, $document->outertext);
    }

    public function testLoadHtmlFile()
    {
        $file = __DIR__ . '/../fixtures/testpage.html';
        $document = new Document();

        $document->loadHtmlFile($file);
        static::assertNotNull(count($document('div')));

        $document->load_file($file);
        static::assertNotNull(count($document('div')));

        $document = Document::file_get_html($file);
        static::assertNotNull(count($document('div')));
    }

    public function testLoadHtml()
    {
        $html = $this->loadFixture('testpage.html');
        $document = new Document();

        $document->loadHtml($html);
        static::assertNotNull(count($document('div')));

        $document->load($html);
        static::assertNotNull(count($document('div')));

        $document = Document::str_get_html($html);
        static::assertNotNull(count($document('div')));
    }

    public function testGetDocument()
    {
        $document = new Document();
        static::assertInstanceOf('DOMDocument', $document->getDocument());
    }

    /**
     * @dataProvider findTests
     */
    static public function testFind($html, $selector, $count)
    {
        $document = new Document($html);
        $elements = $document->find($selector);

        static::assertInstanceOf(NodeList::class, $elements);
        static::assertCount($count, $elements);

        foreach ($elements as $element) {
            static::assertInstanceOf(Element::class, $element);
        }

        if ($count !== 0) {
            $element = $document->find($selector, -1);
            static::assertInstanceOf(Element::class, $element);
        }
    }

	public static function findTests(): array
	{
	    $html = static::loadFixture('testpage.html');

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

        static::assertTrue(is_string($document->html()));
        static::assertTrue(is_string($document->outertext));
        static::assertTrue(strlen($document) > 0);


        $html = '<div>foo</div>';
        $document = new Document($html);

        static::assertEquals($html, $document->html());
        static::assertEquals($html, $document->outertext);
        static::assertEquals($html, $document);
    }

    public function testInnerHtml()
    {
        $html = '<div><div>foo</div></div>';
        $document = new Document($html);

        static::assertEquals('<div>foo</div>', $document->innerHtml());
        static::assertEquals('<div>foo</div>', $document->innertext());
        static::assertEquals('<div>foo</div>', $document->innertext);
    }

    public function testText()
    {
        $html = '<div>foo</div>';
        $document = new Document($html);

        static::assertEquals('foo', $document->text());
        static::assertEquals('foo', $document->plaintext);
    }

    public function testSave()
    {
        $html = $this->loadFixture('testpage.html');
        $document = new Document($html);

        static::assertTrue(is_string($document->save()));
    }

    public function testClear()
    {
        $document = new Document();

        static::assertNull($document->clear());
    }
}
