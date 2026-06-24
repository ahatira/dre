<?php

declare(strict_types=1);

namespace Drupal\ps_offer\EventSubscriber;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Render\Element;
use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Drupal\layout_builder\Plugin\Block\FieldBlock;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Renders the offer agent card when the primary agent field is empty.
 *
 * Layout Builder FieldBlock denies access to empty fields before formatters
 * run. The ps_offer_agent_card formatter resolves a fallback consultant from
 * config.
 */
final class OfferPrimaryAgentFieldBlockSubscriber implements EventSubscriberInterface {

  private const PLUGIN_ID = 'field_block:node:offer:field_primary_agent';

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY => ['onBuildRender', 50],
    ];
  }

  /**
   * Builds the agent card block when core skipped an empty field block.
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
    if (($formatter['type'] ?? '') !== 'ps_offer_agent_card') {
      return;
    }

    $entity = $plugin->getContextValue('entity');
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return;
    }
    if (!$entity->hasField('field_primary_agent') || !$entity->get('field_primary_agent')->isEmpty()) {
      return;
    }

    $fieldView = $entity->get('field_primary_agent')->view($formatter);
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
