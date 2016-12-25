## The library for work with DJVU files.

## How to install

````bash
$ composer require arhitector/php-djvu dev-master
````

## How to use

```php
use Arhitector\Djvu\Document;

// create instance of Document
$document = new Document(__DIR__.'/tests/file1.djvu');

// get the number of pages
$pages = $document->getPages();

var_dump($pages); // int 675
```
## License

[MIT License](LICENSE "MIT LICENSE").
