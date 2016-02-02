# PHP Fast Simple HTML DOM Parser

[![Total Downloads](https://poser.pugx.org/dimabdc/php-fast-simple-html-dom-parser/downloads)](https://packagist.org/packages/dimabdc/php-fast-simple-html-dom-parser)
[![Latest Stable Version](https://poser.pugx.org/dimabdc/php-fast-simple-html-dom-parser/v/stable)](https://packagist.org/packages/dimabdc/php-fast-simple-html-dom-parser)
[![License](https://poser.pugx.org/dimabdc/php-fast-simple-html-dom-parser/license)](https://packagist.org/packages/dimabdc/php-fast-simple-html-dom-parser)

PHP Fast Simple HTML DOM Parser - fast and low mamory usage HTML DOM Parser with syntax like PHP Simple HTML DOM Parser

## Установка

Для установки DiDOM выполните команду:

    composer require dimabdc/php-fast-simple-html-dom-parser

## Быстрый старт

```php    
require_once "vendor/autoload.php";
use FastSimpleHTMLDom\Document;

// Create DOM from URL
$html = new Document(file_get_contents('https://habrahabr.ru/interesting/'));

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

### Базовое

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

### Продвинутое

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
$es = $html->find(''table td[align=center]');
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


