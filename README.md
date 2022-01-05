# HTML Extended Extra for Twig

[![Packagist](https://img.shields.io/packagist/v/gglnx/twig-html-extended-extra.svg)](https://packagist.org/packages/gglnx/twig-html-extended-extra)

This extension extends the [`twig/html-extra`](https://github.com/twigphp/html-extra) package and can be used as drop-in replacement.

## Requirements

* Twig >=2.14 and Twig >=3.0
* PHP >=7.4

## Installation

The recommended way to install this extension is via [Composer](https://getcomposer.org/):

```bash
composer require gglnx/twig-html-extended-extra
```

Afterwards you can add this extension to Twig:

```php
require_once '/path/to/vendor/autoload.php';

$twig = new \Twig\Environment($loader);
$twig->addExtension(new \Gglnx\TwigHtmlExtendedExtra\Extension\HtmlExtendedExtension());
```
