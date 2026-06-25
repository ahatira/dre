<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\ps_theme\Utility\OfferCardPropsBuilder;
use Drupal\ps_theme\Utility\OfferSearchCardPropsBuilder;

/**
 * Offer node preprocess hooks.
 */
final class Offer {

  use StringTranslationTrait;

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_node')]
  public function preprocessNode(array &$variables): void {
    $node = $variables['node'] ?? NULL;
    if (!$node instanceof NodeInterface) {
      return;
    }

    if ($node->bundle() === 'article' && (string) ($variables['view_mode'] ?? '') === 'teaser') {
      $variables['news_teaser_card_component'] = [
        '#type' => 'component',
        '#component' => 'ps_theme:news-teaser-card',
        '#props' => \Drupal::service('ps_news.news_teaser_builder')->build($node),
      ];
      return;
    }

    if ($node->bundle() !== 'offer') {
      return;
    }

    $view_mode = (string) ($variables['view_mode'] ?? '');

    if ($view_mode === 'teaser') {
      $props = array_merge(OfferCardPropsBuilder::build($node), [
        'show_compare' => TRUE,
        'node_id' => (int) $node->id(),
      ]);
      $component = [
        '#type' => 'component',
        '#component' => 'ps_theme:offer-card',
        '#props' => $props,
      ];

      if (\Drupal::hasService('ps_favorite.lazy_builder')) {
        $component['#slots']['actions'] = [
          '#lazy_builder' => [
            'ps_favorite.lazy_builder:buildButton',
            [$node->getEntityTypeId(), (int) $node->id(), 'teaser'],
          ],
          '#create_placeholder' => TRUE,
        ];
      }

      if (\Drupal::hasService('ps_compare.lazy_builder')) {
        $component['#slots']['compare'] = [
          '#lazy_builder' => [
            'ps_compare.lazy_builder:buildButton',
            [$node->getEntityTypeId(), (int) $node->id(), 'teaser'],
          ],
          '#create_placeholder' => TRUE,
        ];
      }

      $this->applyOfferCardCache($component, $node);
      $variables['offer_card_component'] = $component;
      return;
    }

    if ($view_mode === 'search') {
      $component = [
        '#type' => 'component',
        '#component' => 'ps_theme:offer-search-card',
        '#props' => OfferSearchCardPropsBuilder::build($node),
      ];

      if (\Drupal::hasService('ps_favorite.lazy_builder')) {
        $component['#slots']['actions'] = [
          '#lazy_builder' => [
            'ps_favorite.lazy_builder:buildButton',
            [$node->getEntityTypeId(), (int) $node->id(), 'search'],
          ],
          '#create_placeholder' => TRUE,
        ];
      }

      if (\Drupal::hasService('ps_compare.lazy_builder')) {
        $component['#slots']['compare'] = [
          '#lazy_builder' => [
            'ps_compare.lazy_builder:buildButton',
            [$node->getEntityTypeId(), (int) $node->id(), 'search'],
          ],
          '#create_placeholder' => TRUE,
        ];
      }

      $this->applyOfferCardCache($component, $node);
      $variables['offer_search_card_component'] = $component;
      return;
    }

    if ($view_mode === 'full') {
      $variables['attributes']['data-offer-id'] = $node->id();
      $variables['#attached']['library'][] = 'ps_theme/offer-detail';
      $variables['#attached']['library'][] = 'ps_theme/offer-viewed';
      $variables['#attached']['drupalSettings']['psOfferViewed']['currentId'] = (int) $node->id();
      return;
    }
  }

  /**
   * Adds cache tags so card images refresh when the default image config changes.
   *
   * @param array<string, mixed> $component
   *   Offer card component render array.
   */
  private function applyOfferCardCache(array &$component, NodeInterface $node): void {
    if (!\Drupal::hasService('ps_offer.gallery_image_resolver')) {
      return;
    }

    $tags = array_merge(
      $node->getCacheTags(),
      \Drupal::service('ps_offer.gallery_image_resolver')->getDefaultImageCacheTags(),
    );
    $component['#cache']['tags'] = array_values(array_unique(array_merge(
      $component['#cache']['tags'] ?? [],
      $tags,
    )));
  }

  /**
   * Adds layout section modifiers on offer full Layout Builder sections.
   */
  #[Hook('preprocess_layout')]
  public function preprocessLayout(array &$variables): void {
    $route_node = \Drupal::routeMatch()->getParameter('node');
    if (!$route_node instanceof NodeInterface || $route_node->bundle() !== 'offer') {
      return;
    }

    $label = (string) ($variables['settings']['label'] ?? '');
    if ($label === '') {
      return;
    }

    $slug = Html::cleanCssIdentifier(strtolower($label));
    $variables['attributes']['class'][] = 'ps-offer-layout';
    $variables['attributes']['class'][] = 'ps-offer-layout--' . $slug;

    if ($slug === 'main') {
      $variables['attributes']['class'][] = 'container';
      if (isset($variables['region_attributes']['first'])) {
        $variables['region_attributes']['first']->addClass('ps-offer-layout__main-content');
      }
    }

    if ($slug === 'map') {
      $variables['attributes']['class'][] = 'container-fluid';
      $variables['attributes']['class'][] = 'px-0';
    }

    if ($slug === 'similar') {
      $variables['attributes']['class'][] = 'container';
    }
  }

  /**
   * Strips redundant field wrappers on offer detail formatters with own markup.
   */
  #[Hook('preprocess_field')]
  public function preprocessField(array &$variables): void {
    if (($variables['element']['#bundle'] ?? '') !== 'offer') {
      return;
    }

    $field_name = (string) ($variables['field_name'] ?? '');
    $formatter = (string) ($variables['element']['#formatter'] ?? '');

    if ($this->shouldStripAllFieldWrappers($field_name, $formatter)) {
      $variables['display_field_tag'] = FALSE;
      $variables['display_items_wrapper_tag'] = FALSE;
      $variables['display_item_tag'] = FALSE;
      return;
    }

    if ($this->shouldStripFieldItemWrappers($field_name, $formatter)) {
      $variables['display_items_wrapper_tag'] = FALSE;
      $variables['display_item_tag'] = FALSE;
    }
  }

  /**
   * Whether the formatter renders a complete section without field wrappers.
   */
  private function shouldStripAllFieldWrappers(string $field_name, string $formatter): bool {
    return match ($field_name) {
      'field_media_gallery' => $formatter === 'ps_media_gallery_formatter',
      'body' => $formatter === 'ps_offer_description',
      'field_features' => $formatter === 'feature_default',
      'field_divisions' => $formatter === 'ps_surface_division_table',
      'field_primary_agent' => $formatter === 'ps_offer_agent_card',
      default => FALSE,
    };
  }

  /**
   * Whether only field item wrappers can be removed (field shell still needed).
   */
  private function shouldStripFieldItemWrappers(string $field_name, string $formatter): bool {
    return match ($field_name) {
      // field_diagnostics: keep .field--items / .field--item — energy grid CSS
      // in _offer-detail.scss targets those wrappers.
      'field_certification_labels' => $formatter === 'certification_label_badge',
      default => FALSE,
    };
  }

}
