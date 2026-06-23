<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 *
 */
final class CompareLazyBuilder implements TrustedCallbackInterface {

  use StringTranslationTrait;

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly CompareManagerInterface $compareManager,
    private readonly CsrfTokenGenerator $csrfToken,
    private readonly ComparePathResolver $comparePathResolver,
    private readonly CompareDisplaySettings $displaySettings,
  ) {}

  /**
   *
   */
  public static function trustedCallbacks(): array {
    return ['buildButton'];
  }

  /**
   *
   */
  public function buildButton(string $entityTypeId, int $entityId, string $context = 'inline'): array {
    $entity = $this->entityTypeManager->getStorage($entityTypeId)->load($entityId);
    if (!$entity instanceof EntityInterface || !$entity->access('view') || !$this->compareManager->supportsEntity($entity)) {
      return [];
    }

    return $this->buildButtonRenderable($entity, $context);
  }

  /**
   *
   */
  public function buildButtonRenderable(EntityInterface $entity, string $context = 'inline'): array {
    if (!$this->compareManager->supportsEntity($entity)) {
      return [];
    }

    $isCompared = $this->compareManager->isCompared($entity);
    $entityId = (int) $entity->id();

    return [
      '#theme' => 'ps_compare_button',
      '#entity_type_id' => $entity->getEntityTypeId(),
      '#entity_id' => $entityId,
      '#toggle_url' => Url::fromRoute('ps_compare.toggle', [
        'entity_type_id' => $entity->getEntityTypeId(),
        'entity_id' => $entityId,
      ])->toString(),
      '#is_compared' => $isCompared,
      '#context' => $context,
      '#label_add' => t('Compare'),
      '#label_remove' => t('Remove from comparison'),
      '#attached' => $this->buildClientAttachments(),
      '#cache' => [
        'tags' => array_merge($entity->getCacheTags(), [
          'ps_compare:list',
          'ps_compare:count',
          sprintf('ps_compare:%s:%d', $entity->getEntityTypeId(), $entityId),
        ]),
        'contexts' => ['session', 'user'],
        'max-age' => 0,
      ],
    ];
  }

  /**
   * Client-side library and settings for compare toggle buttons.
   *
   * @return array<string, mixed>
   *   Render #attached payload.
   */
  public function buildClientAttachments(): array {
    return [
      'library' => ['ps_compare/compare-toggle'],
      'drupalSettings' => [
        'psCompare' => [
          'csrfToken' => $this->csrfToken->get('ps_compare.toggle'),
          'countEndpoint' => Url::fromRoute('ps_compare.count')->toString(),
          'stateEndpoint' => Url::fromRoute('ps_compare.state')->toString(),
          'compareUrl' => $this->comparePathResolver->getPublicPath(),
          'maxItems' => $this->compareManager->getMaxItems(),
          'minItems' => $this->compareManager->getMinItems(),
          'undoRemoval' => $this->displaySettings->undoRemoval(),
        ],
      ],
    ];
  }

}
