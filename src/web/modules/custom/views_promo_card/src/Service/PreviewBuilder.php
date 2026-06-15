<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views_promo_card\Entity\PromoCardInterface;

/**
 * Builds mock search context previews for placement admin forms.
 */
final class PreviewBuilder {

  use StringTranslationTrait;

  private const DEFAULT_MOCK_ROWS = 9;

  /**
   * Constructs a PreviewBuilder.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly CardRenderer $cardRenderer,
  ) {}

  /**
   * Builds a mock search grid with a promo card slot at the given position.
   *
   * @param string $card_id
   *   Promo card entity ID.
   * @param int $position
   *   One-based row index after which the card is inserted.
   * @param int $total_rows
   *   Number of mock offer rows to render.
   *
   * @return array<string, mixed>|null
   *   Render array or NULL when the card cannot be rendered.
   */
  public function buildPlacementPreview(string $card_id, int $position, int $total_rows = self::DEFAULT_MOCK_ROWS): ?array {
    if ($card_id === '') {
      return NULL;
    }

    $card = $this->entityTypeManager->getStorage('promo_card')->load($card_id);
    if (!$card instanceof PromoCardInterface) {
      return NULL;
    }

    $card_build = $this->cardRenderer->buildAdminPreview($card);
    if ($card_build === NULL) {
      return NULL;
    }

    $position = max(1, $position);
    $total_rows = max(1, $total_rows);
    $children = [];

    for ($row = 1; $row <= $total_rows; $row++) {
      $children['offer_' . $row] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => (string) $this->t('Offer result @num', ['@num' => $row]),
        '#attributes' => ['class' => ['promo-card-admin__mock-offer']],
      ];
      if ($row === $position) {
        $children['slot_' . $row] = [
          '#type' => 'container',
          '#attributes' => ['class' => ['promo-card-admin__preview-slot']],
          'card' => $card_build,
        ];
      }
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-admin__preview-grid']],
      'grid' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['promo-card-admin__preview-grid-inner']],
      ] + $children,
    ];
  }

}
