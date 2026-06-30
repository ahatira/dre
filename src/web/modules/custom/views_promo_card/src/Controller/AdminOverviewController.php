<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\views_promo_card\Entity\PromoCardInterface;
use Drupal\views_promo_card\Entity\PromoCardPlacementInterface;
use Drupal\views_promo_card\Service\PatternRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Admin hub overview for Views Promo Card.
 */
final class AdminOverviewController extends ControllerBase {

  /**
   * Constructs an AdminOverviewController.
   */
  public function __construct(
    private readonly PatternRegistry $patternRegistry,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('views_promo_card.pattern_registry'),
    );
  }

  /**
   * Redirects the hub path to the overview tab.
   */
  public function redirectToOverview(): RedirectResponse {
    return $this->redirect('views_promo_card.admin');
  }

  /**
   * Redirects the former config overview URL.
   */
  public function redirectLegacyConfigOverview(): RedirectResponse {
    return $this->redirect('views_promo_card.admin', [], [], 301);
  }

  /**
   * Redirects the former config settings URL.
   */
  public function redirectLegacyConfigSettings(): RedirectResponse {
    return $this->redirect('views_promo_card.settings', [], [], 301);
  }

  /**
   * Redirects the former structure placements list URL.
   */
  public function redirectLegacyPlacements(): RedirectResponse {
    return $this->redirect('entity.promo_card_placement.collection', [], [], 301);
  }

  /**
   * Redirects the former structure placement add URL.
   */
  public function redirectLegacyPlacementsAdd(): RedirectResponse {
    return $this->redirect('entity.promo_card_placement.add_form', [], [], 301);
  }

  /**
   * Redirects the former structure placement edit URL.
   */
  public function redirectLegacyPlacementsEdit(string $promo_card_placement): RedirectResponse {
    return $this->redirect('entity.promo_card_placement.edit_form', [
      'promo_card_placement' => $promo_card_placement,
    ], [], 301);
  }

  /**
   * Redirects the former structure placement delete URL.
   */
  public function redirectLegacyPlacementsDelete(string $promo_card_placement): RedirectResponse {
    return $this->redirect('entity.promo_card_placement.delete_form', [
      'promo_card_placement' => $promo_card_placement,
    ], [], 301);
  }

  /**
   * Renders the module admin overview dashboard.
   */
  public function overview(): array {
    $entity_type_manager = $this->entityTypeManager();
    /** @var \Drupal\views_promo_card\Entity\PromoCardInterface[] $cards */
    $cards = $entity_type_manager->getStorage('promo_card')->loadMultiple();
    /** @var \Drupal\views_promo_card\Entity\PromoCardPlacementInterface[] $placements */
    $placements = $entity_type_manager->getStorage('promo_card_placement')->loadMultiple();

    $cards_enabled = count(array_filter($cards, static fn(PromoCardInterface $card): bool => $card->status()));
    $placements_enabled = count(array_filter($placements, static fn(PromoCardPlacementInterface $placement): bool => $placement->status()));

    $card_ids_in_placements = $this->collectReferencedCardIds($placements);
    $orphan_cards = array_filter(
      $cards,
      static fn(PromoCardInterface $card): bool => !isset($card_ids_in_placements[$card->id()]),
    );

    $allowed_patterns = $this->patternRegistry->getAllowedPatternIds();

    $build = [
      '#attached' => [
        'library' => ['views_promo_card/admin_overview'],
      ],
      '#cache' => [
        'tags' => ['config:promo_card_list', 'config:promo_card_placement_list', 'config:views_promo_card.settings'],
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['views-promo-card-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Promo cards are reusable SDC content blocks injected into Views search result lists (for example offer grids). Create cards once, attach them to a view display via placements, then control row position, rotation and visibility conditions.'),
      ],
    ];

    $build['stats'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['views-promo-card-overview__stats']],
      'cards' => $this->buildStatCard(
        (string) $this->t('Cards'),
        (string) count($cards),
        $this->t('@enabled enabled', ['@enabled' => $cards_enabled]),
        'entity.promo_card.collection',
      ),
      'placements' => $this->buildStatCard(
        (string) $this->t('Placements'),
        (string) count($placements),
        $this->t('@enabled active', ['@enabled' => $placements_enabled]),
        'entity.promo_card_placement.collection',
      ),
      'patterns' => $this->buildStatCard(
        (string) $this->t('Allowed patterns'),
        (string) count($allowed_patterns),
        $this->t('SDC components'),
        'views_promo_card.settings',
      ),
    ];

    $build['workflow'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['views-promo-card-overview__panel']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('How it works'),
      ],
      'steps' => [
        '#theme' => 'item_list',
        '#title' => NULL,
        '#items' => [
          $this->t('<strong>1. Create a promo card</strong> — choose an allowed SDC pattern (content card, search push card…), edit copy, buttons and appearance with live preview.'),
          $this->t('<strong>2. Add a placement</strong> — bind one or more cards to a View display, set row position or interval, rotation mode, and optional visibility conditions.'),
          $this->t('<strong>3. Verify on front</strong> — open the target search page and confirm the card appears between offer results.'),
        ],
      ],
      'tabs' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['views-promo-card-overview__quick-links']],
        'cards' => Link::createFromRoute($this->t('Manage cards'), 'entity.promo_card.collection')->toRenderable() + [
          '#attributes' => ['class' => ['button']],
        ],
        'placements' => Link::createFromRoute($this->t('Manage placements'), 'entity.promo_card_placement.collection')->toRenderable() + [
          '#attributes' => ['class' => ['button']],
        ],
        'settings' => Link::createFromRoute($this->t('Module settings'), 'views_promo_card.settings')->toRenderable() + [
          '#attributes' => ['class' => ['button', 'button--small']],
        ],
      ],
    ];

    $warnings = $this->buildWarnings($cards, $placements, $orphan_cards, $cards_enabled, $placements_enabled);
    if ($warnings !== []) {
      $build['warnings'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['views-promo-card-overview__warnings']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Attention needed'),
        ],
        'list' => [
          '#theme' => 'item_list',
          '#items' => $warnings,
        ],
      ];
    }

    $build['cards'] = $this->buildCardsPanel($cards);
    $build['placements'] = $this->buildPlacementsPanel($placements);

    if ($orphan_cards !== []) {
      $build['orphans'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['views-promo-card-overview__panel']],
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'h2',
          '#value' => $this->t('Cards not used in any placement'),
        ],
        'list' => [
          '#theme' => 'item_list',
          '#items' => array_map(
            static fn(PromoCardInterface $card): array => [
              '#type' => 'link',
              '#title' => $card->label(),
              '#url' => $card->toUrl('edit-form'),
            ],
            array_values($orphan_cards),
          ),
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['views-promo-card-overview__footer']],
      'hub' => Link::createFromRoute(
        $this->t('Back to PS content hub'),
        'ps_core.content',
      )->toRenderable(),
    ];

    return $build;
  }

  /**
   * Builds a summary stat card render element.
   */
  private function buildStatCard(string $label, string $value, \Stringable|string $meta, string $route): array {
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['views-promo-card-overview__stat']],
      'value' => [
        '#type' => 'link',
        '#title' => $value,
        '#url' => Url::fromRoute($route),
        '#attributes' => ['class' => ['views-promo-card-overview__stat-value']],
      ],
      'label' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $label,
        '#attributes' => ['class' => ['views-promo-card-overview__stat-label']],
      ],
      'meta' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $meta,
        '#attributes' => ['class' => ['views-promo-card-overview__stat-meta']],
      ],
    ];
  }

  /**
   * Collects promo card IDs referenced by placements.
   *
   * @param \Drupal\views_promo_card\Entity\PromoCardPlacementInterface[] $placements
   *   Loaded placements.
   *
   * @return array<string, true>
   *   Referenced card IDs.
   */
  private function collectReferencedCardIds(array $placements): array {
    $ids = [];
    foreach ($placements as $placement) {
      foreach ($placement->getCards() as $card_ref) {
        $card_id = (string) ($card_ref['promo_card'] ?? '');
        if ($card_id !== '') {
          $ids[$card_id] = TRUE;
        }
      }
    }
    return $ids;
  }

  /**
   * Builds warning messages for the overview dashboard.
   *
   * @param \Drupal\views_promo_card\Entity\PromoCardInterface[] $cards
   *   All promo cards.
   * @param \Drupal\views_promo_card\Entity\PromoCardPlacementInterface[] $placements
   *   All placements.
   * @param \Drupal\views_promo_card\Entity\PromoCardInterface[] $orphan_cards
   *   Cards without placement.
   * @param int $cards_enabled
   *   Number of enabled promo cards.
   * @param int $placements_enabled
   *   Number of enabled placements.
   */
  private function buildWarnings(
    array $cards,
    array $placements,
    array $orphan_cards,
    int $cards_enabled,
    int $placements_enabled,
  ): array {
    $warnings = [];

    if ($cards === []) {
      $warnings[] = $this->t('No promo cards yet. @link to get started.', [
        '@link' => Link::fromTextAndUrl($this->t('Add your first card'), Url::fromRoute('entity.promo_card.add_form'))->toString(),
      ]);
    }
    elseif ($placements === []) {
      $warnings[] = $this->t('Cards exist but no placement is configured — nothing will appear on the site. @link', [
        '@link' => Link::fromTextAndUrl($this->t('Add a placement'), Url::fromRoute('entity.promo_card_placement.add_form'))->toString(),
      ]);
    }
    elseif ($placements_enabled === 0) {
      $warnings[] = $this->t('All placements are disabled. Enable at least one placement for cards to show on search results.');
    }
    elseif ($cards_enabled === 0) {
      $warnings[] = $this->t('All promo cards are disabled. Enable cards or update placement references.');
    }

    if ($orphan_cards !== [] && $placements !== []) {
      $warnings[] = $this->t('@count card(s) are not attached to any placement.', [
        '@count' => count($orphan_cards),
      ]);
    }

    if ($this->patternRegistry->getAllowedPatternIds() === []) {
      $warnings[] = $this->t('No SDC patterns are allowed. Configure @settings before creating cards.', [
        '@settings' => Link::fromTextAndUrl($this->t('module settings'), Url::fromRoute('views_promo_card.settings'))->toString(),
      ]);
    }

    return $warnings;
  }

  /**
   * Builds the promo cards summary panel.
   *
   * @param \Drupal\views_promo_card\Entity\PromoCardInterface[] $cards
   *   Loaded promo cards.
   */
  private function buildCardsPanel(array $cards): array {
    $header = [
      $this->t('Card'),
      $this->t('Pattern'),
      $this->t('Status'),
    ];

    $rows = [];
    foreach ($cards as $card) {
      $rows[] = [
        Link::fromTextAndUrl($card->label(), $card->toUrl('edit-form'))->toString(),
        $this->patternRegistry->getPatternLabel($card->getPatternId()) ?: $this->t('Not configured'),
        $card->status() ? $this->t('Enabled') : $this->t('Disabled'),
      ];
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['views-promo-card-overview__panel']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Cards overview'),
      ],
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No promo cards yet.'),
      ],
      'footer' => [
        '#type' => 'link',
        '#title' => $this->t('Manage all cards'),
        '#url' => Url::fromRoute('entity.promo_card.collection'),
        '#attributes' => ['class' => ['views-promo-card-overview__panel-link']],
      ],
    ];
  }

  /**
   * Builds the active placements summary panel.
   *
   * @param \Drupal\views_promo_card\Entity\PromoCardPlacementInterface[] $placements
   *   Loaded placements.
   */
  private function buildPlacementsPanel(array $placements): array {
    $entity_type_manager = $this->entityTypeManager();
    $header = [
      $this->t('Placement'),
      $this->t('View display'),
      $this->t('Status'),
      $this->t('Cards'),
    ];

    $rows = [];
    foreach ($placements as $placement) {
      $card_labels = [];
      foreach ($placement->getCards() as $card_ref) {
        $card_id = (string) ($card_ref['promo_card'] ?? '');
        if ($card_id === '') {
          continue;
        }
        $card = $entity_type_manager->getStorage('promo_card')->load($card_id);
        if ($card instanceof PromoCardInterface) {
          $card_labels[] = Link::fromTextAndUrl($card->label(), $card->toUrl('edit-form'))->toString();
        }
      }

      $rows[] = [
        Link::fromTextAndUrl($placement->label(), $placement->toUrl('edit-form'))->toString(),
        $placement->getViewId() . ' / ' . $placement->getDisplayId(),
        $placement->status() ? $this->t('Enabled') : $this->t('Disabled'),
        $card_labels !== [] ? ['data' => ['#markup' => implode(', ', $card_labels)]] : $this->t('—'),
      ];
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['views-promo-card-overview__panel']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Placements overview'),
      ],
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No placements configured yet.'),
      ],
      'footer' => [
        '#type' => 'link',
        '#title' => $this->t('Manage all placements'),
        '#url' => Url::fromRoute('entity.promo_card_placement.collection'),
        '#attributes' => ['class' => ['views-promo-card-overview__panel-link']],
      ],
    ];
  }

}
