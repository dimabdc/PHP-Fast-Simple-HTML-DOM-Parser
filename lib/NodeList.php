<?php

namespace FastSimpleHTMLDom;


/**
 * Class NodeList
 * @package FastSimpleHTMLDom
 * @property-read string outertext Get dom node's outer html
 * @property-read string plaintext Get dom node's plain text
 */
class NodeList extends \ArrayObject
{
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
        $elements = new NodeList();
        foreach ($this as $node) {
            foreach ($node->find($selector) as $res) {
                $elements->append($res);
            }
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
     * Get plain text
     *
     * @return string
     */
    public function text()
    {
        $text = '';
        foreach ($this as $node) {
            $text .= $node->plaintext;
        }

        return $text;
    }

    /**
     * Get html of Elements
     *
     * @return string
     */
    public function innerHtml()
    {
        $text = '';
        foreach ($this as $node) {
            $text .= $node->outertext;
        }

        return $text;
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function __get($name)
    {
        switch ($name) {
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
        return $this->innerHtml();
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
}
