# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

* Fix: Don’t render attributes if value is strict false

## [0.5.0] - 2024-01-08

* Fix: Don’t use deprecated syntax

## [0.5.0-beta.2] - 2023-06-12

* Move most functions into global namespace

## [0.5.0-beta.1] - 2023-03-16

* Fix `|contextualize` with empty term

## [0.5.0-beta.0] - 2022-09-16

* **Better merging of html attributes**: With this change `attributes` now supports all attributes with space-separated tokes (like class or rel). Multiple values will be merged, values with null will be ignored and values with false remove all previous ones.
* **Breaking Change**: This extension doesn't include anymore all filters and functions from `twig/html-extra`. Please load `HtmlExtension` directly.

## [0.4.0] - 2022-02-07

* New: `wrap_text` for using custom control sequences.
* Fix broken randomness in `html_id`, also using a fixed format (`[prefix]-[five digits]-[five digits]`)

## [0.3.0] - 2022-01-11

* Return Twig\Markup for html modifying filters

## [0.2.4] - 2022-01-11

* Fix double escape in `html_attribute`

## [0.2.3] - 2022-01-11

* Only return HTML attributes with not-null value

## [0.2.2] - 2022-01-10

* Trim combined classes

## [0.2.1] - 2022-01-07

* Always convert value to a string in `html_attribute`

## [0.2.0] - 2022-01-06

* Adds `html_id` function
* Fixes wrong generated html attributes

## [0.1.0] - 2022-01-06

* Adds `html_attributes` function
* Adds `html_attribute` function
* Adds `html_tag` function
* Adds `html_styles` function
* Adds `highlight` filter
* Adds `breakerize` filter
* Adds `contextualize` filter
* Adds `paragraphize` filter
* Adds `strip_control_characters` filter

[Unreleased]: https://github.com/gglnx/twig-html-extended-extra/compare/v0.5.0...HEAD
[0.5.0]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.5.0
[0.5.0-beta.2]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.5.0-beta.2
[0.5.0-beta.1]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.5.0-beta.1
[0.5.0-beta.0]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.5.0-beta.0
[0.4.0]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.4.0
[0.3.0]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.3.0
[0.2.4]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.2.4
[0.2.3]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.2.3
[0.2.2]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.2.2
[0.2.1]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.2.1
[0.2.0]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.2.0
[0.1.0]: https://github.com/gglnx/twig-html-extended-extra/releases/tag/v0.1.0
