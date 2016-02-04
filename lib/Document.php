<?php

namespace FastSimpleHTMLDom;


use DOMDocument;
use DOMXPath;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Document
 * @package FastSimpleHTMLDom
 *
 * @property string outertext Get dom node's outer html
 * @property string innertext Get dom node's inner html
 * @property string plaintext Get dom node's plain text
 */
class Document
{
    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * @var Callable
     */
    static protected $callback;


    /**
     * Constructor
     *
     * @param string|Element $element HTML code or Element
     */
    public function __construct($element = null)
    {
        $this->document = new DOMDocument('1.0', 'UTF-8');

        if ($element instanceof Element) {
            $element = $element->getNode();

            $domNode = $this->document->importNode($element, true);
            $this->document->appendChild($domNode);

            return;
        }

        if ($element !== null) {
            $this->loadHtml($element);
        }
    }

    /**
     * Load HTML from string
     *
     * @param string $html
     * @return Document
     * @throws InvalidArgumentException if argument is not string
     */
    public function loadHtml($html)
    {
        if (!is_string($html)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects parameter 1 to be string.');
        }

        libxml_use_internal_errors(true);
        libxml_disable_entity_loader(true);

        $this->document->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED);

        libxml_clear_errors();
        libxml_disable_entity_loader(false);
        libxml_use_internal_errors(false);

        return $this;
    }

    /**
     * @param $html
     * @return Document
     */
    public function load($html)
    {
        return $this->loadHtml($html);
    }

    /**
     * Load HTML from file
     *
     * @param string $filePath
     * @return Document
     */
    public function loadHtmlFile($filePath)
    {
        if (!is_string($filePath)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects parameter 1 to be string.');
        }

        if (!preg_match("/^https:\/\//i", $filePath) || !file_exists($filePath)) {
            throw new RuntimeException("File $filePath not found");
        }

        $html = file_get_contents($filePath);

        if ($html === false) {
            throw new RuntimeException("Could not load file $filePath");
        }

        $this->loadHtml($html);

        return $this;
    }

    public function load_file($filePath)
    {
        return $this->loadHtmlFile($filePath);
    }

    /**
     * @return DOMDocument
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Find list of nodes with a CSS selector
     *
     * @param string $selector
     * @param int $idx
     * @return NodeList|Element|null
     */
    public function find($selector, $idx = null)
    {
        $xPathQuery = SelectorConverter::toXPath($selector);

        $xPath    = new DOMXPath($this->document);
        $nodesList = $xPath->query($xPathQuery);
        $elements = new NodeList();

        foreach ($nodesList as $node) {
            $elements[] = new Element($node);
        }

        $count = count($elements);
        if ($count === 0) return array();

        if (is_null($idx)) {
            return $elements;
        } else if ($idx < 0) {
            $idx = count($elements) + $idx;
        }

        return (isset($elements[$idx])) ? $elements[$idx] : null;
    }

    /**
     * Get dom node's outer html
     *
     * @return string
     */
    public function html()
    {
        if ($this::$callback !== null)
        {
            call_user_func_array($this::$callback, array($this));
        }

        return trim($this->document->saveXML($this->document->documentElement));
        //return trim($this->document->saveHTML($this->document->documentElement));
    }

    /**
     * Get dom node's inner html
     *
     * @return string
     */
    public function innerHtml()
    {
        $text = '';
        foreach ($this->document->documentElement->childNodes as $node) {
            $text .= trim($this->document->saveXML($node));
        }
        return $text;
    }

    /**
     * Get dom node's plain text
     *
     * @return string
     */
    public function text()
    {
        return $this->document->textContent;
    }

    /**
     * Get dom node's outer html
     *
     * @return string
     */
    public function outertext()
    {
        return $this->html();
    }

    /**
     * Get dom node's inner html
     *
     * @return string
     */
    public function innertext()
    {
        return $this->innerHtml();
    }

    /**
     * Save dom as string
     *
     * @param string $filepath
     * @return string
     */
    public function save($filepath = '')
    {
        $string = $this->innerHtml();
        if ($filepath !== '') {
            file_put_contents($filepath, $string, LOCK_EX);
        }
        return $string;
    }

    public function set_callback($functionName)
    {
        $this::$callback = $functionName;
    }

    /**
     * @param $name
     * @return string
     */
    public function __get($name) {
        switch ($name) {
            case 'outertext': return $this->html();
            case 'innertext': return $this->innerHtml();
            case 'plaintext': return $this->text();
        }
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->outertext();
    }

    /**
     * @param string $selector
     * @param int $idx
     * @return Element|NodeList|null
     */
    public function __invoke($selector, $idx = null)
    {
        return $this->find($selector, $idx);
    }
}
