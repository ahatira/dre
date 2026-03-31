<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Element;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Component\Render\MarkupInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Template\Attribute;

/**
 * Element Prerender methods for contextual_links_placeholder.
 */
class ElementPreRenderContextualLinksPlaceholder implements TrustedCallbackInterface {

  /**
   * Add classes for dropdown styling.
   */
  public static function preRenderContextualLinksPlaceholder(array $element): array {
    if (!isset($element['#markup']) || !($element['#markup'] instanceof MarkupInterface)) {
      return $element;
    }

    $placeholder = (string) $element['#markup'];
    $attributes = static::extractAttributes($placeholder);
    $attributes['class'][] = 'dropdown';
    $attributes['class'][] = 'position-absolute';

    $attribute = new Attribute($attributes);
    $element['#markup'] = new FormattableMarkup('<div@attributes></div>', ['@attributes' => $attribute]);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks(): array {
    return ['preRenderContextualLinksPlaceholder'];
  }

  /**
   * Extract attributes.
   *
   * @param string $html
   *   The HTML to parse. Expected to be one div.
   *
   * @return array
   *   The array of attributes.
   */
  protected static function extractAttributes(string $html): array {
    $attributes = [];
    // Extract existing attributes.
    /** @var \DOMElement $div */
    foreach (Html::load($html)->getElementsByTagName('div') as $div) {
      /** @var \DOMAttr $attr */
      foreach ($div->attributes as $attr) {
        $attributes[$attr->nodeName] = $attr->nodeValue;
      }
    }
    return $attributes;
  }

}
