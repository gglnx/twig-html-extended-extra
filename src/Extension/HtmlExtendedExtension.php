<?php

/**
 * Twig HTML Extended Extra
 *
 * @copyright 2024 Dennis Morhardt <info@dennismorhardt.de>
 * @license MIT License; see LICENSE file for details.
 */

namespace Gglnx\TwigHtmlExtendedExtra\Extension;

use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Extension\AbstractExtension;
use Twig\Extra\Html\HtmlExtension;
use Twig\Markup;
use Twig\Runtime\EscaperRuntime;
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
     * @var string[]
     */
    private static $htmlIds = [];

    /**
     * @var string[]
     * @see https://html.spec.whatwg.org/multipage/indices.html#attributes-3
     */
    private static array $booleanAttributes = [
        'allowfullscreen',
        'async',
        'autofocus',
        'autoplay',
        'checked',
        'controls',
        'default',
        'defer',
        'disabled',
        'formnovalidate',
        'inert',
        'ismap',
        'itemscope',
        'loop',
        'multiple',
        'muted',
        'nomodule',
        'novalidate',
        'open',
        'playsinline',
        'readonly',
        'required',
        'reversed',
        'selected',
        'shadowrootdelegatesfocus',
    ];

    /**
     * @var string[]
     * @see https://html.spec.whatwg.org/multipage/indices.html#attributes-3
     */
    private static array $spaceSeparatedTokenAttributes = [
        'accesskey',
        'accept-charset',
        'autocomplete',
        'blocking',
        'class',
        'for',
        'headers',
        'itemprop',
        'itemref',
        'itemtype',
        'ping',
        'rel',
        'sandbox',
        'sizes',
        'aria-controls',
        'aria-described-by',
        'aria-drop-effect',
        'aria-flow-to',
        'aria-labelled-by',
        'aria-owns',
        'aria-role-description',
        'aria-role-description',
    ];

    /**
     * @var string[]
     * @see https://html.spec.whatwg.org/multipage/syntax.html#void-elements
     */
    private static array $voidElements = [
        'area',
        'base',
        'br',
        'col',
        'embed',
        'hr',
        'img',
        'input',
        'link',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
    ];

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
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
            new TwigFilter(
                'wrap_text',
                [$this, 'wrapText'],
                [
                    'pre_escape' => 'html',
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'html_attributes',
                [$this, 'attributes'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new TwigFunction(
                'html_attribute',
                [$this, 'attribute'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new TwigFunction(
                'html_tag',
                [$this, 'tag'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new TwigFunction(
                'html_styles',
                [$this, 'styles'],
            ),
            new TwigFunction(
                'html_id',
                [$this, 'htmlId']
            ),
        ];
    }

    /**
     * Generates an random, unique HTML ID (prefix-XXXXX-XXXXX).
     *
     * @param string $prefix
     * @return string
     */
    public function htmlId(string $prefix = 'html'): string
    {
        // Generate a random html ID
        do {
            $id = sprintf(
                '%s-%d-%d',
                $prefix,
                mt_rand(10000, 99999),
                mt_rand(10000, 99999)
            );
        } while (in_array($id, self::$htmlIds, true));

        // Cache generated ID
        self::$htmlIds[] = $id;

        return $id;
    }

    /**
     * Converts double new lines into paragraphs
     *
     * @param string $text Input text
     * @param bool $nl2br Convert single new lines into <br>
     * @return Markup
     */
    public function paragraphize(string $text, bool $nl2br = true): Markup
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

        return new Markup($text, 'UTF-8');
    }

    /**
     * Adds breaks to a string by using control characters. A single pipe `|`
     * will be converted into a soft break (`&shy;`) and a double pipe `||` will
     * be converted into a hard break (`<br>`). For preserving pipes escaping
     * will be respected.
     *
     * @param string $text Input text
     * @param bool $stripSlashes Enable striping of slashes.
     * @return Markup HTML-formatted string
     */
    public function breakerize(string $text, bool $stripSlashes = true): Markup
    {
        // Convert || into <br>
        $text = preg_replace('/(?<!\\\)\|\|/', '<br>', $text);

        // Convert | into &shy;
        $text = preg_replace('/(?<!\\\)\|/', '&shy;', $text);

        // Remove slashes
        if ($stripSlashes) {
            $text = stripcslashes($text);
        }

        return new Markup($text, 'UTF-8');
    }

    /**
     * Highlights parts of string marked using double-asterisk.
     *
     * @param Environment $env Current Twig environment
     * @param string $text Input text
     * @param bool $stripSlashes Enable striping of slashes.
     * @param string $tag HTML tag for highlighting
     * @param null|string $className Class name for highlight tag
     * @return Markup HTML-formatted string
     */
    public function highlight(
        Environment $env,
        string $text,
        bool $stripSlashes = true,
        string $tag = 'em',
        ?string $className = null
    ): Markup {
        return $this->wrapText($env, $text, '**', $stripSlashes, $tag, $className);
    }

    /**
     * Wraps text using a control sequence.
     *
     * @param Environment $env Current Twig environment
     * @param string $text Input text
     * @param string $controlSequence Control sequence
     * @param bool $stripSlashes Enable striping of slashes.
     * @param string $tag HTML tag for highlighting
     * @param null|string $className Class name for highlight tag
     * @return Markup HTML-formatted string
     */
    public function wrapText(
        Environment $env,
        string $text,
        string $controlSequence,
        bool $stripSlashes = true,
        string $tag = 'em',
        ?string $className = null
    ): Markup {
        // Wraps text
        $controlSequence = preg_quote($controlSequence, '/');
        $regex = "/((?<!\\\){$controlSequence})(.*?)((?<!\\\){$controlSequence})/";
        $replacement = $this->tag($env, $tag, '$2', ['class' => $className]);
        $text = preg_replace($regex, $replacement, $text);

        // Remove slashes
        if ($stripSlashes) {
            $text = stripcslashes($text);
        }

        return new Markup($text, 'UTF-8');
    }

    /**
     * Strips all format control characters from a string.
     *
     * @param string $text
     * @param string[] $controlSequences
     * @return string
     */
    public function stripControlCharacters(
        string $text,
        array $controlSequences = ['**']
    ): string {
        // Remove wrap text markers
        foreach ($controlSequences as $controlSequence) {
            $controlSequence = preg_quote($controlSequence, '/');
            $regex = "/((?<!\\\){$controlSequence})(.*?)((?<!\\\){$controlSequence})/";
            $text = preg_replace($regex, '$2', $text);
        }

        // Remove soft and hard breaks
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
     * @return Markup
     */
    public function contextualize(
        Environment $env,
        string $text,
        string $term,
        int $length = 250,
        string $tag = 'em',
        ?string $className = null
    ): Markup {
        $text = strip_tags($text);
        $pattern = '/(' . preg_quote($term, '/') . ')/im';
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

        if (!empty($term)) {
            $replacement = $this->tag($env, $tag, '$1', ['class' => $className]);
            $text = preg_replace($pattern, $replacement, $text);
        }

        return new Markup($text, 'UTF-8');
    }

    /**
     * Returns all HTML attributes which can be boolean
     *
     * @return string[]
     */
    public static function getBooleanAttributes(): array
    {
        return self::$booleanAttributes;
    }

    /**
     * Returns all HTML attributes with tokens inside
     *
     * @return string[]
     */
    public static function getSpaceSeparatedTokenAttributes(): array
    {
        return self::$spaceSeparatedTokenAttributes;
    }

    /**
     * Converts an array into a style attribute value
     *
     * @param array $properties
     * @return string|null
     */
    public static function styles(array $properties): ?string
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
     * Renders a HTML attribute
     *
     * @param Environment $env
     * @param string $name
     * @param mixed $value
     * @param bool $isRoot
     * @return string
     */
    public function attribute(Environment $env, $name, $value, $isRoot = false): string
    {
        // Convert value into a string
        if (is_array($value)) {
            if ($isRoot && in_array($name, ['data', 'aria'])) {
                $attributes = [];

                foreach ($value as $n => $v) {
                    $attribute = $this->attribute($env, "{$name}-{$n}", $v);

                    if (!empty($attribute)) {
                        $attributes[] = $attribute;
                    }
                }

                return implode(' ', $attributes);
            } elseif (in_array($name, self::$spaceSeparatedTokenAttributes) && !empty($value)) {
                $value = trim(HtmlExtension::htmlClasses($value));
            } elseif ($name === 'style' && !empty($value)) {
                $value = self::styles($value);
            } else {
                $value = json_encode(
                    $value,
                    JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS
                );
            }
        }

        if (in_array($name, self::$booleanAttributes)) {
            return $value ? $name : '';
        }

        if (is_bool($value)) {
            return $value ? $name : '';
        }

        if ($value !== null) {
            $value = $env->getRuntime(EscaperRuntime::class)->escape((string) $value);

            return "{$name}=\"{$value}\"";
        }

        return '';
    }

    /**
     * Merges two or more arrays with attributes recursively into one
     *
     * @param array $arrays
     * @return array
     */
    public static function mergeAttributes(...$arrays): array
    {
        $attributes = [];

        while (!empty($arrays)) {
            foreach (array_shift($arrays) as $k => $v) {
                if (in_array($k, self::$spaceSeparatedTokenAttributes)) {
                    if (is_string($v)) {
                        $v = array_values(array_filter(explode(' ', $v)));
                    }

                    if (is_array($v) && array_is_list($v)) {
                        $v = array_fill_keys($v, true);
                    }
                }

                if ($v === false) {
                    if (array_key_exists($k, $attributes)) {
                        unset($attributes[$k]);
                    }
                } elseif (is_int($k)) {
                    if (array_key_exists($k, $attributes)) {
                        $attributes[] = $v;
                    } else {
                        $attributes[$k] = $v;
                    }
                } elseif (is_array($v) && isset($attributes[$k]) && is_array($attributes[$k])) {
                    $attributes[$k] = self::mergeAttributes($attributes[$k], $v);
                } elseif (in_array($k, ['value', 'alt']) && is_string($v)) {
                    $attributes[$k] = $v;
                } elseif (!empty($v)) {
                    $attributes[$k] = $v;
                }
            }
        }

        return $attributes;
    }

    /**
     * Renders HTML attributes by merging multiple attribute arrays. The values
     * of `class` of two ore more attribute arrays will be merged into one.
     *
     * @param Environment $env
     * @param array[][] $attributes
     * @return string
     */
    public function attributes(Environment $env, ...$attributes): string
    {
        // Get only arrays
        $attributes = array_filter($attributes, function ($value) {
            return is_iterable($value) && (!is_countable($value) || count($value) > 0);
        });

        // Merge into all attribute arrays into one
        $attributes = self::mergeAttributes(...$attributes);

        // Render attributes as HTML
        $html = [];
        foreach ($attributes as $name => $value) {
            $html[] = $this->attribute($env, $name, $value, true);
        }

        return trim(implode(' ', $html));
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
    public function tag(Environment $env, string $name, string $content = '', array $attributes = []): string
    {
        // Escape name
        $name = strtolower($name);
        $name = $env->getRuntime(EscaperRuntime::class)->escape($name, 'html');

        // Open tag
        $html = "<{$name}";

        // Render attributes
        $attributes = $this->attributes($env, $attributes);
        if ($attributes !== '') {
            $html .= " {$attributes}";
        }

        if (!in_array($name, self::$voidElements)) {
            $content = $env->getRuntime(EscaperRuntime::class)->escape($content, 'html');
            $html .= ">{$content}</{$name}>";
        } else {
            $html .= ">";
        }

        return $html;
    }
}
