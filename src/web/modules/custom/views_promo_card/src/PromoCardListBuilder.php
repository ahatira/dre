<?php

declare(strict_types=1);

namespace Drupal\views_promo_card;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\views_promo_card\Entity\PromoCardInterface;
use Drupal\views_promo_card\Service\PatternRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * List builder for promo cards.
 */
class PromoCardListBuilder extends ConfigEntityListBuilder {

  /**
   * Placements indexed by referenced promo card ID.
   *
   * @var array<string, list<\Drupal\views_promo_card\Entity\PromoCardPlacementInterface>>|null
   */
  private ?array $placementsByCard = NULL;

  /**
   * Constructs a PromoCardListBuilder.
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    EntityStorageInterface $storage,
    private readonly PatternRegistry $patternRegistry,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($entity_type, $storage);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): static {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('views_promo_card.pattern_registry'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    return [
      'label' => $this->t('Label'),
      'id' => $this->t('Machine name'),
      'pattern' => $this->t('Pattern'),
      'used_in' => $this->t('Used in'),
      'status' => $this->t('Status'),
      'operations' => $this->t('Operations'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\views_promo_card\Entity\PromoCardInterface $entity */
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['pattern'] = $this->getPatternLabel($entity);
    $row['used_in'] = [
      'data' => ['#markup' => $this->buildUsedInLinks($entity)],
    ];
    $row['status'] = $entity->status() ? $this->t('Enabled') : $this->t('Disabled');
    return $row + parent::buildRow($entity);
  }

  /**
   * Returns a human-readable pattern label for a card.
   */
  private function getPatternLabel(PromoCardInterface $entity): string {
    $pattern_id = $entity->getPatternId();
    if ($pattern_id === '') {
      return (string) $this->t('Not configured');
    }
    return $this->patternRegistry->getPatternLabel($pattern_id);
  }

  /**
   * Builds comma-separated links to placements using this card.
   */
  private function buildUsedInLinks(PromoCardInterface $entity): string {
    $placements = $this->getPlacementsByCard()[$entity->id()] ?? [];
    if ($placements === []) {
      return (string) $this->t('—');
    }

    $links = [];
    foreach ($placements as $placement) {
      $links[] = Link::fromTextAndUrl(
        $placement->label(),
        $placement->toUrl('edit-form'),
      )->toString();
    }
    return implode(', ', $links);
  }

  /**
   * Loads and indexes placements by promo card ID.
   *
   * @return array<string, list<\Drupal\views_promo_card\Entity\PromoCardPlacementInterface>>
   *   Placements grouped by promo card ID.
   */
  private function getPlacementsByCard(): array {
    if ($this->placementsByCard !== NULL) {
      return $this->placementsByCard;
    }

    $this->placementsByCard = [];
    /** @var \Drupal\views_promo_card\Entity\PromoCardPlacementInterface[] $placements */
    $placements = $this->entityTypeManager->getStorage('promo_card_placement')->loadMultiple();
    foreach ($placements as $placement) {
      foreach ($placement->getCards() as $card_ref) {
        $card_id = (string) ($card_ref['promo_card'] ?? '');
        if ($card_id === '') {
          continue;
        }
        $this->placementsByCard[$card_id][] = $placement;
      }
    }

    return $this->placementsByCard;
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();
    if (isset($build['table']['#empty'])) {
      $build['table']['#empty'] = $this->t('No promo cards yet. <a href=":url">Add a promo card</a>.', [
        ':url' => Url::fromRoute('entity.promo_card.add_form')->toString(),
      ]);
    }

    $build['promo_card_actions'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['views-promo-card-actions'],
      ],
      'add' => [
        '#type' => 'link',
        '#title' => $this->t('Add promo card'),
        '#url' => Url::fromRoute('entity.promo_card.add_form'),
        '#attributes' => ['class' => ['button', 'button--primary']],
      ],
    ];

    if (isset($build['table'])) {
      $build['promo_card_actions']['#weight'] = ($build['table']['#weight'] ?? 0) - 10;
    }

    return $build;
  }

}
