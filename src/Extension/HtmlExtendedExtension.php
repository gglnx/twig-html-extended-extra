<?php

/**
 * Twig HTML Extended Extra
 *
 * @copyright 2022 Dennis Morhardt <info@dennismorhardt.de>
 * @license MIT License; see LICENSE file for details.
 */

namespace Gglnx\TwigHtmlExtendedExtra\Extension;

use Symfony\Component\Mime\MimeTypes;
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\Extra\Html\HtmlExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * This extension extends the `HtmlExtension` from `twig/html-extra`
 *
 * @author Dennis Morhardt <info@dennismorhardt.de>
 * @package Gglnx\TwigHtmlExtendedExtra\Extension
 */
class HtmlExtendedExtension extends AbstractExtension
{
    /**
     * Data attributes
     *
     * @var string[]
     */
    public $dataAttributes = ['aria', 'data'];

    /**
     * Attributes which can be empty rendered as empty string
     *
     * @var string[]
     */
    public $emptyAttributes = ['value', 'alt'];

    /**
     * HTML attributes which don't have content.
     *
     * @see https://html.spec.whatwg.org/multipage/syntax.html#void-elements
     * @var string[]
     */
    public $voidHtmlElements = [
        'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input',
        'link', 'meta', 'param', 'source', 'track', 'wbr',
    ];

    /**
     * @var HtmlExtension
     */
    private $htmlExtension;

    /**
     * Inits this extension
     *
     * @param MimeTypes|null $mimeTypes
     * @return void
     */
    public function __construct(MimeTypes $mimeTypes = null)
    {
        $this->htmlExtension = new HtmlExtension($mimeTypes);
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        $baseFilters = $this->htmlExtension->getFilters();

        return array_merge(
            $baseFilters,
            array(
                new TwigFilter(
                    'strip_control_characters',
                    [$this, 'stripControlCharacters']
                ),
                new TwigFilter(
                    'paragraphize',
                    [$this, 'paragraphize'],
                    [
                        'pre_escape' => 'html',
                        'is_safe' => ['html'],
                    ]
                ),
                new TwigFilter(
                    'contextualize',
                    [$this, 'contextualize'],
                    [
                        'pre_escape' => 'html',
                        'is_safe' => ['html'],
                        'needs_environment' => true,
                    ]
                ),
                new TwigFilter(
                    'breakerize',
                    [$this, 'breakerize'],
                    [
                        'pre_escape' => 'html',
                        'is_safe' => ['html'],
                    ]
                ),
                new TwigFilter(
                    'highlight',
                    [$this, 'highlight'],
                    [
                        'pre_escape' => 'html',
                        'is_safe' => ['html'],
                        'needs_environment' => true,
                    ]
                ),
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        $baseFunctions = $this->htmlExtension->getFunctions();

        return array_merge(
            $baseFunctions,
            array(
                new TwigFunction(
                    'html_attributes',
                    [$this, 'htmlAttributes'],
                    [
                        'is_safe' => ['html'],
                        'needs_environment' => true,
                    ]
                ),
                new TwigFunction(
                    'html_attribute',
                    [$this, 'htmlAttribute'],
                    [
                        'is_safe' => ['html'],
                        'needs_environment' => true,
                    ]
                ),
                new TwigFunction(
                    'html_tag',
                    [$this, 'htmlTag'],
                    [
                        'is_safe' => ['html'],
                        'needs_environment' => true,
                    ]
                ),
                new TwigFunction(
                    'html_styles',
                    [$this, 'htmlStyles']
                ),
            )
        );
    }

    /**
     * Converts an array into a style attribute value
     *
     * @param array $properties
     * @return string|null
     */
    public function htmlStyles(array $properties): ?string
    {
        if (array_is_list($properties)) {
            throw new RuntimeError('Array with CSS properties must be an associative array');
        }

        if (count($properties) === 0) {
            return null;
        }

        $style = [];
        foreach ($properties as $property => $value) {
            $style[] = "$property: $value;";
        }

        return implode(' ', $style);
    }

    /**
     * Renders HTML attributes by merging multiple attribute arrays. The values
     * of `class` of two ore more attribute arrays will be merged into one.
     *
     * @param Environment $env
     * @param array[][] $attributes
     * @return string
     */
    public function htmlAttributes(Environment $env, ...$attributes): string
    {
        // Get only arrays
        $attributes = array_filter($attributes, function ($value) {
            return is_array($value) && count($value) > 0;
        });

        // Merge into one attribute array
        $attributes = array_reduce($attributes, function ($carry, $item) {
            // Split class names
            if (isset($item['class'])) {
                if (is_string($item['class'])) {
                    $item['class'] = explode(' ', $item['class']);
                }

                if (is_array($item['class']) && array_is_list($item['class'])) {
                    $item['class'] = array_fill_keys($item['class'], true);
                }
            }

            return $this->mergeArray($carry, $item);
        }, []);

        // Remove empty values
        $attributes = array_filter($attributes, function ($value, $key) {
            if (in_array($key, $this->emptyAttributes)) {
                return $value === '' || !empty($value);
            }

            return !empty($value);
        }, ARRAY_FILTER_USE_BOTH);

        // Render attributes as HTML
        $html = [];
        foreach ($attributes as $name => $value) {
            $html[] = $this->htmlAttribute($env, $name, $value, true);
        }

        return trim(implode(' ', $html));
    }

    /**
     * Renders a HTML attribute
     *
     * @param Environment $env
     * @param string $name
     * @param mixed $value
     * @param bool $isRoot
     * @return string
     */
    public function htmlAttribute(Environment $env, $name, $value, $isRoot = false): string
    {
        // Convert value into a string
        if (is_array($value)) {
            if ($isRoot && in_array($name, $this->dataAttributes)) {
                $attributes = [];

                foreach ($value as $n => $v) {
                    $attributes[] = $this->htmlAttribute($env, "{$name}-{$n}", $v);
                }

                return implode(' ', $attributes);
            } elseif ($name === 'class' && !empty($value)) {
                $value = twig_html_classes($value);
            } elseif ($name === 'style' && !empty($value)) {
                $value = $this->htmlStyles($value);
            } else {
                $value = json_encode(
                    $value,
                    JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
                );
            }
        }

        if (is_bool($value) && $value) {
            return $name;
        } elseif (is_string($value)) {
            $value = twig_escape_filter($env, $value);

            return "{$name}=\"{$value}\"";
        }

        return '';
    }

    /**
     * Renders a HTML tag
     *
     * @param Environment $env Current Twig environment
     * @param string $name Tag name
     * @param string $content Tag content
     * @param array $attributes Tag attributes
     * @return string
     */
    public function htmlTag(Environment $env, string $name, string $content = '', array $attributes = [])
    {
        // Escape name
        $name = strtolower($name);
        $name = twig_escape_filter($env, $name, 'html');

        // Open tag
        $html = "<{$name}";

        // Render attributes
        $attributes = $this->htmlAttributes($env, $attributes);
        if ($attributes !== '') {
            $html .= " {$attributes}";
        }

        // Add content if it's not a void element
        if (!in_array($name, $this->voidHtmlElements)) {
            $content = twig_escape_filter($env, $content, 'html');
            $html .= ">${content}</{$name}>";
        } else {
            $html .= ">";
        }

        return $html;
    }

    /**
     * Converts double new lines into paragraphs
     *
     * @param string $text Input text
     * @param bool $nl2br Convert single new lines into <br>
     * @return string
     */
    public function paragraphize(string $text, bool $nl2br = true): string
    {
        $text = strip_tags($text);
        $text = trim($text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace("~\n\n+~", "\n\n", $text);
        $text = '<p>' . implode('</p><p>', array_filter(explode("\n\n", $text))) . '</p>';
        $text = preg_replace('~<p>\s+</p>~', '', $text);

        if ($nl2br) {
            $text = str_replace("\n", '<br>', $text);
            $text = preg_replace('~\s+<(/?(p|br))>~', '<$1>', $text);
            $text = preg_replace('~<(/?(p|br))>\s+~', '<$1>', $text);
            $text = preg_replace('~(<br>)+~', '<br>', $text);
            $text = str_replace(['<p><br>','<br></p>'], ['<p>', '</p>'], $text);
        } else {
            $text = preg_replace("~\n~", '', $text);
            $text = preg_replace('~\s+<(/?p)>~', '<$1>', $text);
            $text = preg_replace('~<(/?p)>\s+~', '<$1>', $text);
        }

        $text = preg_replace('~\s\s+~', ' ', $text);

        return $text;
    }

    /**
     * Adds breaks to a string by using control characters. A single pipe `|`
     * will be converted into a soft break (`&shy;`) and a double pipe `||` will
     * be converted into a hard break (`<br>`). For preserving pipes escaping
     * will be respected.
     *
     * @param string $text Input text
     * @param bool $stripSlashes Enable striping of slashes.
     * @return string HTML-formatted string
     */
    public function breakerize(string $text, bool $stripSlashes = true): string
    {
        // Convert || into <br>
        $text = preg_replace('/(?<!\\\)\|\|/', '<br>', $text);

        // Convert | into &shy;
        $text = preg_replace('/(?<!\\\)\|/', '&shy;', $text);

        // Remove slashes
        if ($stripSlashes) {
            $text = stripcslashes($text);
        }

        return $text;
    }

    /**
     * Highlights parts of string marked using double-asterisk.
     *
     * @param Environment $env Current Twig environment
     * @param string $text Input text
     * @param bool $stripSlashes Enable striping of slashes.
     * @param string $tag HTML tag for highlighting
     * @param null|string $className Class name for highlight tag
     * @return string HTML-formatted string
     */
    public function highlight(
        Environment $env,
        string $text,
        bool $stripSlashes = true,
        string $tag = 'em',
        ?string $className = null
    ): string {
        // Highlight text
        $replacement = $this->htmlTag($env, $tag, '$2', ['class' => $className]);
        $text = preg_replace('/((?<!\\\)\*\*)(.*?)((?<!\\\)\*\*)/', $replacement, $text);

        // Remove slashes
        if ($stripSlashes) {
            $text = stripcslashes($text);
        }

        return $text;
    }

    /**
     * Strips all format control characters from a string.
     *
     * @param string $text
     * @return string
     */
    public function stripControlCharacters(string $text): string
    {
        // Remove * and | from string
        $text = preg_replace('/((?<!\\\)\*\*)(.*?)((?<!\\\)\*\*)/', '$2', $text);
        $text = preg_replace('/(?<!\\\)\|\|/', ' ', $text);
        $text = preg_replace('/(?<!\\\)\|/', '', $text);

        // Remove slashes
        $text = stripcslashes($text);

        return $text;
    }

    /**
     * Contextualize a term in a string.
     *
     * @param Environment $env Current Twig environment
     * @param string $text Input text
     * @param string $term Term to look for
     * @param int $length Length of the output
     * @param string $tag HTML tag for highlighting
     * @param null|string $className Class name for highlight tag
     * @return string
     */
    public function contextualize(
        Environment $env,
        string $text,
        string $term,
        int $length = 250,
        string $tag = 'em',
        ?string $className = null
    ): string {
        $text = strip_tags($text);
        $pattern = '/(' . preg_quote($term) . ')/im';
        $midway = round($length / 2);

        if (strlen($text) > $length) {
            $strpos = stripos($text, $term);

            if ($strpos > $midway) {
                $text = '...' . mb_substr($text, $strpos - $midway);
            }

            if (strlen($text) > $length) {
                $text = mb_substr($text, 0, $length) . '...';
            }
        }

        $replacement = $this->htmlTag($env, $tag, '$1', ['class' => $className]);
        $text = preg_replace($pattern, $replacement, $text);

        return $text;
    }

    /**
     * Merges two or more arrays into one recursively
     *
     * @param array $arrays
     * @return array
     */
    private function mergeArray(...$arrays)
    {
        $baseArray = array_shift($arrays);

        while (!empty($arrays)) {
            foreach (array_shift($arrays) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $baseArray)) {
                        $baseArray[] = $v;
                    } else {
                        $baseArray[$k] = $v;
                    }
                } elseif (is_array($v) && isset($baseArray[$k]) && is_array($baseArray[$k])) {
                    $baseArray[$k] = $this->mergeArray($baseArray[$k], $v);
                } else {
                    $baseArray[$k] = $v;
                }
            }
        }

        return $baseArray;
    }
}
