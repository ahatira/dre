<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Form;

use Drupal\Core\Condition\ConditionManager;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;
use Drupal\views_promo_card\Entity\PromoCardPlacement;
use Drupal\views_promo_card\Entity\PromoCardPlacementInterface;
use Drupal\views_promo_card\Service\PatternRegistry;
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
   * Pattern registry service.
   */
  private PatternRegistry $patternRegistry;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    /** @var static $instance */
    $instance = parent::create($container);
    $instance->conditionManager = $container->get('plugin.manager.condition');
    $instance->patternRegistry = $container->get('views_promo_card.pattern_registry');
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
    $form['#attached']['library'][] = 'ps_theme/framework';
    foreach ($this->patternRegistry->getAllowedPatternLibraries() as $library_id) {
      $form['#attached']['library'][] = $library_id;
    }
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
      '#default_value' => $entity->getDisplayId(),
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
      ],
    ];

    $form['#attached']['drupalSettings']['viewsPromoCard'] = [
      'previewUrl' => Url::fromRoute('views_promo_card.placement_preview')->toString(),
    ];

    return $form;
  }

  /**
   * Builds the compact conditions section.
   */
  private function buildConditionsForm(PromoCardPlacementInterface $entity, FormStateInterface $form_state): array {
    $form = [];

    $form['conditions_logic'] = [
      '#type' => 'radios',
      '#title' => $this->t('Condition logic'),
      '#options' => [
        'and' => $this->t('All conditions must pass'),
        'or' => $this->t('Any condition may pass'),
      ],
      '#default_value' => $entity->getConditionsLogic(),
    ];

    $stored = [];
    foreach ($entity->getConditions() as $item) {
      $stored[(string) ($item['id'] ?? '')] = $item;
    }

    $visible = $this->getVisibleConditions($form_state, $entity);
    $definitions = $this->conditionManager->getDefinitions();

    $form['conditions'] = [
      '#type' => 'details',
      '#title' => $this->t('Conditions'),
      '#open' => TRUE,
    ];

    $add_options = [];
    foreach (self::ALLOWED_CONDITIONS as $condition_id) {
      if (!isset($definitions[$condition_id])) {
        continue;
      }
      if (!in_array($condition_id, $visible, TRUE)) {
        $add_options[$condition_id] = (string) $definitions[$condition_id]['label'];
      }
    }

    $form['conditions']['toolbar'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-conditions-toolbar']],
      '#access' => $add_options !== [],
    ];
    $form['conditions']['toolbar']['add_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Add condition'),
      '#title_display' => 'invisible',
      '#options' => $add_options,
      '#empty_option' => $this->t('- Select condition type -'),
    ];
    $form['conditions']['toolbar']['add'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
      '#submit' => ['::addCondition'],
      '#limit_validation_errors' => [],
      '#ajax' => [
        'callback' => '::rebuildConditionsSection',
        'wrapper' => 'promo-card-conditions-list',
      ],
    ];

    $form['conditions']['list'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'promo-card-conditions-list'],
    ];

    if ($visible === []) {
      $form['conditions']['list']['empty'] = [
        '#markup' => '<p class="promo-card-conditions-empty">' . $this->t('No conditions yet. Add one to restrict when this placement applies.') . '</p>',
      ];
    }

    foreach ($visible as $condition_id) {
      if (!isset($definitions[$condition_id])) {
        continue;
      }
      $configuration = $stored[$condition_id] ?? ['id' => $condition_id];
      /** @var \Drupal\Core\Condition\ConditionInterface $condition */
      $condition = $this->conditionManager->createInstance($condition_id, $configuration);
      $form_state->set(['conditions', $condition_id], $condition);
      $condition_form = $condition->buildConfigurationForm([], $form_state);
      $condition_form['#type'] = 'details';
      $condition_form['#title'] = (string) $definitions[$condition_id]['label'];
      $condition_form['#open'] = isset($stored[$condition_id]);
      $condition_form['#tree'] = TRUE;
      $condition_form['#attributes']['class'][] = 'promo-card-condition-item';
      $this->applyOptionalConditionWrapper(
        $condition_form,
        'layout[editor][conditions][list][condition_' . $condition_id,
        isset($stored[$condition_id]),
      );
      $form['conditions']['list']['condition_' . $condition_id] = $condition_form;
    }

    return $form;
  }

  /**
   * AJAX callback to rebuild the conditions list after adding a condition.
   */
  public function rebuildConditionsSection(array &$form, FormStateInterface $form_state): array {
    return $form['layout']['editor']['conditions']['list'];
  }

  /**
   * Submit handler: adds a condition type to the visible list.
   */
  public function addCondition(array &$form, FormStateInterface $form_state): void {
    $type = (string) $form_state->getValue([
      'layout',
      'editor',
      'conditions',
      'toolbar',
      'add_type',
    ]);
    if ($type === '') {
      return;
    }

    $visible = $this->getVisibleConditions($form_state, $this->entity);
    if (!in_array($type, $visible, TRUE)) {
      $visible[] = $type;
      $form_state->set('visible_conditions', $visible);
    }
    $form_state->setRebuild(TRUE);
  }

  /**
   * Returns condition IDs currently shown on the form.
   *
   * @return list<string>
   *   Visible condition plugin IDs.
   */
  private function getVisibleConditions(FormStateInterface $form_state, PromoCardPlacementInterface $entity): array {
    $visible = $form_state->get('visible_conditions');
    if (!is_array($visible)) {
      $visible = [];
      foreach ($entity->getConditions() as $item) {
        $id = (string) ($item['id'] ?? '');
        if ($id !== '') {
          $visible[] = $id;
        }
      }
      $form_state->set('visible_conditions', $visible);
    }
    return array_values(array_filter($visible, fn(mixed $id): bool => is_string($id) && $id !== ''));
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

    foreach (['label', 'id', 'status', 'weight', 'rotation', 'max_insertions_per_page', 'conditions_logic'] as $key) {
      if (array_key_exists($key, $editor)) {
        $entity->set($key, $editor[$key]);
      }
    }

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

    foreach ($this->getVisibleConditions($form_state, $this->entity) as $condition_id) {
      if (!$this->isConditionEnabled($form_state, $condition_id)) {
        continue;
      }
      $condition = $form_state->get(['conditions', $condition_id]);
      $condition_form = $form['layout']['editor']['conditions']['list']['condition_' . $condition_id] ?? NULL;
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

    $conditions = [];
    foreach ($this->getVisibleConditions($form_state, $entity) as $condition_id) {
      $condition_form = $form['layout']['editor']['conditions']['list']['condition_' . $condition_id] ?? NULL;
      if ($condition_form === NULL || !$this->isConditionEnabled($form_state, $condition_id)) {
        continue;
      }
      $condition = $form_state->get(['conditions', $condition_id]);
      if ($condition === NULL) {
        continue;
      }
      $condition->submitConfigurationForm(
        $condition_form,
        SubformState::createForSubform($condition_form, $form, $form_state),
      );
      $config = $condition->getConfiguration();
      if ($this->conditionIsActive($config)) {
        $conditions[] = $config;
      }
    }
    $entity->set('conditions', $conditions);

    $status = parent::save($form, $form_state);
    $this->messenger()->addStatus($this->t('Placement %label saved.', ['%label' => $entity->label()]));
    $form_state->setRedirectUrl($entity->toUrl('collection'));
    return $status;
  }

  /**
   * Adds an enable checkbox and makes child fields required only when enabled.
   */
  private function applyOptionalConditionWrapper(array &$form, string $parents, bool $enabled_default): void {
    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable this condition'),
      '#default_value' => $enabled_default,
      '#weight' => -100,
    ];
    $input_name = $parents . '][enabled]';
    foreach (Element::children($form) as $key) {
      if ($key === 'enabled') {
        continue;
      }
      if (!empty($form[$key]['#required'])) {
        unset($form[$key]['#required']);
        $form[$key]['#states']['required'] = [
          ':input[name="' . $input_name . '"]' => ['checked' => TRUE],
        ];
      }
    }
  }

  /**
   * Checks whether a condition is enabled on the placement form.
   */
  private function isConditionEnabled(FormStateInterface $form_state, string $condition_id): bool {
    return (bool) $form_state->getValue([
      'layout',
      'editor',
      'conditions',
      'list',
      'condition_' . $condition_id,
      'enabled',
    ]);
  }

  /**
   * Determines whether a condition configuration is actively used.
   */
  private function conditionIsActive(array $config): bool {
    if (!empty($config['negate'])) {
      return TRUE;
    }
    $id = (string) ($config['id'] ?? '');
    return match ($id) {
      'request_path' => trim((string) ($config['pages'] ?? '')) !== '',
      'user_role' => !empty($config['roles']),
      'language' => !empty($config['langcodes']),
      'promo_card_min_results' => isset($config['minimum']),
      'promo_card_pager_page' => isset($config['max_page']),
      'promo_card_route_name' => trim((string) ($config['routes'] ?? '')) !== '',
      'promo_card_request_parameter' => trim((string) ($config['parameter'] ?? '')) !== '',
      'promo_card_views_exposed_filter' => trim((string) ($config['filter_id'] ?? '')) !== '',
      default => FALSE,
    };
  }

}
