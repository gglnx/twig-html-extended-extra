# HTML Extended Extra for Twig

[![Packagist](https://img.shields.io/packagist/v/gglnx/twig-html-extended-extra.svg)](https://packagist.org/packages/gglnx/twig-html-extended-extra)

This extension extends the [`twig/html-extra`](https://github.com/twigphp/html-extra) with additional HTML related Twig functions:

* `html_id('my-prefix')`: Returns a random, but for this request unique prefixed ID
* `html_attribute('id', 'html-id')`: Renders a HTML attribute, e.g. `id="html-id"`
* `html_attributes({ id: 'html-id', class: {first: true, second: false}, data: {test: '1'} })`: Renders a map of HTML attributes, result: `id="html-id" class="first" data-test="1"`
* `html_styles({ 'font-size': '12px', 'font-weight': bold })`: Renders CSS properties and values, result: `font-size: 12px, font-weight: bold`

## Requirements

* Twig >=3.20
* PHP >=8.1

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
