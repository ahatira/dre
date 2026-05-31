<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\media\MediaInterface;
use Drupal\bnp_media\Service\RemoteVideoProviderResolver;

/**
 * Hook implementations for remote video provider handling.
 */
final class RemoteVideoHooks {

  public function __construct(
    private readonly RemoteVideoProviderResolver $providerResolver,
  ) {}

  /**
   * Implements hook_entity_presave().
   */
  #[Hook('entity_presave')]
  public function onEntityPresave(EntityInterface $entity): void {
    if (!$entity instanceof MediaInterface) {
      return;
    }

    if (!$entity->hasField('field_provider') || !$entity->hasField('field_media_oembed_video')) {
      return;
    }

    $source_url = (string) $entity->get('field_media_oembed_video')->value;
    $provider = $this->providerResolver->resolveFromUrl($source_url);

    $entity->set('field_provider', $provider ?? '');
  }

}
