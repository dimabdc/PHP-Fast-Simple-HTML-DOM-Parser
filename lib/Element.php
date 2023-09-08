<?php

namespace FastSimpleHTMLDom;

use BadMethodCallException;
use DOMElement;
use DOMNode;
use DOMText;
use RuntimeException;

/**
 * Class Element
 *
 * @package FastSimpleHTMLDom
 * @property string      outertext Get dom node's outer html
 * @property string      innertext Get dom node's inner html
 * @property string      plaintext Get dom node's plain text
 * @property-read string tag       Get dom node name
 * @property-read string attr      Get dom node attributes
 *
 * @method NodeList|Element|null children() children($idx = -1) Returns children of node
 * @method Element|null first_child() Returns the first child of node
 * @method Element|null last_child() Returns the last child of node
 * @method Element|null next_sibling() Returns the next sibling of node
 * @method Element|null prev_sibling() Returns the previous sibling of node
 * @method Element|null parent() Returns the parent of node
 * @method string outertext() Get dom node's outer html
 * @method string innertext() Get dom node's inner html
 */
class Element implements \IteratorAggregate
{
    /**
     * @var DOMElement
     */
    protected mixed $node;

    /**
     * @var array
     */
    protected array $functionAliases = [
        'children'     => 'childNodes',
        'first_child'  => 'firstChild',
        'last_child'   => 'lastChild',
        'next_sibling' => 'nextSibling',
        'prev_sibling' => 'previousSibling',
        'parent'       => 'parentNode',
        'outertext'    => 'html',
        'innertext'    => 'innerHtml',
    ];


    public function __construct(DOMNode $node)
    {
        $this->node = $node;
    }

	/**
	 * @return DOMNode|DOMElement
	 */
    public function getNode(): DOMNode|DOMElement
    {
        return $this->node;
    }

	/**
	 * Replace this node
	 *
	 * @param $string
	 *
	 * @return Element|null
	 *
	 */
    protected function replaceNode($string): null|static
    {
        $importNodeList = NodeList::fromString($string);

        if ($importNodeList->count() > 1) {
            throw new RuntimeException('Not valid HTML fragment. String contains more one root node');
        }

        if ($importNodeList->count() === 0) {
            $this->node->parentNode->removeChild($this->node);
            $this->node = new DOMText();
            return null;
        }

        $newNode = $this->node->ownerDocument->importNode($importNodeList[0]->getNode(), true);

        $this->node->parentNode->replaceChild($newNode, $this->node);
        $this->node = $newNode;

        return $this;
    }

    /**
     * Replace child node
     *
     * @param $string
     *
     * @return $this
     */
    protected function replaceChild($string): static
    {
        $importNodeList = NodeList::fromString($string);

        foreach ($this->node->childNodes as $node) {
            $this->node->removeChild($node);
        }

        foreach ($importNodeList as $importNode) {
            $newNode = $this->node->ownerDocument->importNode($importNode->getNode(), true);
            $this->node->appendChild($newNode);
        }

        $this->node->normalize();

        return $this;
    }

	/**
	 * Replace this node with text
	 *
	 * @param $string
	 *
	 * @return Element|null
	 */
    protected function replaceText($string): null|static
    {
        if (empty($string)) {
            $this->node->parentNode->removeChild($this->node);

            return null;
        }

        $newElement = $this->node->ownerDocument->createTextNode($string);

        $newNode = $this->node->ownerDocument->importNode($newElement, true);

        $this->node->parentNode->replaceChild($newNode, $this->node);
        $this->node = $newNode;

        return $this;
    }

    /**
     * @return Document
     */
    public function getDom(): Document
    {
        return new Document($this);
    }

    /**
     * Find list of nodes with a CSS selector
     *
     * @param string $selector
     * @param int|null $idx
     *
     * @return NodeList|Element|null
     */
    public function find(string $selector, int $idx = null): NodeList|Element|null
    {
        return $this->getDom()->find($selector, $idx);
    }

	/**
	 * Return Element by id
	 *
	 * @param $id
	 *
	 * @return NodeList|Element|null
	 */
    public function getElementById($id): NodeList|Element|null
    {
        return $this->find("#$id", 0);
    }

    /**
     * Returns Elements by id
     *
     * @param      $id
     * @param null $idx
     *
     * @return Element|NodeList|null
     */
    public function getElementsById($id, $idx = null): NodeList|Element|null
    {
        return $this->find("#$id", $idx);
    }

	/**
	 * Return Element by tag name
	 *
	 * @param $name
	 *
	 * @return NodeList|Element|null
	 */
    public function getElementByTagName($name): NodeList|Element|null
    {
        return $this->find($name, 0);
    }

    /**
     * Returns Elements by tag name
     *
     * @param      $name
     * @param null $idx
     *
     * @return Element|NodeList|null
     */
    public function getElementsByTagName($name, $idx = null): NodeList|Element|null
    {
        return $this->find($name, $idx);
    }

    /**
     * Returns children of node
     *
     * @param int $idx
     *
     * @return NodeList|Element|null
     */
    public function childNodes(int $idx = -1): NodeList|Element|null
    {
        $nodeList = $this->getIterator();

        if ($idx === -1) {
            return $nodeList;
        }

        if (isset($nodeList[$idx])) {
            return $nodeList[$idx];
        }

        return null;
    }

    /**
     * Returns the first child of node
     *
     * @return Element|null
     */
    public function firstChild(): ?Element
    {
        $node = $this->node->firstChild;

        if ($node === null) {
            return null;
        }

        return new Element($node);
    }

    /**
     * Returns the last child of node
     *
     * @return Element|null
     */
    public function lastChild(): ?Element
    {
        $node = $this->node->lastChild;

        if ($node === null) {
            return null;
        }

        return new Element($node);
    }

    /**
     * Returns the next sibling of node
     *
     * @return Element|null
     */
    public function nextSibling(): ?Element
    {
        $node = $this->node->nextSibling;

        if ($node === null) {
            return null;
        }

        return new Element($node);
    }

    /**
     * Returns the previous sibling of node
     *
     * @return Element|null
     */
    public function previousSibling(): ?Element
    {
        $node = $this->node->previousSibling;

        if ($node === null) {
            return null;
        }

        return new Element($node);
    }

    /**
     * Returns the parent of node
     *
     * @return Element
     */
    public function parentNode(): Element
    {
        return new Element($this->node->parentNode);
    }

    /**
     * Get dom node's outer html
     *
     * @return string
     */
    public function html(): string
    {
        return $this->getDom()->html();
    }

    /**
     * Get dom node's inner html
     *
     * @return string
     */
    public function innerHtml(): string
    {
        return $this->getDom()->innerHtml();
    }

    /**
     * Get dom node's plain text
     *
     * @return string
     */
    public function text(): string
    {
        return $this->node->textContent;
    }

    /**
     * Returns array of attributes
     *
     * @return array|null
     */
    public function getAllAttributes(): ?array
    {
        if ($this->node->hasAttributes()) {
            $attributes = [];
            foreach ($this->node->attributes as $attr) {
                $attributes[$attr->name] = $attr->value;
            }

            return $attributes;
        }

        return null;
    }

    /**
     * Return attribute value
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getAttribute(string $name): ?string
    {
        return $this->node->getAttribute($name);
    }

    /**
     * Set attribute value
     *
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setAttribute($name, $value): static
    {
        if (empty($value)) {
            $this->node->removeAttribute($name);
        } else {
            $this->node->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * Determine if an attribute exists on the element.
     *
     * @param $name
     *
     * @return bool
     */
    public function hasAttribute($name): bool
    {
        return $this->node->hasAttribute($name);
    }

    /**
     * @param $name
     *
     * @return array|null|string
     */
    public function __get($name)
    {
	    return match ($name) {
		    'outertext' => $this->html(),
		    'innertext' => $this->innerHtml(),
		    'plaintext' => $this->text(),
		    'tag' => $this->node->nodeName,
		    'attr' => $this->getAllAttributes(),
		    default => $this->getAttribute($name),
	    };
    }

    public function __set($name, $value)
    {
	    return match ($name) {
		    'outertext' => $this->replaceNode($value),
		    'innertext' => $this->replaceChild($value),
		    'plaintext' => $this->replaceText($value),
		    default => $this->setAttribute($name, $value),
	    };
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
	    return match ($name) {
		    'outertext', 'innertext', 'plaintext', 'tag' => true,
		    default => $this->hasAttribute($name),
	    };
    }

    public function __unset($name)
    {
        return $this->setAttribute($name, null);
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
     * @param int|null $idx
     *
     * @return Element|NodeList|null
     */
    public function __invoke(string $selector, int $idx = null): NodeList|Element|null
    {
        return $this->find($selector, $idx);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return null|string|Element
     *
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (isset($this->functionAliases[$name])) {
            return call_user_func_array([$this, $this->functionAliases[$name]], $arguments);
        }
        throw new BadMethodCallException('Method does not exist');
    }

    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return NodeList An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): NodeList
    {
        $elements = new NodeList();
        if ($this->node->hasChildNodes()) {
            foreach ($this->node->childNodes as $node) {
                $elements[] = new Element($node);
            }
        }

        return $elements;
    }
}
