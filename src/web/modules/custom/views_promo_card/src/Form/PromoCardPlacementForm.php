<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Form;

use Drupal\Core\Condition\ConditionManager;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\views_promo_card\Entity\PromoCardPlacement;
use Drupal\views_promo_card\Entity\PromoCardPlacementInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for promo card placement entities (split view + preview).
 */
final class PromoCardPlacementForm extends EntityForm {

  /**
   * Condition plugin IDs available for placements.
   *
   * @var list<string>
   */
  private const ALLOWED_CONDITIONS = [
    'request_path',
    'user_role',
    'language',
    'promo_card_min_results',
    'promo_card_pager_page',
    'promo_card_route_name',
    'promo_card_request_parameter',
    'promo_card_views_exposed_filter',
  ];

  /**
   * Condition plugin manager.
   */
  private ConditionManager $conditionManager;

  /**
   * Language manager.
   */
  private LanguageManagerInterface $languageManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    /** @var static $instance */
    $instance = parent::create($container);
    $instance->conditionManager = $container->get('plugin.manager.condition');
    $instance->languageManager = $container->get('language_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\views_promo_card\Entity\PromoCardPlacementInterface $entity */
    $entity = $this->entity;

    $form['#attached']['library'][] = 'views_promo_card/promo_card_admin';
    $form['#attributes']['class'][] = 'promo-card-admin-form';
    $form['#attributes']['class'][] = 'promo-card-placement-form';
    $form['#attributes']['data-preview-url'] = Url::fromRoute('views_promo_card.placement_preview')->toString();

    $form['layout'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-admin__layout']],
      '#tree' => TRUE,
    ];

    $form['layout']['editor'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-admin__editor']],
    ];

    $editor = &$form['layout']['editor'];

    $editor['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $entity->label(),
      '#required' => TRUE,
    ];

    $editor['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\\Drupal\\views_promo_card\\Entity\\PromoCardPlacement::load',
      ],
      '#disabled' => !$entity->isNew(),
      '#required' => TRUE,
    ];

    $editor['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $entity->status(),
    ];

    $editor['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#default_value' => $entity->get('weight') ?? 0,
    ];

    $view_options = [];
    /** @var \Drupal\views\Entity\View[] $views */
    $views = $this->entityTypeManager->getStorage('view')->loadMultiple();
    foreach ($views as $view) {
      $view_options[$view->id()] = $view->label() . ' (' . $view->id() . ')';
    }
    asort($view_options);

    $selected_view = $form_state->getValue(['layout', 'editor', 'target', 'view_id']) ?? $entity->getViewId();
    $display_options = $this->getDisplayOptions((string) $selected_view);
    $selected_display = $form_state->getValue(['layout', 'editor', 'target', 'display_id']) ?? $entity->getDisplayId();
    if ($display_options !== [] && !isset($display_options[$selected_display])) {
      $selected_display = (string) array_key_first($display_options);
    }

    $editor['target'] = [
      '#type' => 'details',
      '#title' => $this->t('Target view'),
      '#open' => TRUE,
    ];
    $editor['target']['view_id'] = [
      '#type' => 'select',
      '#title' => $this->t('View'),
      '#options' => $view_options,
      '#default_value' => $entity->getViewId(),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updateDisplayOptions',
        'wrapper' => 'display-id-wrapper',
      ],
    ];
    $editor['target']['display_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Display'),
      '#options' => $display_options,
      '#default_value' => $selected_display,
      '#required' => TRUE,
      '#prefix' => '<div id="display-id-wrapper">',
      '#suffix' => '</div>',
    ];

    $card_options = [];
    /** @var \Drupal\views_promo_card\Entity\PromoCardInterface[] $cards */
    $cards = $this->entityTypeManager->getStorage('promo_card')->loadMultiple();
    foreach ($cards as $card) {
      $card_options[$card->id()] = $card->label();
    }

    $editor['cards'] = [
      '#type' => 'details',
      '#title' => $this->t('Promo card'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];
    $editor['cards']['card_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Primary card'),
      '#options' => $card_options,
      '#empty_option' => $this->t('- Select -'),
      '#default_value' => $entity->getCards()[0]['promo_card'] ?? '',
      '#required' => TRUE,
    ];
    $editor['cards']['card_weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Card weight'),
      '#default_value' => $entity->getCards()[0]['weight'] ?? 0,
    ];

    $editor['rotation'] = [
      '#type' => 'select',
      '#title' => $this->t('Rotation'),
      '#options' => [
        PromoCardPlacement::ROTATION_WEIGHT_FIRST => $this->t('Always first card (by weight)'),
        PromoCardPlacement::ROTATION_ROUND_ROBIN => $this->t('Round robin'),
        PromoCardPlacement::ROTATION_RANDOM => $this->t('Random'),
      ],
      '#default_value' => $entity->getRotation(),
    ];

    $rules = $entity->getPlacementRules();
    $default_rule = $rules[0] ?? ['type' => 'fixed', 'position' => 3];
    $interval_rule = NULL;
    foreach ($rules as $rule) {
      if (($rule['type'] ?? '') === 'interval') {
        $interval_rule = $rule;
        break;
      }
    }

    $editor['placement_rules'] = [
      '#type' => 'details',
      '#title' => $this->t('Placement rules'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];
    $editor['placement_rules']['fixed_position'] = [
      '#type' => 'number',
      '#title' => $this->t('Insert after row (fixed)'),
      '#description' => $this->t('1-based index. Card is inserted after this result row.'),
      '#default_value' => $default_rule['type'] === 'fixed' ? ($default_rule['position'] ?? 3) : 3,
      '#min' => 1,
      '#required' => TRUE,
    ];
    $editor['placement_rules']['interval_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable interval rule'),
      '#default_value' => $interval_rule !== NULL,
    ];
    $editor['placement_rules']['interval_every'] = [
      '#type' => 'number',
      '#title' => $this->t('Repeat every N rows'),
      '#default_value' => $interval_rule['every'] ?? 8,
      '#min' => 1,
      '#states' => [
        'visible' => [
          ':input[name="layout[editor][placement_rules][interval_enabled]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $editor['placement_rules']['interval_start'] = [
      '#type' => 'number',
      '#title' => $this->t('Interval starts at row'),
      '#default_value' => $interval_rule['start_at'] ?? 8,
      '#min' => 1,
      '#states' => [
        'visible' => [
          ':input[name="layout[editor][placement_rules][interval_enabled]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $editor['max_insertions_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Max insertions per page'),
      '#default_value' => $entity->getMaxInsertionsPerPage(),
      '#min' => 1,
    ];

    $editor += $this->buildConditionsForm($entity, $form_state);

    $form['layout']['preview'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-admin__preview']],
    ];
    $form['layout']['preview']['toolbar'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-admin__preview-toolbar']],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'strong',
        '#value' => $this->t('Placement preview'),
      ],
      'hint' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => ' ' . $this->t('(mock search grid, 3 columns)'),
        '#attributes' => ['class' => ['promo-card-admin__preview-hint']],
      ],
    ];
    $form['layout']['preview']['target'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['promo-card-admin__preview-target'],
        'id' => 'promo-card-preview-target',
        'data-preview-empty' => $this->t('Select a promo card to preview placement in the search grid.'),
      ],
    ];

    $form['#attached']['drupalSettings']['viewsPromoCard'] = [
      'previewUrl' => Url::fromRoute('views_promo_card.placement_preview')->toString(),
    ];

    return $form;
  }

  /**
   * Builds the conditions section with vertical tabs (block visibility pattern).
   */
  private function buildConditionsForm(PromoCardPlacementInterface $entity, FormStateInterface $form_state): array {
    $stored = [];
    foreach ($entity->getConditions() as $item) {
      $stored[(string) ($item['id'] ?? '')] = $item;
    }

    $definitions = $this->conditionManager->getDefinitions();

    $form['conditions_section'] = [
      '#type' => 'details',
      '#title' => $this->t('Conditions'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['conditions_section']['conditions_logic'] = [
      '#type' => 'radios',
      '#title' => $this->t('Condition logic'),
      '#options' => [
        'and' => $this->t('All conditions must pass'),
        'or' => $this->t('Any condition may pass'),
      ],
      '#default_value' => $entity->getConditionsLogic(),
    ];

    $form['conditions_section']['conditions_tabs'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Condition types'),
      '#parents' => ['conditions_tabs'],
      '#attached' => [
        'library' => [
          'block/drupal.block',
        ],
      ],
    ];

    foreach ($this->getAllowedConditionIds() as $condition_id) {
      $configuration = $stored[$condition_id] ?? ['id' => $condition_id];
      /** @var \Drupal\Core\Condition\ConditionInterface $condition */
      $condition = $this->conditionManager->createInstance($condition_id, $configuration);
      $form_state->set(['conditions', $condition_id], $condition);

      $condition_form = $condition->buildConfigurationForm([], $form_state);
      $condition_form['#type'] = 'details';
      $condition_form['#title'] = (string) $definitions[$condition_id]['label'];
      $condition_form['#group'] = 'conditions_tabs';
      $condition_form['#parents'] = [
        'layout',
        'editor',
        'conditions_section',
        $condition_id,
      ];

      $form['conditions_section'][$condition_id] = $condition_form;
    }

    $this->applyBlockConditionFormTweaks($form['conditions_section']);

    return $form;
  }

  /**
   * Returns condition plugin IDs always shown on the placement form.
   *
   * @return list<string>
   *   Allowed condition plugin IDs.
   */
  private function getAllowedConditionIds(): array {
    $ids = [];
    foreach (self::ALLOWED_CONDITIONS as $condition_id) {
      if ($condition_id === 'language' && !$this->languageManager->isMultilingual()) {
        continue;
      }
      if ($this->conditionManager->hasDefinition($condition_id)) {
        $ids[] = $condition_id;
      }
    }
    return $ids;
  }

  /**
   * Applies block visibility form tweaks for core condition plugins.
   */
  private function applyBlockConditionFormTweaks(array &$conditions_section): void {
    $disable_negation = ['language', 'user_role'];
    foreach ($disable_negation as $condition_id) {
      if (isset($conditions_section[$condition_id]['negate'])) {
        $conditions_section[$condition_id]['negate']['#type'] = 'value';
        $conditions_section[$condition_id]['negate']['#value'] = $conditions_section[$condition_id]['negate']['#default_value'];
      }
    }

    if (isset($conditions_section['user_role'])) {
      $conditions_section['user_role']['#title'] = $this->t('Roles');
      unset($conditions_section['user_role']['roles']['#description']);
    }

    if (isset($conditions_section['request_path'])) {
      $conditions_section['request_path']['#title'] = $this->t('Pages');
      $conditions_section['request_path']['negate']['#type'] = 'radios';
      $conditions_section['request_path']['negate']['#default_value'] = (int) $conditions_section['request_path']['negate']['#default_value'];
      $conditions_section['request_path']['negate']['#title_display'] = 'invisible';
      $conditions_section['request_path']['negate']['#options'] = [
        $this->t('Show for the listed pages'),
        $this->t('Hide for the listed pages'),
      ];
    }
  }

  /**
   * Returns a condition subform element from the built form array.
   */
  private function getConditionFormElement(array $form, string $condition_id): ?array {
    return $form['layout']['editor']['conditions_section'][$condition_id] ?? NULL;
  }

  /**
   * AJAX callback for display select options.
   */
  public function updateDisplayOptions(array &$form, FormStateInterface $form_state): array {
    return $form['layout']['editor']['target']['display_id'];
  }

  /**
   * Returns display options for a view.
   *
   * @return array<string, string>
   *   Display labels keyed by ID.
   */
  private function getDisplayOptions(string $view_id): array {
    if ($view_id === '') {
      return [];
    }
    $view = $this->entityTypeManager->getStorage('view')->load($view_id);
    if ($view === NULL) {
      return [];
    }
    $options = [];
    foreach ($view->get('display') as $display_id => $display) {
      $options[$display_id] = ($display['display_title'] ?? $display_id) . ' (' . $display_id . ')';
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state): void {
    $editor = $form_state->getValue(['layout', 'editor']);
    if (!is_array($editor)) {
      return;
    }

    foreach (['label', 'id', 'weight', 'rotation', 'max_insertions_per_page'] as $key) {
      if (array_key_exists($key, $editor)) {
        $entity->set($key, $editor[$key]);
      }
    }
    $conditions_section = $editor['conditions_section'] ?? [];
    if (is_array($conditions_section) && array_key_exists('conditions_logic', $conditions_section)) {
      $entity->set('conditions_logic', $conditions_section['conditions_logic']);
    }
    $entity->set('status', (bool) ($editor['status'] ?? FALSE));

    $target = $editor['target'] ?? [];
    if (is_array($target)) {
      foreach (['view_id', 'display_id'] as $key) {
        if (array_key_exists($key, $target)) {
          $entity->set($key, $target[$key]);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);
    $this->validateConditions($form, $form_state);
  }

  /**
   * Validates all condition subforms (block visibility pattern).
   */
  private function validateConditions(array $form, FormStateInterface $form_state): void {
    foreach ($this->getAllowedConditionIds() as $condition_id) {
      $condition = $form_state->get(['conditions', $condition_id]);
      $condition_form = $this->getConditionFormElement($form, $condition_id);
      if ($condition === NULL || $condition_form === NULL) {
        continue;
      }
      $condition->validateConfigurationForm(
        $condition_form,
        SubformState::createForSubform($condition_form, $form, $form_state),
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);
    $this->entity->set('conditions', $this->submitConditions($form, $form_state));
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\views_promo_card\Entity\PromoCardPlacementInterface $entity */
    $entity = $this->entity;
    $editor = $form_state->getValue(['layout', 'editor']);
    if (!is_array($editor)) {
      $editor = [];
    }

    $rules = [
      [
        'type' => 'fixed',
        'position' => max(1, (int) ($editor['placement_rules']['fixed_position'] ?? 3)),
      ],
    ];
    if ((bool) ($editor['placement_rules']['interval_enabled'] ?? FALSE)) {
      $rules[] = [
        'type' => 'interval',
        'every' => max(1, (int) ($editor['placement_rules']['interval_every'] ?? 8)),
        'start_at' => max(1, (int) ($editor['placement_rules']['interval_start'] ?? 8)),
      ];
    }

    $entity->set('cards', [
      [
        'promo_card' => (string) ($editor['cards']['card_id'] ?? ''),
        'weight' => (int) ($editor['cards']['card_weight'] ?? 0),
      ],
    ]);
    $entity->set('placement_rules', $rules);
    $entity->set('conditions', $this->submitConditions($form, $form_state));

    $status = parent::save($form, $form_state);
    $this->messenger()->addStatus($this->t('Placement %label saved.', ['%label' => $entity->label()]));
    $form_state->setRedirectUrl($entity->toUrl('collection'));
    return $status;
  }

  /**
   * Submits all condition subforms and returns persisted configuration.
   *
   * @return list<array<string, mixed>>
   *   Condition configuration arrays.
   */
  private function submitConditions(array $form, FormStateInterface $form_state): array {
    $conditions = [];
    foreach ($this->getAllowedConditionIds() as $condition_id) {
      $condition_form = $this->getConditionFormElement($form, $condition_id);
      if ($condition_form === NULL) {
        continue;
      }
      $condition = $form_state->get(['conditions', $condition_id]);
      if ($condition === NULL) {
        continue;
      }
      $subform_state = SubformState::createForSubform($condition_form, $form, $form_state);
      $condition->submitConfigurationForm($condition_form, $subform_state);
      $config = $this->normalizeConditionConfiguration($condition->getConfiguration());
      if ($this->conditionConfigurationIsValid($config)) {
        $conditions[] = $config;
      }
    }
    return $conditions;
  }

  /**
   * Normalizes a condition configuration before persistence.
   */
  private function normalizeConditionConfiguration(array $config): array {
    $config['negate'] = !empty($config['negate']);
    return $config;
  }

  /**
   * Checks whether a submitted condition has enough data to persist.
   */
  private function conditionConfigurationIsValid(array $config): bool {
    $id = (string) ($config['id'] ?? '');
    return match ($id) {
      'request_path' => trim((string) ($config['pages'] ?? '')) !== '',
      'user_role' => !empty($config['roles']),
      'language' => !empty($config['langcodes']),
      'promo_card_min_results' => array_key_exists('minimum', $config) && is_numeric($config['minimum']),
      'promo_card_pager_page' => array_key_exists('max_page', $config) && is_numeric($config['max_page']),
      'promo_card_route_name' => trim((string) ($config['routes'] ?? '')) !== '',
      'promo_card_request_parameter' => trim((string) ($config['parameter'] ?? '')) !== '',
      'promo_card_views_exposed_filter' => trim((string) ($config['filter_id'] ?? '')) !== '',
      default => FALSE,
    };
  }

}
