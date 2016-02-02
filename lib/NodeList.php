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

    public function outertext()
    {
        $text = '';
        foreach ($this as $node) {
            $text .= $node->outertext;
        }
        return $text;
    }

    function __get($name) {
        switch ($name) {
            case 'outertext': return $this->outertext();
            case 'plaintext': return $this->text();
        }
    }
}