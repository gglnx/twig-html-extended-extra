<?php

/**
 * Twig HTML Extended Extra
 *
 * @copyright 2024 Dennis Morhardt <info@dennismorhardt.de>
 * @license MIT License; see LICENSE file for details.
 */

use Gglnx\TwigHtmlExtendedExtra\Extension\HtmlExtendedExtension;
use Twig\Environment;

/**
 * Returns all HTML attributes which can be boolean
 *
 * @private
 * @return string[]
 */
function twig_html_extended_attributes_boolean(): array
{
    trigger_deprecation('gglnx/twig-html-extended-extra', '0.7', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return HtmlExtendedExtension::getBooleanAttributes();
}

/**
 * Returns all HTML attributes with tokens inside
 *
 * @private
 * @return string[]
 */
function twig_html_extended_attributes_with_space_separated_tokens(): array
{
    trigger_deprecation('gglnx/twig-html-extended-extra', '0.7', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return HtmlExtendedExtension::getSpaceSeparatedTokenAttributes();
}

/**
 * Converts an array into a style attribute value
 *
 * @param array<int|string, mixed> $properties
 * @return string|null
 */
function twig_html_extended_styles(array $properties): ?string
{
    trigger_deprecation('gglnx/twig-html-extended-extra', '0.7', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return HtmlExtendedExtension::styles($properties);
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
function twig_html_extended_attribute(Environment $env, $name, $value, $isRoot = false): string
{
    trigger_deprecation('gglnx/twig-html-extended-extra', '0.7', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return $env->getExtension(HtmlExtendedExtension::class)->attribute($env, $name, $value, $isRoot);
}

/**
 * Merges two or more arrays with attributes recursively into one
 *
 * @param array<int|string, mixed> $arrays
 * @return array<int|string, mixed>
 */
function twig_html_extended_merge_attributes(...$arrays)
{
    trigger_deprecation('gglnx/twig-html-extended-extra', '0.7', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return HtmlExtendedExtension::mergeAttributes(...$arrays);
}

/**
 * Renders HTML attributes by merging multiple attribute arrays. The values
 * of `class` of two ore more attribute arrays will be merged into one.
 *
 * @param Environment $env
 * @param mixed $attributes
 * @return string
 */
function twig_html_extended_attributes(Environment $env, ...$attributes): string
{
    trigger_deprecation('gglnx/twig-html-extended-extra', '0.7', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return $env->getExtension(HtmlExtendedExtension::class)->attributes($env, ...$attributes);
}

/**
 * Renders a HTML tag
 *
 * @param Environment $env Current Twig environment
 * @param string $name Tag name
 * @param string $content Tag content
 * @param array<string, mixed> $attributes Tag attributes
 * @return string
 */
function twig_html_extended_tag(Environment $env, string $name, string $content = '', array $attributes = [])
{
    trigger_deprecation('gglnx/twig-html-extended-extra', '0.7', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return $env->getExtension(HtmlExtendedExtension::class)->tag($env, $name, $content, $attributes);
}
