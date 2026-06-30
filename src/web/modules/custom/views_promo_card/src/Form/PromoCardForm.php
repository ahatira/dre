<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\views_promo_card\Entity\PromoCardInterface;
use Drupal\views_promo_card\Service\PatternIconHelper;
use Drupal\views_promo_card\Service\PatternRegistry;
use Drupal\views_promo_card\Service\PresetRepository;
use Drupal\views_promo_card\Service\PromoCardPatternFormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for promo card entities (dedicated pattern editor + preview).
 */
final class PromoCardForm extends EntityForm {

  /**
   * Pattern registry service.
   */
  private PatternRegistry $patternRegistry;

  /**
   * Preset repository service.
   */
  private PresetRepository $presetRepository;

  /**
   * Pattern icon helper service.
   */
  private PatternIconHelper $patternIconHelper;

  /**
   * Dedicated pattern form builder.
   */
  private PromoCardPatternFormBuilder $patternFormBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    /** @var static $instance */
    $instance = parent::create($container);
    $instance->patternRegistry = $container->get('views_promo_card.pattern_registry');
    $instance->presetRepository = $container->get('views_promo_card.preset_repository');
    $instance->patternIconHelper = $container->get('views_promo_card.pattern_icon_helper');
    $instance->patternFormBuilder = $container->get('views_promo_card.pattern_form_builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\views_promo_card\Entity\PromoCardInterface $entity */
    $entity = $this->entity;

    $form['#attached']['library'][] = 'views_promo_card/promo_card_admin';
    $form['#attached']['library'][] = 'core/drupal.machine-name';
    $form['#attributes']['class'][] = 'promo-card-admin-form';
    $form['#attributes']['data-preview-url'] = Url::fromRoute('views_promo_card.card_preview')->toString();

    if ($entity->isNew() && !$form_state->get('preset_step_done') && $entity->getPatternId() === '') {
      return $this->buildPresetStep($form, $form_state);
    }

    $form_state->disableCache();

    return $this->buildEditorForm($form, $form_state, $entity);
  }

  /**
   * Builds the preset gallery step for new cards.
   */
  private function buildPresetStep(array $form, FormStateInterface $form_state): array {
    $form['preset_intro'] = [
      '#type' => 'item',
      '#title' => $this->t('Choose a preset'),
      '#markup' => '<p>' . $this->t('Start from a business preset or create a blank card.') . '</p>',
    ];

    $form['presets'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-preset-gallery']],
    ];

    foreach ($this->presetRepository->getAll() as $preset_id => $preset) {
      $form['presets'][$preset_id] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['promo-card-preset-card']],
        'label' => [
          '#type' => 'html_tag',
          '#tag' => 'h3',
          '#value' => (string) ($preset['label'] ?? $preset_id),
        ],
        'description' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => (string) ($preset['description'] ?? ''),
        ],
        'use' => [
          '#type' => 'submit',
          '#value' => $this->t('Use this preset'),
          '#name' => 'preset_' . $preset_id,
          '#submit' => ['::applyPreset'],
          '#limit_validation_errors' => [],
          '#preset_id' => $preset_id,
          '#attributes' => ['class' => ['button', 'button--primary']],
        ],
      ];
    }

    $form['presets']['blank'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-preset-card', 'promo-card-preset-card--blank']],
      'label' => [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#value' => $this->t('Blank card'),
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Choose a pattern and configure props manually.'),
      ],
      'use' => [
        '#type' => 'submit',
        '#value' => $this->t('Start blank'),
        '#name' => 'preset_blank',
        '#submit' => ['::startBlank'],
        '#limit_validation_errors' => [],
        '#attributes' => ['class' => ['button']],
      ],
    ];

    return $form;
  }

  /**
   * Builds the split editor + preview form.
   */
  private function buildEditorForm(array $form, FormStateInterface $form_state, PromoCardInterface $entity): array {
    $allowed_patterns = $this->patternRegistry->getAllowedPatternIds();

    $form['layout'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-admin__layout']],
      '#tree' => TRUE,
    ];

    $form['layout']['editor'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-admin__editor']],
    ];

    if ($allowed_patterns === []) {
      $form['layout']['editor']['no_patterns'] = [
        '#type' => 'item',
        '#markup' => '<p>' . $this->t('No SDC patterns are configured yet. Add allowed pattern IDs in the <a href=":url">module settings</a>.', [
          ':url' => Url::fromRoute('views_promo_card.settings')->toString(),
        ]) . '</p>',
      ];
      return $form;
    }

    $submitted_pattern = $form_state->getValue(['layout', 'editor', 'pattern_id']);
    if (!is_string($submitted_pattern) || $submitted_pattern === '') {
      $user_input = $form_state->getUserInput();
      $submitted_pattern = $user_input['layout']['editor']['pattern_id'] ?? NULL;
    }
    $pattern_id = is_string($submitted_pattern) && $submitted_pattern !== ''
      ? $submitted_pattern
      : $entity->getPatternId();
    if ($pattern_id === '') {
      $pattern_id = $allowed_patterns[0];
    }

    $form['layout']['editor']['identity'] = [
      '#type' => 'details',
      '#title' => $this->t('Card identity'),
      '#open' => TRUE,
      '#attributes' => ['class' => ['promo-card-admin__identity']],
    ];

    $form['layout']['editor']['identity']['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $entity->label(),
      '#required' => TRUE,
    ];

    $form['layout']['editor']['identity']['id'] = [
      '#type' => 'machine_name',
      '#title' => $this->t('Machine-readable name'),
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => [PromoCardInterface::class, 'load'],
        'source' => ['layout', 'editor', 'identity', 'label'],
      ],
      '#disabled' => !$entity->isNew(),
      '#required' => TRUE,
    ];

    $form['layout']['editor']['identity']['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $entity->status(),
    ];

    $form['layout']['editor']['identity']['weight'] = [
      '#type' => 'value',
      '#value' => $entity->get('weight') ?? 0,
    ];

    if ($entity->getPresetId() !== '') {
      $preset = $this->presetRepository->get($entity->getPresetId());
      $preset_label = is_array($preset) ? (string) ($preset['label'] ?? $entity->getPresetId()) : $entity->getPresetId();
      $form['layout']['editor']['identity']['preset_info'] = [
        '#type' => 'item',
        '#title' => $this->t('Preset'),
        '#markup' => $preset_label,
      ];
    }

    $form['layout']['editor']['pattern_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Pattern'),
      '#options' => $this->patternRegistry->getPatternOptions(),
      '#default_value' => $pattern_id,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::updatePatternForm',
        'wrapper' => 'promo-card-pattern-form-wrapper',
        'event' => 'change',
      ],
    ];

    $form['layout']['editor']['pattern_form'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#prefix' => '<div id="promo-card-pattern-form-wrapper">',
      '#suffix' => '</div>',
    ];

    if ($pattern_id !== '' && $this->patternRegistry->isValidPattern($pattern_id)) {
      $last_built_pattern = $form_state->get('promo_card_editor_pattern');
      if (is_string($last_built_pattern) && $last_built_pattern !== $pattern_id) {
        $form_state->unsetValue(['layout', 'editor', 'pattern_form']);
      }
      $form_state->set('promo_card_editor_pattern', $pattern_id);

      $default_ui_patterns = $entity->getUiPatterns();
      $entity_pattern = $entity->getPatternId();
      $stored_pattern = (string) ($default_ui_patterns['component_id'] ?? $entity_pattern);
      if ($pattern_id !== $entity_pattern || $stored_pattern !== $pattern_id) {
        $default_ui_patterns = [
          'component_id' => $pattern_id,
          'variant_id' => NULL,
          'props' => [],
          'slots' => [],
        ];
      }
      elseif (($default_ui_patterns['component_id'] ?? '') !== $pattern_id) {
        $default_ui_patterns['component_id'] = $pattern_id;
      }

      $default_ui_patterns = $this->patternIconHelper->normalizeUiPatterns($default_ui_patterns);
      $this->patternFormBuilder->buildForm(
        $form['layout']['editor']['pattern_form'],
        $pattern_id,
        $default_ui_patterns,
      );
    }

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
        '#value' => $this->t('Live preview'),
      ],
      'caption' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Rendered with front theme styles (ps_theme). Appearance options apply here.'),
        '#attributes' => ['class' => ['promo-card-admin__preview-caption']],
      ],
    ];
    $form['layout']['preview']['context'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['promo-card-admin__preview-context']],
    ];
    $form['layout']['preview']['context']['before'] = [
      '#markup' => '<div class="promo-card-admin__mock-offer">' . $this->t('Offer result (mock)') . '</div>',
    ];
    $form['layout']['preview']['context']['target'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['promo-card-admin__preview-target'],
        'id' => 'promo-card-preview-target',
        'data-preview-empty' => $this->t('Loading preview…'),
      ],
      'empty' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Loading preview…'),
        '#attributes' => ['class' => ['promo-card-admin__preview-empty']],
      ],
    ];
    $form['layout']['preview']['context']['after'] = [
      '#markup' => '<div class="promo-card-admin__mock-offer">' . $this->t('Offer result (mock)') . '</div>',
    ];

    $form['#attached']['drupalSettings']['viewsPromoCard'] = [
      'previewUrl' => Url::fromRoute('views_promo_card.card_preview')->toString(),
    ];

    return $form;
  }

  /**
   * AJAX callback for pattern prop form rebuild.
   */
  public function updatePatternForm(array &$form, FormStateInterface $form_state): array {
    return $form['layout']['editor']['pattern_form'];
  }

  /**
   * Submit handler: applies a preset to a new card.
   */
  public function applyPreset(array &$form, FormStateInterface $form_state): void {
    $trigger = $form_state->getTriggeringElement();
    $preset_id = (string) ($trigger['#preset_id'] ?? '');
    $preset = $this->presetRepository->get($preset_id);
    if ($preset === NULL) {
      return;
    }

    /** @var \Drupal\views_promo_card\Entity\PromoCardInterface $entity */
    $entity = $this->entity;
    $entity->set('preset_id', $preset_id);
    $entity->set('pattern_id', (string) ($preset['pattern_id'] ?? ''));
    $ui_patterns = is_array($preset['ui_patterns'] ?? NULL) ? $preset['ui_patterns'] : [];
    $entity->set('ui_patterns', $this->patternIconHelper->normalizeUiPatterns($ui_patterns));
    if ($entity->label() === '' && !empty($preset['label'])) {
      $entity->set('label', (string) $preset['label']);
    }

    $form_state->set('preset_step_done', TRUE);
    $form_state->set('applied_preset_id', $preset_id);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Submit handler: starts a blank card without preset defaults.
   */
  public function startBlank(array &$form, FormStateInterface $form_state): void {
    $allowed = $this->patternRegistry->getAllowedPatternIds();
    /** @var \Drupal\views_promo_card\Entity\PromoCardInterface $entity */
    $entity = $this->entity;
    if ($allowed !== []) {
      $entity->set('pattern_id', $allowed[0]);
      $entity->set('ui_patterns', [
        'component_id' => $allowed[0],
        'variant_id' => NULL,
        'props' => [],
        'slots' => [],
      ]);
    }
    $form_state->set('preset_step_done', TRUE);
    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state): void {
    if ($entity->isNew() && !$form_state->get('preset_step_done') && $entity->getPatternId() === '') {
      return;
    }
    $editor = $form_state->getValue(['layout', 'editor']);
    if (!is_array($editor)) {
      return;
    }

    if (isset($editor['identity']) && is_array($editor['identity'])) {
      foreach (['label', 'id', 'status', 'weight'] as $key) {
        if (array_key_exists($key, $editor['identity'])) {
          $entity->set($key, $editor['identity'][$key]);
        }
      }
    }

    if (array_key_exists('pattern_id', $editor)) {
      $entity->set('pattern_id', $editor['pattern_id']);
    }

    $pattern_id = (string) ($editor['pattern_id'] ?? $entity->getPatternId());
    $pattern_form = is_array($editor['pattern_form'] ?? NULL) ? $editor['pattern_form'] : [];
    if ($pattern_id !== '') {
      $entity->set('ui_patterns', $this->patternFormBuilder->valuesToUiPatterns($pattern_id, $pattern_form));
    }

    if ($form_state->get('applied_preset_id')) {
      $entity->set('preset_id', (string) $form_state->get('applied_preset_id'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    if ($this->entity->isNew() && !$form_state->get('preset_step_done') && $this->entity->getPatternId() === '') {
      return;
    }
    parent::validateForm($form, $form_state);
    $pattern_id = (string) $form_state->getValue(['layout', 'editor', 'pattern_id']);
    if ($pattern_id !== '' && !$this->patternRegistry->isValidPattern($pattern_id)) {
      $form_state->setErrorByName('layout][editor][pattern_id', $this->t('Select a valid pattern.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $status = parent::save($form, $form_state);
    $this->messenger()->addStatus($this->t('Promo card %label saved.', ['%label' => $this->entity->label()]));
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $status;
  }

}
