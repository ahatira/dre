<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_companion\EventSubscriber;

use Drupal\Component\Serialization\Json;
use Drupal\Core\EventSubscriber\ActiveLinkResponseFilter as CoreActiveLinkResponseFilterAlias;

// cspell:ignore noemptytag
/**
 * Replace core event subscriber to set active class instead of is-active.
 */
class ActiveLinkResponseFilter extends CoreActiveLinkResponseFilterAlias {

  public const string ACTIVE_CLASS = 'active';

  /**
   * {@inheritdoc}
   *
   * @SuppressWarnings("PHPMD.ErrorControlOperator")
   */
  public static function setLinkActiveClass($html_markup, $current_path, $is_front, $url_language, array $query): string {
    $search_key_current_path = 'data-drupal-link-system-path="' . $current_path . '"';
    $search_key_front = 'data-drupal-link-system-path="&lt;front&gt;"';

    // Receive the query in a standardized manner.
    \ksort($query);

    $offset = 0;
    // There are two distinct conditions that can make a link be marked active:
    // 1. A link has the current path in its 'data-drupal-link-system-path'
    //    attribute.
    // 2. We are on the front page and a link has the special '<front>' value in
    //    its 'data-drupal-link-system-path' attribute.
    while (\str_contains(\substr($html_markup, $offset), $search_key_current_path) || ($is_front && \str_contains(\substr($html_markup, $offset), $search_key_front))) {
      $pos_current_path = \strpos($html_markup, $search_key_current_path, $offset);
      // Only look for links with the special '<front>' system path if we are
      // actually on the front page.
      $pos_front = $is_front ? \strpos($html_markup, $search_key_front, $offset) : FALSE;

      // Determine which of the two values is the next match: the exact path, or
      // the <front> special case.
      $pos_match = NULL;
      if ($pos_front === FALSE) {
        $pos_match = $pos_current_path;
      }
      elseif ($pos_current_path === FALSE) {
        $pos_match = $pos_front;
      }
      elseif ($pos_current_path < $pos_front) {
        $pos_match = $pos_current_path;
      }
      else {
        $pos_match = $pos_front;
      }

      // Find beginning and ending of opening tag.
      $pos_tag_start = NULL;
      for ($i = $pos_match; $pos_tag_start === NULL && $i > 0; --$i) {
        if ($html_markup[$i] === '<') {
          $pos_tag_start = $i;
        }
      }
      $pos_tag_end = NULL;
      // @phpstan-ignore-next-line
      for ($i = $pos_match; $pos_tag_end === NULL && $i < \strlen($html_markup); ++$i) {
        if (isset($html_markup[$i]) && $html_markup[$i] === '>') {
          $pos_tag_end = $i;
        }
      }

      // Get the HTML: this will be the opening part of a single tag, e.g.:
      // <a href="/" data-drupal-link-system-path="&lt;front&gt;">.
      $tag = \substr($html_markup, $pos_tag_start ?? 0, $pos_tag_end - $pos_tag_start + 1);

      // Parse it into a DOMDocument so we can reliably read and modify
      // attributes.
      $dom = new \DOMDocument();
      @$dom->loadHTML('<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>' . $tag . '</body></html>');

      /** @var \DOMElement $node */
      // @phpstan-ignore-next-line
      $node = $dom->getElementsByTagName('body')->item(0)->firstChild;

      // Ensure we don't set the "active" class twice on the same element.
      $class = $node->getAttribute('class');
      $add_active = !\in_array(static::ACTIVE_CLASS, \explode(' ', $class), TRUE);

      // The language of an active link is equal to the current language.
      if ($add_active && $url_language) {
        if ($node->hasAttribute('hreflang') && $node->getAttribute('hreflang') !== $url_language) {
          $add_active = FALSE;
        }
      }
      // The query parameters of an active link are equal to the current
      // parameters.
      if ($add_active) {
        if ($query) {
          if (!$node->hasAttribute('data-drupal-link-query') || $node->getAttribute('data-drupal-link-query') !== Json::encode($query)) {
            $add_active = FALSE;
          }
        }
        else {
          if ($node->hasAttribute('data-drupal-link-query')) {
            $add_active = FALSE;
          }
        }
      }

      // Only if the path, the language and the query match, we set the
      // "active" class and add aria-current="page".
      if ($add_active) {
        if ($class !== '') {
          $class .= ' ';
        }
        $class .= static::ACTIVE_CLASS;
        $node->setAttribute('class', $class);
        $node->setAttribute('aria-current', 'page');

        // Get the updated tag.
        $updated_tag = $dom->saveXML($node, \LIBXML_NOEMPTYTAG);
        // saveXML() added a closing tag, remove it.
        // @phpstan-ignore-next-line
        $updated_tag = \substr($updated_tag, 0, \strrpos($updated_tag, '<'));

        $html_markup = \str_replace($tag, $updated_tag, $html_markup);

        // Ensure we only search the remaining HTML.
        $offset = $pos_tag_end - \strlen($tag) + \strlen($updated_tag);
      }
      else {
        // Ensure we only search the remaining HTML.
        $offset = $pos_tag_end + 1;
      }
    }

    return static::setActiveClassOnTrail($html_markup, $query);
  }

  /**
   * Sets the "active" class on relevant links.
   *
   * This is a PHP implementation of the drupal.active-link JavaScript library.
   *
   * @param string $html_markup
   *   The HTML markup to update.
   * @param array $query
   *   The query string for the current URL.
   *
   * @return string
   *   The updated HTML markup.
   *
   * @SuppressWarnings("PHPMD.ErrorControlOperator")
   */
  protected static function setActiveClassOnTrail(string $html_markup, array $query): string {
    $search_key_current_path = 'data-drupal-active-trail="true"';

    // Receive the query in a standardized manner.
    \ksort($query);

    $offset = 0;
    while (\str_contains(\substr($html_markup, $offset), $search_key_current_path)) {
      $pos_match = \strpos($html_markup, $search_key_current_path, $offset);

      // Find beginning and ending of opening tag.
      $pos_tag_start = NULL;
      for ($i = $pos_match; $pos_tag_start === NULL && $i > 0; --$i) {
        if ($html_markup[$i] === '<') {
          $pos_tag_start = $i;
        }
      }
      $pos_tag_end = NULL;
      // @phpstan-ignore-next-line
      for ($i = $pos_match; $pos_tag_end === NULL && $i < \strlen($html_markup); ++$i) {
        if (isset($html_markup[$i]) && $html_markup[$i] === '>') {
          $pos_tag_end = $i;
        }
      }

      // Get the HTML: this will be the opening part of a single tag, e.g.:
      // <a href="/" data-drupal-active-trail="true">.
      $tag = \substr($html_markup, $pos_tag_start ?? 0, $pos_tag_end - $pos_tag_start + 1);

      // Parse it into a DOMDocument so we can reliably read and modify
      // attributes.
      $dom = new \DOMDocument();
      @$dom->loadHTML('<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body>' . $tag . '</body></html>');

      /** @var \DOMElement $node */
      // @phpstan-ignore-next-line
      $node = $dom->getElementsByTagName('body')->item(0)->firstChild;

      // Ensure we don't set the "active" class twice on the same element.
      $class = $node->getAttribute('class');
      $add_active = !\in_array(static::ACTIVE_CLASS, \explode(' ', $class), TRUE);

      if ($add_active) {
        if ($class !== '') {
          $class .= ' ';
        }
        $class .= static::ACTIVE_CLASS;
        $node->setAttribute('class', $class);

        // Get the updated tag.
        $updated_tag = $dom->saveXML($node, \LIBXML_NOEMPTYTAG);
        // saveXML() added a closing tag, remove it.
        // @phpstan-ignore-next-line
        $updated_tag = \substr($updated_tag, 0, \strrpos($updated_tag, '<'));

        $html_markup = \str_replace($tag, $updated_tag, $html_markup);

        // Ensure we only search the remaining HTML.
        $offset = $pos_tag_end - \strlen($tag) + \strlen($updated_tag);
      }
      else {
        // Ensure we only search the remaining HTML.
        $offset = $pos_tag_end + 1;
      }
    }

    return $html_markup;
  }

}
