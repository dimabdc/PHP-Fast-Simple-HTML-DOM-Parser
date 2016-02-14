<?php

namespace FastSimpleHTMLDom;


use BadMethodCallException;
use DOMDocument;
use DOMXPath;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Document
 * @package FastSimpleHTMLDom
 *
 * @property string      outertext Get dom node's outer html
 * @property string      innertext Get dom node's inner html
 * @property-read string plaintext Get dom node's plain text
 *
 * @method string outertext() Get dom node's outer html
 * @method string innertext() Get dom node's inner html
 * @method Document load() load($html) Load HTML from string
 * @method Document load_file() load_file($html) Load HTML from file
 *
 * @method static Document file_get_html() file_get_html($html) Load HTML from file
 * @method static Document str_get_html() str_get_html($html) Load HTML from string
 */
class Document
{
    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * @var array
     */
    protected $functionAliases = [
        'outertext' => 'html',
        'innertext' => 'innerHtml',
        'load'      => 'loadHtml',
        'load_file' => 'loadHtmlFile',
    ];

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
     *
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

        $sxe = simplexml_load_string($html);
        if (libxml_get_errors()) {
            $this->document->loadHTML('<?xml encoding="UTF-8">' . $html);
        } else {
            $this->document = dom_import_simplexml($sxe)->ownerDocument;
        }

        libxml_clear_errors();
        libxml_disable_entity_loader(false);
        libxml_use_internal_errors(false);

        return $this;
    }

    /**
     * Load HTML from file
     *
     * @param string $filePath
     *
     * @return Document
     */
    public function loadHtmlFile($filePath)
    {
        if (!is_string($filePath)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects parameter 1 to be string.');
        }

        if (!preg_match("/^https?:\/\//i", $filePath) && !file_exists($filePath)) {
            throw new RuntimeException("File $filePath not found");
        }

        try {
            $html = file_get_contents($filePath);
        } catch (\Exception $e) {
            throw new RuntimeException("Could not load file $filePath");
        }

        if ($html === false) {
            throw new RuntimeException("Could not load file $filePath");
        }

        $this->loadHtml($html);

        return $this;
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
     * @param int    $idx
     *
     * @return NodeList|Element|null
     */
    public function find($selector, $idx = null)
    {
        $xPathQuery = SelectorConverter::toXPath($selector);

        $xPath = new DOMXPath($this->document);
        $nodesList = $xPath->query($xPathQuery);
        $elements = new NodeList();

        foreach ($nodesList as $node) {
            $elements[] = new Element($node);
        }

        if (is_null($idx)) {
            return $elements;
        } else {
            if ($idx < 0) {
                $idx = count($elements) + $idx;
            }
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
        if ($this::$callback !== null) {
            call_user_func_array($this::$callback, [$this]);
        }

        return trim($this->document->saveHTML($this->document->documentElement));
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
     * Save dom as string
     *
     * @param string $filepath
     *
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

    /**
     * @param $functionName
     */
    public function set_callback($functionName)
    {
        $this::$callback = $functionName;
    }

    public function clear()
    {
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function __get($name)
    {
        switch ($name) {
            case 'outertext':
                return $this->html();
            case 'innertext':
                return $this->innerHtml();
            case 'plaintext':
                return $this->text();
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->html();
    }

    /**
     * @param string $selector
     * @param int    $idx
     *
     * @return Element|NodeList|null
     */
    public function __invoke($selector, $idx = null)
    {
        return $this->find($selector, $idx);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return bool|mixed
     */
    public function __call($name, $arguments)
    {
        if (isset($this->functionAliases[$name])) {
            return call_user_func_array([$this, $this->functionAliases[$name]], $arguments);
        }
        throw new BadMethodCallException('Method does not exist');
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return bool|Document
     */
    public static function __callStatic($name, $arguments)
    {
        if ($name == 'str_get_html') {
            $document = new Document();

            return $document->loadHtml($arguments[0]);
        }

        if ($name == 'file_get_html') {
            $document = new Document();

            return $document->loadHtmlFile($arguments[0]);
        }
        throw new BadMethodCallException('Method does not exist');
    }
}
