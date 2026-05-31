<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_favorite\Entity\FavoriteTarget;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class FavoriteSettingsForm extends ConfigFormBase implements ContainerInjectionInterface {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityTypeBundleInfoInterface $entityTypeBundleInfo,
    private readonly EntityDisplayRepositoryInterface $entityDisplayRepository,
  ) {}

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity_display.repository'),
    );
  }

  protected function getEditableConfigNames(): array {
    return ['ps_favorite.settings'];
  }

  public function getFormId(): string {
    return 'ps_favorite_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_favorite.settings');
    $targetEntities = $this->loadTargetEntities();
    $targetRules = $this->parseLimitRules((string) $config->get('max_favorites_map'));
    $viewModeRules = $this->parseViewModeRules((string) $config->get('view_mode_map'));
    $favoriteTargets = $this->buildFavoriteTargets();

    $form['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-favorite-settings__intro']],
    ];
    $form['intro']['description'] = [
      '#markup' => '<p>' . $this->t('Configure which entity targets can be favorited and how favorite cards should be rendered.') . '</p>',
    ];

    $form['targets'] = [
      '#type' => 'details',
      '#title' => $this->t('Favorite targets'),
      '#open' => TRUE,
    ];

    $form['targets']['help'] = [
      '#markup' => '<p>' . $this->t('Enable favorites per entity target, then choose whether the target has no limit or a fixed maximum.') . '</p>',
    ];

    $form['targets']['favorite_targets'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    foreach ($favoriteTargets as $entityTypeId => $group) {
      $detailsKey = 'entity_type__' . $entityTypeId;
      $form['targets']['favorite_targets'][$detailsKey] = [
        '#type' => 'details',
        '#title' => $group['label'],
        '#open' => $entityTypeId === 'node',
      ];
      $form['targets']['favorite_targets'][$detailsKey]['table'] = [
        '#type' => 'table',
        '#header' => [
          $this->t('Bundle'),
          $this->t('Enable'),
          $this->t('Limit mode'),
          $this->t('Max favorites'),
          $this->t('Preferred view mode'),
        ],
        '#empty' => $this->t('No eligible bundle found for this entity type.'),
      ];

      foreach ($group['targets'] as $targetKey => $target) {
        $targetEntity = $targetEntities[$targetKey] ?? NULL;
        $legacyLimit = $targetRules[$targetKey] ?? NULL;
        $legacyViewMode = $viewModeRules[$targetKey] ?? NULL;
        $row = $this->buildTargetRow(
          $target,
          $targetEntity?->isEnabled() ?? ($legacyLimit !== NULL || $legacyViewMode !== NULL),
          $targetEntity?->getMaxFavorites() ?? $legacyLimit,
          $targetEntity?->getViewMode() ?: $legacyViewMode,
        );
        $form['targets']['favorite_targets'][$detailsKey]['table'][$targetKey] = $row;
      }
    }

    $form['rendering'] = [
      '#type' => 'details',
      '#title' => $this->t('Favorite card rendering'),
      '#open' => TRUE,
    ];
    $form['rendering']['rendering_help'] = [
      '#theme' => 'item_list',
      '#title' => $this->t('Rendering fallback order'),
      '#items' => [
        $this->t('Configured view mode for the matching entity target.'),
        $this->t('Card favorite view mode when available.'),
        $this->t('Teaser view mode when available.'),
        $this->t('Fallback title with canonical link.'),
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $favoriteTargets = (array) $form_state->getValue(['favorite_targets'], []);
    if ($favoriteTargets === []) {
      $favoriteTargets = (array) $form_state->getValue(['targets', 'favorite_targets'], []);
    }
    foreach ($favoriteTargets as $entityTypeKey => $entityTypeGroup) {
      $rows = (array) ($entityTypeGroup['table'] ?? []);
      foreach ($rows as $targetKey => $row) {
        if (empty($row['enabled'])) {
          continue;
        }

        $limitMode = (string) ($row['limit_mode'] ?? 'unlimited');
        $limitValue = (string) ($row['limit'] ?? '');
        if ($limitMode === 'limited') {
          if ($limitValue === '' || !is_numeric($limitValue) || (int) $limitValue < 1) {
            $form_state->setErrorByName('favorite_targets][' . $entityTypeKey . '][table][' . $targetKey . '][limit', $this->t('Set a positive maximum for @target when using a fixed limit.', [
              '@target' => (string) $targetKey,
            ]));
          }
        }

        $viewMode = (string) ($row['view_mode'] ?? '');
        if ($viewMode !== '' && !preg_match('/^[a-z0-9_]+$/', $viewMode)) {
          $form_state->setErrorByName('favorite_targets][' . $entityTypeKey . '][table][' . $targetKey . '][view_mode', $this->t('Selected view mode is invalid for @target.', [
            '@target' => (string) $targetKey,
          ]));
        }
      }
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $favoriteTargets = (array) $form_state->getValue(['favorite_targets'], []);
    if ($favoriteTargets === []) {
      $favoriteTargets = (array) $form_state->getValue(['targets', 'favorite_targets'], []);
    }

    [$maxFavoritesMap, $viewModeMap] = $this->buildConfigMapsFromFormValues($favoriteTargets);
    $this->saveTargetEntitiesFromFormValues($favoriteTargets);

    $this->configFactory->getEditable('ps_favorite.settings')
      ->set('max_favorites_map', $maxFavoritesMap)
      ->set('view_mode_map', $viewModeMap)
      ->save();

    parent::submitForm($form, $form_state);

    $this->messenger()->addStatus($this->formatPlural(
      count($this->parseLimitRules($maxFavoritesMap)),
      'Saved 1 favorite target rule.',
      'Saved @count favorite target rules.',
    ));
    $this->messenger()->addStatus($this->formatPlural(
      count($this->parseViewModeRules($viewModeMap)),
      'Saved 1 view mode rule.',
      'Saved @count view mode rules.',
    ));
  }

  /**
   * @return array<string, array{label: string, targets: array<string, array{entity_type_id: string, bundle: string, label: string, view_mode_options: array<string, string>}>}>
   *   Displayable favorite targets grouped by entity type.
   */
  private function buildFavoriteTargets(): array {
    $targets = [];
    foreach ($this->entityTypeManager->getDefinitions() as $entityTypeId => $definition) {
      if (!$this->isEligibleFavoriteEntityType($entityTypeId)) {
        continue;
      }

      $entityTypeLabel = (string) $definition->getLabel();
      $bundleInfo = $this->entityTypeBundleInfo->getBundleInfo($entityTypeId);
      if ($bundleInfo === []) {
        $bundleInfo = ['*' => ['label' => $this->t('All items')]];
      }

      $groupTargets = [];
      foreach ($bundleInfo as $bundle => $info) {
        $targetKey = $entityTypeId . '.' . $bundle;
        $groupTargets[$targetKey] = [
          'entity_type_id' => $entityTypeId,
          'bundle' => $bundle,
          'label' => (string) ($info['label'] ?? $bundle),
          'view_mode_options' => $this->getViewModeOptions($entityTypeId, $bundle),
        ];
      }

      if ($groupTargets !== []) {
        $targets[$entityTypeId] = [
          'label' => $entityTypeLabel,
          'targets' => $groupTargets,
        ];
      }
    }

    uasort($targets, static fn (array $left, array $right): int => strnatcasecmp($left['label'], $right['label']));
    return $targets;
  }

  private function isEligibleFavoriteEntityType(string $entityTypeId): bool {
    $definition = $this->entityTypeManager->getDefinition($entityTypeId);
    if (!$definition->entityClassImplements('Drupal\\Core\\Entity\\ContentEntityInterface') || !$definition->hasViewBuilderClass()) {
      return FALSE;
    }

    if (!$definition->hasLinkTemplate('canonical')) {
      return FALSE;
    }

    if (!in_array($entityTypeId, ['node', 'media', 'taxonomy_term'], TRUE) && !str_starts_with($entityTypeId, 'ps_')) {
      return FALSE;
    }

    $bundleInfo = $this->entityTypeBundleInfo->getBundleInfo($entityTypeId);
    if ($bundleInfo !== []) {
      return TRUE;
    }

    return str_starts_with($entityTypeId, 'ps_');
  }

  /**
   * @param array{entity_type_id: string, bundle: string, label: string, view_mode_options: array<string, string>} $target
   *   Target metadata.
   */
  private function buildTargetRow(array $target, bool $enabled, ?int $limit, ?string $viewMode): array {
    $limitMode = $enabled && $limit !== NULL && $limit > 0 ? 'limited' : 'unlimited';

    return [
      'bundle_label' => [
        '#plain_text' => $target['label'],
      ],
      'enabled' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Enable'),
        '#title_display' => 'invisible',
        '#default_value' => $enabled,
      ],
      'limit_mode' => [
        '#type' => 'select',
        '#title' => $this->t('Limit mode'),
        '#title_display' => 'invisible',
        '#options' => [
          'unlimited' => $this->t('No limit'),
          'limited' => $this->t('Fixed limit'),
        ],
        '#default_value' => $limitMode,
      ],
      'limit' => [
        '#type' => 'number',
        '#title' => $this->t('Max favorites'),
        '#title_display' => 'invisible',
        '#default_value' => $limitMode === 'limited' ? $limit : '',
        '#min' => 1,
        '#step' => 1,
        '#size' => 6,
        '#placeholder' => '10',
      ],
      'view_mode' => [
        '#type' => 'select',
        '#title' => $this->t('Preferred view mode'),
        '#title_display' => 'invisible',
        '#options' => ['' => $this->t('Automatic fallback')] + $target['view_mode_options'],
        '#default_value' => $viewMode ?? '',
      ],
    ];
  }

  /**
   * @return array<string, string>
   *   View mode options for a bundle.
   */
  private function getViewModeOptions(string $entityTypeId, string $bundle): array {
    $options = $bundle === '*'
      ? $this->entityDisplayRepository->getViewModeOptions($entityTypeId)
      : $this->entityDisplayRepository->getViewModeOptionsByBundle($entityTypeId, $bundle);
    natcasesort($options);
    return $options;
  }

  /**
   * @param array<string, mixed> $favoriteTargets
   *   Submitted target values.
   *
   * @return array{0: string, 1: string}
   *   Limit map and view mode map as stored config strings.
   */
  private function buildConfigMapsFromFormValues(array $favoriteTargets): array {
    $limitRules = [];
    $viewModeRules = [];

    foreach ($favoriteTargets as $group) {
      foreach ((array) ($group['table'] ?? []) as $targetKey => $row) {
        if (empty($row['enabled'])) {
          continue;
        }

        $limitMode = (string) ($row['limit_mode'] ?? 'unlimited');
        $limitRules[] = $targetKey . ':' . ($limitMode === 'limited' ? (int) ($row['limit'] ?? 0) : 0);

        $viewMode = trim((string) ($row['view_mode'] ?? ''));
        if ($viewMode !== '') {
          $viewModeRules[] = $targetKey . ':' . $viewMode;
        }
      }
    }

    return [implode("\n", $limitRules), implode("\n", $viewModeRules)];
  }

  /**
   * @param array<string, mixed> $favoriteTargets
   *   Submitted target values.
   */
  private function saveTargetEntitiesFromFormValues(array $favoriteTargets): void {
    $storage = $this->entityTypeManager->getStorage('ps_favorite_target');
    $existing = $this->loadTargetEntities();

    foreach ($favoriteTargets as $group) {
      foreach ((array) ($group['table'] ?? []) as $targetKey => $row) {
        $enabled = !empty($row['enabled']);
        $limitMode = (string) ($row['limit_mode'] ?? 'unlimited');
        $limit = $enabled && $limitMode === 'limited' ? max(1, (int) ($row['limit'] ?? 0)) : 0;
        $viewMode = trim((string) ($row['view_mode'] ?? ''));
        $label = (string) ($row['bundle_label'] ?? $targetKey);

        if (!$enabled) {
          if (isset($existing[$targetKey])) {
            $existing[$targetKey]->delete();
          }
          continue;
        }

        $targetEntity = $existing[$targetKey] ?? $storage->create(['id' => $targetKey]);
        $targetEntity->set('label', $label);
        $targetEntity->set('entity_type_id', (string) ($group['entity_type_id'] ?? 'node'));
        $targetEntity->set('bundle', (string) ($group['bundle'] ?? ''));
        $targetEntity->set('status', TRUE);
        $targetEntity->set('max_favorites', $limit);
        $targetEntity->set('view_mode', $viewMode);
        $targetEntity->save();
      }
    }
  }

  /**
   * @return array<string, \Drupal\ps_favorite\Entity\FavoriteTarget>
   *   Loaded target entities keyed by target key.
   */
  private function loadTargetEntities(): array {
    $targets = [];
    foreach ($this->entityTypeManager->getStorage('ps_favorite_target')->loadMultiple() as $target) {
      if ($target instanceof FavoriteTarget) {
        $targets[$target->getTargetKey()] = $target;
      }
    }

    return $targets;
  }

  /**
   * @return array<string, int>
   *   Parsed target rules keyed by entity target.
   */
  private function parseLimitRules(string $raw): array {
    $rules = [];
    $lines = preg_split('/\r\n|\r|\n/', trim($raw)) ?: [];
    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === '') {
        continue;
      }
      if (preg_match('/^([a-z0-9_]+\.(?:[a-z0-9_]+|\*)):(\d+)$/', $line, $matches)) {
        $rules[$matches[1]] = (int) $matches[2];
      }
    }

    return $rules;
  }

  /**
   * @return array<string, string>
   *   Parsed view mode rules keyed by entity target.
   */
  private function parseViewModeRules(string $raw): array {
    $rules = [];
    $lines = preg_split('/\r\n|\r|\n/', trim($raw)) ?: [];
    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === '') {
        continue;
      }
      if (preg_match('/^([a-z0-9_]+\.(?:[a-z0-9_]+|\*)):([a-z0-9_]+)$/', $line, $matches)) {
        $rules[$matches[1]] = $matches[2];
      }
    }

    return $rules;
  }

}
