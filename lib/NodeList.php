<?php

namespace FastSimpleHTMLDom;


/**
 * Class NodeList
 * @package FastSimpleHTMLDom
 * @property string outertext Get dom node's outer html
 * @property string plaintext Get dom node's plain text
 */
class NodeList extends \ArrayObject
{
    /**
     * Find list of nodes with a CSS selector
     *
     * @param string $selector
     * @param int $idx
     * @return NodeList|Element|null
     */
    public function find($selector, $idx = null)
    {
        $elements = new NodeList();
        foreach ($this as $node) {
            $elements->append($node->find($selector));
        }
        if (is_null($idx)) {
            return $elements;
        } else if ($idx < 0) {
            $idx = count($elements) + $idx;
        }
        return (isset($elements[$idx])) ? $elements[$idx] : null;
    }

    public function text()
    {
        $text = '';
        foreach ($this as $node) {
            $text .= $node->plaintext;
        }
        return $text;
    }

    public function innertext()
    {
        $text = '';
        foreach ($this as $node) {
            $text .= $node->outertext;
        }
        return $text;
    }

    public function __get($name) {
        switch ($name) {
            case 'innertext': return $this->innertext();
            case 'plaintext': return $this->text();
        }
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->innertext();
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