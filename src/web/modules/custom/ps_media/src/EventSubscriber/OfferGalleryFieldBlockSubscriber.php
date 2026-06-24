<?php

declare(strict_types=1);

namespace Drupal\ps_media\EventSubscriber;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Render\Element;
use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Drupal\layout_builder\Plugin\Block\FieldBlock;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Renders the offer gallery hero when the media field is empty.
 *
 * Layout Builder FieldBlock denies access to empty fields before formatters run.
 * The gallery formatter shows the configured default image in hero mode, so
 * the block must still render when field_media_gallery has no items.
 */
final class OfferGalleryFieldBlockSubscriber implements EventSubscriberInterface {

  private const PLUGIN_ID = 'field_block:node:offer:field_media_gallery';

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    // Runs after BlockComponentRenderArray (100) when access was denied.
    return [
      LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY => ['onBuildRender', 50],
    ];
  }

  /**
   * Builds the hero gallery block when core skipped an empty field block.
   */
  public function onBuildRender(SectionComponentBuildRenderArrayEvent $event): void {
    if ($event->inPreview() || $event->getBuild() !== []) {
      return;
    }

    $plugin = $event->getPlugin();
    if (!$plugin instanceof FieldBlock || $plugin->getPluginId() !== self::PLUGIN_ID) {
      return;
    }

    $formatter = $plugin->getConfiguration()['formatter'] ?? [];
    if (($formatter['type'] ?? '') !== 'ps_media_gallery_formatter') {
      return;
    }
    if (($formatter['settings']['display_template'] ?? '') !== 'hero') {
      return;
    }

    $entity = $plugin->getContextValue('entity');
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return;
    }
    if (!$entity->hasField('field_media_gallery') || !$entity->get('field_media_gallery')->isEmpty()) {
      return;
    }

    $fieldView = $entity->get('field_media_gallery')->view($formatter);
    if (Element::isEmpty($fieldView)) {
      return;
    }

    $event->setBuild([
      '#theme' => 'block',
      '#configuration' => $plugin->getConfiguration(),
      '#plugin_id' => $plugin->getPluginId(),
      '#base_plugin_id' => $plugin->getBaseId(),
      '#derivative_plugin_id' => $plugin->getDerivativeId(),
      '#in_preview' => FALSE,
      '#weight' => $event->getComponent()->getWeight(),
      'content' => [$fieldView],
    ]);
    $event->addCacheableDependency($plugin);
    $event->addCacheableDependency(CacheableMetadata::createFromRenderArray($fieldView));
  }

}
