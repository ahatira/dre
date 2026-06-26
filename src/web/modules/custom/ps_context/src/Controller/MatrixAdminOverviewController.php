<?php

declare(strict_types=1);

namespace Drupal\ps_context\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ps_context\Entity\PsContextRuleInterface;
use Drupal\ps_context\Service\ContextLabelResolver;
use Drupal\ps_context\Service\SearchFilterVisibilityResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Admin overview dashboard for Context (rules + labels).
 */
final class MatrixAdminOverviewController extends ControllerBase {

  /**
   * @var list<string>
   */
  private const ASSET_CODES = ['BUR', 'COW', 'ENT', 'ACT', 'COM', 'TER'];

  /**
   * Search operation columns: flexible (no op) then rent and sale.
   *
   * @var list<array{key: string, operation: ?string}>
   */
  private const OPERATION_COLUMNS = [
    ['key' => 'flexible', 'operation' => NULL],
    ['key' => 'loc', 'operation' => 'LOC'],
    ['key' => 'ven', 'operation' => 'VEN'],
  ];

  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    SearchFilterVisibilityResolver $searchFilterVisibility,
    ContextLabelResolver $contextLabelResolver,
    ModuleHandlerInterface $moduleHandler,
    LanguageManagerInterface $languageManager,
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->searchFilterVisibility = $searchFilterVisibility;
    $this->contextLabelResolver = $contextLabelResolver;
    $this->moduleHandler = $moduleHandler;
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('ps_context.search_filter_visibility'),
      $container->get('ps_context.context_label_resolver'),
      $container->get('module_handler'),
      $container->get('language_manager'),
    );
  }

  /**
   * Redirects the context hub path to the overview tab.
   */
  public function redirectToOverview(): RedirectResponse {
    return $this->redirect('ps_context.context_overview');
  }

  /**
   * Redirects legacy /matrix URLs to the context overview.
   */
  public function redirectLegacyMatrix(): RedirectResponse {
    return $this->redirect('ps_context.context_overview', [], [], 301);
  }

  /**
   * Redirects legacy /matrix/translate to the context translate tab.
   */
  public function redirectLegacyMatrixTranslate(): RedirectResponse {
    return $this->redirect('ps_context.context_translate', [], [], 301);
  }

  /**
   * Dashboard summary of context rules and labels.
   */
  public function overview(): array {
    $this->addMultilingualHelpMessage();

    $ruleStorage = $this->entityTypeManager->getStorage('ps_context_rule');
    /** @var list<\Drupal\ps_context\Entity\PsContextRuleInterface> $rules */
    $rules = array_values($ruleStorage->loadMultiple());
    usort($rules, static fn(PsContextRuleInterface $a, PsContextRuleInterface $b): int => $a->getWeight() <=> $b->getWeight());

    $profileStorage = $this->entityTypeManager->getStorage('ps_context_label_profile');
    $profiles = $profileStorage->loadMultiple();

    $enabledRules = array_filter($rules, static fn(PsContextRuleInterface $rule): bool => $rule->status());
    $enabledProfiles = array_filter($profiles, static fn($profile): bool => $profile->status());
    $disabledRules = count($rules) - count($enabledRules);
    $disabledProfiles = count($profiles) - count($enabledProfiles);

    $build = [
      '#attached' => [
        'library' => ['ps_context/matrix_admin_overview'],
      ],
      '#cache' => [
        'tags' => ['config:ps_context.rule_list', 'config:ps_context.label_profile_list'],
        'contexts' => ['user.permissions', 'languages:language_interface'],
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-context-admin-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Context drives offer forms, search filters and publication validation from a single source: <strong>asset type × operation type</strong> (and optional field values such as divisible). The <strong>Rules</strong> tab controls behaviour; the <strong>Labels</strong> tab controls wording.'),
      ],
      'why' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Property Search covers offices, coworking, logistics and more — each with different fields (surface m² vs desk capacity, rent vs sale budget units). Instead of hardcoding COW or BUR in PHP, administrators maintain declarative rules and labels that stay aligned across the back office, search bar and front office.'),
      ],
    ];

    $build['stats'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-context-admin-overview__stats']],
      'rules' => $this->buildStatCard(
        (string) $this->t('Rules'),
        (string) count($rules),
        $disabledRules > 0
          ? $this->t('@enabled enabled · @disabled disabled', [
            '@enabled' => count($enabledRules),
            '@disabled' => $disabledRules,
          ])
          : $this->t('@enabled enabled', ['@enabled' => count($enabledRules)]),
        'entity.ps_context_rule.collection',
      ),
      'labels' => $this->buildStatCard(
        (string) $this->t('Labels'),
        (string) count($profiles),
        $disabledProfiles > 0
          ? $this->t('@enabled enabled · @disabled disabled', [
            '@enabled' => count($enabledProfiles),
            '@disabled' => $disabledProfiles,
          ])
          : $this->t('@enabled enabled', ['@enabled' => count($enabledProfiles)]),
        'entity.ps_context_label_profile.collection',
      ),
      'assets' => $this->buildStatCard(
        (string) $this->t('Asset types'),
        (string) count(self::ASSET_CODES),
        $this->t('BUR, COW, ENT, ACT, COM, TER'),
      ),
      'operations' => $this->buildStatCard(
        (string) $this->t('Operations'),
        '3',
        $this->t("I'm flexible (default labels), rent (LOC) and sale (VEN)"),
      ),
    ];

    $build['workflow'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-context-admin-overview__panel']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('How Context works'),
      ],
      'steps' => [
        '#theme' => 'item_list',
        '#items' => [
          $this->t('<strong>1. Rules</strong> — show or hide offer tabs and fields, set defaults and validation from asset type, operation and field values.'),
          $this->t('<strong>2. Labels</strong> — override search bar, homepage hero and offer wording per asset × operation (merged by specificity).'),
          $this->t('<strong>3. Front office</strong> — the same context drives the offer form, search filters and publication checks without hardcoded PHP.'),
        ],
      ],
    ];

    $build['search_filters'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-context-admin-overview__panel']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Search filters by asset and operation'),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("Derived from active rules and label profiles — surface or desk capacity plus budget wording per operation. <strong>I'm flexible</strong> is the default state (no rent/sale selected); label profiles with operation <em>*</em> apply there."),
        '#attributes' => ['class' => ['ps-context-admin-overview__panel-description']],
      ],
      'table' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-context-admin-overview__grid-wrap']],
        'matrix' => $this->buildSearchFilterMatrixTable(),
      ],
    ];

    $build['rules_summary'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-context-admin-overview__panel']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Rules (evaluation order)'),
      ],
      'table' => $this->buildRulesSummaryTable($rules),
      'footer' => [
        '#type' => 'link',
        '#title' => $this->t('Manage all rules'),
        '#url' => Url::fromRoute('entity.ps_context_rule.collection'),
        '#attributes' => ['class' => ['ps-context-admin-overview__panel-link']],
      ],
    ];

    $build['labels_summary'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-context-admin-overview__panel']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Labels'),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Merged by specificity (wildcards first, then asset × operation overrides).'),
        '#attributes' => ['class' => ['ps-context-admin-overview__panel-description']],
      ],
      'table' => $this->buildProfilesSummaryTable($profiles),
      'footer' => [
        '#type' => 'link',
        '#title' => $this->t('Manage all labels'),
        '#url' => Url::fromRoute('entity.ps_context_label_profile.collection'),
        '#attributes' => ['class' => ['ps-context-admin-overview__panel-link']],
      ],
    ];

    $navigationLinks = $this->buildNavigationLinks();
    if ($navigationLinks !== []) {
      $build['navigation'] = [
        '#type' => 'details',
        '#title' => $this->t('Context sections'),
        '#open' => FALSE,
        '#attributes' => ['class' => ['ps-context-admin-overview__navigation']],
        'links' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-context-admin-overview__navigation-links']],
          '#theme' => 'admin_block_content',
          '#content' => $navigationLinks,
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-context-admin-overview__footer']],
      'hub' => Link::createFromRoute(
        $this->t('Back to PS configuration hub'),
        'ps_core.config',
      )->toRenderable(),
    ];

    return $build;
  }

  /**
   * Builds a dashboard stat card.
   *
   * @return array<string, mixed>
   */
  private function buildStatCard(
    string $label,
    string $value,
    \Stringable|string $meta,
    ?string $route = NULL,
  ): array {
    $valueRender = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => $value,
      '#attributes' => ['class' => ['ps-context-admin-overview__stat-value']],
    ];
    if ($route !== NULL && Url::fromRoute($route)->access($this->currentUser())) {
      $valueRender = [
        '#type' => 'link',
        '#title' => $value,
        '#url' => Url::fromRoute($route),
        '#attributes' => ['class' => ['ps-context-admin-overview__stat-value']],
      ];
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-context-admin-overview__stat']],
      'value' => $valueRender,
      'label' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $label,
        '#attributes' => ['class' => ['ps-context-admin-overview__stat-label']],
      ],
      'meta' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $meta,
        '#attributes' => ['class' => ['ps-context-admin-overview__stat-meta']],
      ],
    ];
  }

  /**
   * Builds admin block navigation links for context child routes.
   *
   * @return list<array<string, mixed>>
   */
  private function buildNavigationLinks(): array {
    $items = [
      [
        'title' => $this->t('Rules'),
        'description' => $this->t('Conditional behaviour for offer forms, search and validation.'),
        'route' => 'entity.ps_context_rule.collection',
      ],
      [
        'title' => $this->t('Labels'),
        'description' => $this->t('Search, hero and offer wording by asset × operation.'),
        'route' => 'entity.ps_context_label_profile.collection',
      ],
      [
        'title' => $this->t('Translate'),
        'description' => $this->t('Translate rule titles and label wording for other languages.'),
        'route' => 'ps_context.context_translate',
      ],
    ];

    $links = [];
    foreach ($items as $item) {
      $url = Url::fromRoute($item['route']);
      if (!$url->access($this->currentUser())) {
        continue;
      }

      $links[] = [
        'title' => $item['title'],
        'url' => $url,
        'description' => $item['description'],
      ];
    }

    return $links;
  }

  /**
   * Adds multilingual guidance when config translation is available.
   */
  private function addMultilingualHelpMessage(): void {
    if (!$this->moduleHandler->moduleExists('config_translation')) {
      return;
    }

    $default = $this->languageManager->getDefaultLanguage()->getId();
    $targets = array_filter(
      $this->languageManager->getLanguages(),
      static fn(string $langcode): bool => $langcode !== $default,
      ARRAY_FILTER_USE_KEY,
    );
    if ($targets === []) {
      return;
    }

    $this->messenger()->addMessage(
      $this->t('This site is multilingual. Edit default-language wording in the Rules and Labels tabs. Use the Translate tab for other enabled languages.'),
      'info',
    );
  }

  /**
   * @return array<string, mixed>
   */
  private function buildSearchFilterMatrixTable(): array {
    $groups = [];
    foreach (self::OPERATION_COLUMNS as $column) {
      $groups[] = [
        'label' => match ($column['key']) {
          'flexible' => (string) $this->t("I'm flexible"),
          'loc' => (string) $this->t('Rent (LOC)'),
          'ven' => (string) $this->t('Sale (VEN)'),
          default => $column['key'],
        },
        'price_header' => (string) $this->getPriceColumnHeader($column['key']),
        'operation' => $column['operation'],
      ];
    }

    $rows = [];
    foreach (self::ASSET_CODES as $asset) {
      $cells = [];
      foreach ($groups as $group) {
        $visibility = $this->searchFilterVisibility->resolve($asset, $group['operation']);
        $cells[] = [
          'surface' => $this->formatSurfaceFilterCell($visibility),
          'price' => $this->formatPriceFilterCell($asset, $group['operation'], $visibility),
        ];
      }
      $rows[] = [
        'asset' => $asset,
        'cells' => $cells,
      ];
    }

    return [
      '#type' => 'inline_template',
      '#template' => '{% apply spaceless %}
<table class="ps-context-admin-overview__grid">
  <thead>
    <tr>
      <th rowspan="2">{{ asset_label|e }}</th>
      {% for group in groups %}
        <th colspan="2">{{ group.label|e }}</th>
      {% endfor %}
    </tr>
    <tr>
      {% for group in groups %}
        <th>{{ surface_label|e }}</th>
        <th>{{ group.price_header|e }}</th>
      {% endfor %}
    </tr>
  </thead>
  <tbody>
    {% for row in rows %}
      <tr>
        <td>{{ row.asset|e }}</td>
        {% for cell in row.cells %}
          <td>{{ cell.surface|e }}</td>
          <td>{{ cell.price|e }}</td>
        {% endfor %}
      </tr>
    {% endfor %}
  </tbody>
</table>
{% endapply %}',
      '#context' => [
        'asset_label' => (string) $this->t('Asset'),
        'surface_label' => (string) $this->t('Surface'),
        'groups' => $groups,
        'rows' => $rows,
      ],
    ];
  }

  /**
   * Price sub-column header per operation context.
   */
  private function getPriceColumnHeader(string $operationKey): \Stringable|string {
    return match ($operationKey) {
      'flexible' => $this->t('Budget'),
      'loc' => $this->t('Rent'),
      'ven' => $this->t('Price'),
      default => '',
    };
  }

  /**
   * @param array{show_surface: bool, show_capacity: bool, show_price: bool, primary_filter: string} $visibility
   */
  private function formatSurfaceFilterCell(array $visibility): string {
    if ($visibility['show_capacity']) {
      return (string) $this->t('Desk capacity');
    }
    if ($visibility['show_surface']) {
      return (string) $this->t('Surface');
    }

    return '—';
  }

  /**
   * @param array{show_surface: bool, show_capacity: bool, show_price: bool, primary_filter: string} $visibility
   */
  private function formatPriceFilterCell(string $asset, ?string $operation, array $visibility): string {
    if ($operation !== NULL && !$visibility['show_price']) {
      return '—';
    }

    return $this->contextLabelResolver->resolve($asset, $operation)['field_label'];
  }

  /**
   * @param list<\Drupal\ps_context\Entity\PsContextRuleInterface> $rules
   *
   * @return array<string, mixed>
   */
  private function buildRulesSummaryTable(array $rules): array {
    $header = [
      $this->t('Rule'),
      $this->t('Weight'),
      $this->t('Conditions'),
      $this->t('Actions'),
      $this->t('Status'),
    ];

    $rows = [];
    foreach ($rules as $rule) {
      $conditions = $rule->getConditions();
      if ($conditions === []) {
        $condSummary = $this->t('Always applies');
      }
      else {
        $logic = $rule->getConditionsLogic();
        $parts = array_map(
          static fn(array $c): string => ($c['field_name'] ?? '?') . ' ' . ($c['operator'] ?? '=') . ' ' . ($c['value'] ?? ''),
          $conditions,
        );
        $condSummary = implode(' ' . $logic . ' ', $parts);
      }

      $rows[] = [
        Link::fromTextAndUrl($rule->label(), $rule->toUrl('edit-form')),
        $rule->getWeight(),
        $condSummary,
        count($rule->getActions()),
        $rule->status() ? $this->t('Enabled') : $this->t('Disabled'),
      ];
    }

    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No rules configured.'),
    ];
  }

  /**
   * @param array<string, \Drupal\ps_context\Entity\PsContextLabelProfileInterface> $profiles
   *
   * @return array<string, mixed>
   */
  private function buildProfilesSummaryTable(array $profiles): array {
    $header = [
      $this->t('Label'),
      $this->t('Asset'),
      $this->t('Operation'),
      $this->t('Labels'),
      $this->t('Status'),
    ];

    $rows = [];
    foreach ($profiles as $profile) {
      $rows[] = [
        Link::fromTextAndUrl($profile->label(), $profile->toUrl('edit-form')),
        $profile->getAssetType(),
        $profile->getOperationType(),
        count(array_filter($profile->getLabels(), static fn(string $v): bool => $v !== '')),
        $profile->status() ? $this->t('Enabled') : $this->t('Disabled'),
      ];
    }

    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No labels configured.'),
    ];
  }

}
