<?php

declare(strict_types=1);

namespace Drupal\ps_feature\EventSubscriber;

use Drupal\Core\Render\Element;
use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Drupal\layout_builder\Plugin\Block\FieldBlock;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Suppresses empty Layout Builder field blocks for grouped feature sections.
 *
 * FieldBlock treats cache-only field output as non-empty, which leaves bare
 * wrappers when a grouped feature block has no items for its group filter.
 */
final class EmptyFeatureFieldBlockSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY => ['onBuildRender', 99],
    ];
  }

  /**
   * Clears LB render arrays whose grouped feature field output is empty.
   */
  public function onBuildRender(SectionComponentBuildRenderArrayEvent $event): void {
    if ($event->inPreview()) {
      return;
    }

    $plugin = $event->getPlugin();
    if (!$plugin instanceof FieldBlock || $plugin->getPluginId() !== 'field_block:node:offer:field_features') {
      return;
    }

    $formatter = $plugin->getConfiguration()['formatter'] ?? [];
    if (($formatter['type'] ?? '') !== 'feature_default') {
      return;
    }
    if (($formatter['settings']['format_style'] ?? '') !== 'grouped') {
      return;
    }

    $build = $event->getBuild();
    if ($build === []) {
      return;
    }

    $content = $build['content'] ?? [];
    $field_output = $content[0] ?? $content;
    if (Element::isEmpty($field_output)) {
      $event->setBuild([]);
    }
  }

}
