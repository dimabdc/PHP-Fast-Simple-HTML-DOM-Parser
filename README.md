# PHP Fast Simple HTML DOM Parser

[![Build Status](https://travis-ci.org/dimabdc/PHP-Fast-Simple-HTML-DOM-Parser.svg?branch=master)](https://travis-ci.org/dimabdc/PHP-Fast-Simple-HTML-DOM-Parser)
[![Total Downloads](https://poser.pugx.org/dimabdc/php-fast-simple-html-dom-parser/downloads)](https://packagist.org/packages/dimabdc/php-fast-simple-html-dom-parser)
[![Latest Stable Version](https://poser.pugx.org/dimabdc/php-fast-simple-html-dom-parser/v/stable)](https://packagist.org/packages/dimabdc/php-fast-simple-html-dom-parser)
[![License](https://poser.pugx.org/dimabdc/php-fast-simple-html-dom-parser/license)](https://packagist.org/packages/dimabdc/php-fast-simple-html-dom-parser)

PHP Fast Simple HTML DOM Parser - fast and low mamory usage HTML DOM Parser with syntax like PHP Simple HTML DOM Parser

## Установка

Для установки выполните команду:

    composer require dimabdc/php-fast-simple-html-dom-parser

## Быстрый старт

```php    
require_once "vendor/autoload.php";
use FastSimpleHTMLDom\Document;

// Create DOM from URL
$html = Document::file_get_html('https://habrahabr.ru/interesting/');

// Find all post blocks
$post = [];
foreach($html->find('div.post') as $post) {
    $item['title']   = $post->find('h1.title', 0)->plaintext;
    $item['hubs']    = $post->find('div.hubs', 0)->plaintext;
    $item['content'] = $post->find('div.content', 0)->plaintext;
    $post[] = $item;
}

print_r($post);
```

## Как создать HTML DOM объект

```php    

// Create a DOM object from a string
$html = new Document('<html><body>Hello!</body></html>');

// Create a DOM object from a string
$html = new Document();
$html->loadHtml('<html><body>Hello!</body></html>');

// Create a DOM object from a HTML file
$html = new Document();
$html->loadHtmlFile('test.htm');

// Create a DOM object from a URL
$html = new Document(file_get_contents('https://habrahabr.ru/interesting/'));
```

## Как искать HTML DOM элементы?

### Основа

```php    

// Find all anchors, returns a array of element objects
$ret = $html->find('a');

// Find (N)th anchor, returns element object or null if not found (zero based)
$ret = $html->find('a', 0);

// Find lastest anchor, returns element object or null if not found (zero based)
$ret = $html->find('a', -1); 

// Find all <div> with the id attribute
$ret = $html->find('div[id]');

// Find all <div> which attribute id=foo
$ret = $html->find('div[id=foo]'); 
```

### Часто используемое

```php    

// Find all element which id=foo
$ret = $html->find('#foo');

// Find all element which class=foo
$ret = $html->find('.foo');

// Find all element has attribute id
$ret = $html->find('*[id]'); 

// Find all anchors and images 
$ret = $html->find('a, img'); 

// Find all anchors and images with the "title" attribute
$ret = $html->find('a[title], img[title]');
```

### Слекторы потомков

```php    

// Find all <li> in <ul> 
$es = $html->find('ul li');

// Find Nested <div> tags
$es = $html->find('div div div'); 

// Find all <td> in <table> which class=hello 
$es = $html->find('table.hello td');

// Find all td tags with attribite align=center in table tags 
$es = $html->find('table td[align=center]');
```

### Вложенные селекторы

```php    

// Find all <li> in <ul> 
foreach($html->find('ul') as $ul) 
{
       foreach($ul->find('li') as $li) 
       {
             // do something...
       }
}

// Find first <li> in first <ul> 
$e = $html->find('ul', 0)->find('li', 0);
```

### Фильтр атрибутов

Filter	|Description
---|---
[attribute]	|Matches elements that have the specified attribute.
[!attribute]	|Matches elements that don't have the specified attribute.
[attribute=value]	|Matches elements that have the specified attribute with a certain value.
[attribute!=value]	|Matches elements that don't have the specified attribute with a certain value.
[attribute^=value]	|Matches elements that have the specified attribute and it starts with a certain value.
[attribute$=value]	|Matches elements that have the specified attribute and it ends with a certain value.
[attribute*=value]	|Matches elements that have the specified attribute and it contains a certain value.

### Текст, комментарии

```php    

// Find all text blocks 
$es = $html->find('text');

// Find all comment (<!--...-->) blocks 
$es = $html->find('comment');
```

## Доступ к атрибутам

### Получение, установка и удаление атрибутов

```php    

// Get a attribute ( If the attribute is non-value attribute (eg. checked, selected...), it will returns true or false)
$value = $e->href;

// Set a attribute(If the attribute is non-value attribute (eg. checked, selected...), set it's value as true or false)
$e->href = 'my link';

// Remove a attribute, set it's value as null! 
$e->href = null;

// Determine whether a attribute exist? 
if(isset($e->href)) 
        echo 'href exist!';
```

### "Магические" атрибуты

```php    

// Example
$html = str_get_html('<div>foo <b>bar</b></div>'); 
$e = $html->find('div', 0);

echo $e->tag; // Returns: "div"
echo $e->outertext; // Returns: "<div>foo <b>bar</b></div>"
echo $e->innertext; // Returns: "foo <b>bar</b>"
echo $e->plaintext; // Returns: "foo bar"
```

Attribute Name	|Usage
---|---
$e->tag	|Read or write the tag name of element.
$e->outertext	|Read or write the outer HTML text of element.
$e->innertext	|Read or write the inner HTML text of element.
$e->plaintext	|Read or write the plain text of element.

### Трюки

```php    

// Extract contents from HTML 
echo $html->plaintext;

// Wrap a element
$e->outertext = '<div class="wrap">' . $e->outertext . '<div>';

// Remove a element, set it's outertext as an empty string 
$e->outertext = '';

// Append a element
$e->outertext = $e->outertext . '<div>foo<div>';

// Insert a element
$e->outertext = '<div>foo<div>' . $e->outertext;
```

## Прогон по DOM-дереву

```php    

// If you are not so familiar with HTML DOM, check this link to learn more... 

// Example
echo $html->find('#div1', 0)->children(1)->children(1)->children(2)->id;
// or 
echo $html->getElementById('div1')->childNodes(1)->childNodes(1)->childNodes(2)->getAttribute('id');
```

Method	|Description
---|---
`mixed` $e->children([int $index])	|Returns the Nth child object if index is set, otherwise return an array of children.
`Element` $e->parent()	|Returns the parent of element.
`Element` $e->first_child()	|Returns the first child of element, or null if not found.
`Element` $e->last_child()	|Returns the last child of element, or null if not found.
`Element` $e->next_sibling()	|Returns the next sibling of element, or null if not found.
`Element` $e->prev_sibling()	|Returns the previous sibling of element, or null if not found.

## API-справочник

### Методы и свойства DOM

Name	|Description
---|---
`void` __construct([string|Element $html])	|Constructor $html is text or Element.
`string` plaintext	|Returns the contents extracted from HTML.
`mixed` find (string $selector [, int $index])	|Find elements by the CSS selector. Returns the Nth element object if index is set, otherwise return an array of object.

### Методы и свойства элементов

Name	|Description
---|---
`string` [attribute]	|Read or write element's attribure value.
`string` tag	|Read or write the tag name of element.
`string` outertext	|Read or write the outer HTML text of element.
`string` innertext	|Read or write the inner HTML text of element.
`string` plaintext	|Read or write the plain text of element.
`mixed` find (string $selector [, int $index])	|Find children by the CSS selector. Returns the Nth element object if index is set, otherwise, return an array of object.

### Прогон по дереву DOM

Name	|Description
---|---
`mixed` $e->children([int $index])	|Returns the Nth child object if index is set, otherwise return an array of children.
`element` $e->parent()	|Returns the parent of element.
`element` $e->first_child()	|Returns the first child of element, or null if not found.
`element` $e->last_child()	|Returns the last child of element, or null if not found.
`element` $e->next_sibling()	|Returns the next sibling of element, or null if not found.
`element` $e->prev_sibling()	|Returns the previous sibling of element, or null if not found.

### camelCase эквиваленты

```php    

string $e->getAttribute($name)
string $e->attribute

void $e->setAttribute($name, $value)
void $value = $e->attribute

bool $e->hasAttribute($name)
bool isset($e->attribute)

void $e->removeAttribute($name)
void $e->attribute = null

element $e->getElementById($id)
mixed $e->find("#$id", 0)

mixed $e->getElementsById($id [,$index])
mixed $e->find("#$id" [, int $index])

element $e->getElementByTagName($name)
mixed $e->find($name, 0)

mixed $e->getElementsByTagName($name [, $index])
mixed $e->find($name [, int $index])

element $e->parentNode()
element $e->parent()

mixed $e->childNodes([$index])
mixed $e->children([int $index])

element $e->firstChild()
element $e->first_child()

element $e->lastChild()
element $e->last_child()

element $e->nextSibling()
element $e->next_sibling()

element $e->previousSibling()
element $e->prev_sibling()
```
