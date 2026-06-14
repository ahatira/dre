<?php

declare(strict_types=1);

namespace Drupal\ps_content\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ps_content\Form\ContentBlockFormTrait;

/**
 * Block form builder for the expert journey block.
 */
final class ExpertsAccompagnementBlockFormBuilder {

  use ContentBlockFormTrait;

  private const MAX_STEPS = 8;

  private const MIN_VISIBLE_SLOTS = 5;

  /**
   * Builds the expert journey block form.
   *
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $steps = $this->sortItemsByWeight($config['steps'] ?? []);
    $slotCount = $this->computeRepeaterSlotCount(
      $steps,
      static fn (array $item): bool => trim((string) ($item['step_label'] ?? '')) !== '',
      self::MAX_STEPS,
      self::MIN_VISIBLE_SLOTS,
    );

    $form = [
      '#attributes' => ['class' => ['ps-homepage-expert-steps-form']],
      'editing_language' => $this->buildContentEditingLanguageNotice(),
      'steps_intro' => $this->buildBodyBlockSectionHeaderNotice(),
    ];

    $form['steps'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['ps-homepage-expert-steps']],
    ];

    $form['steps']['order'] = $this->buildRepeaterOrderTable(
      $slotCount,
      $steps,
      static fn (array $item): string => trim((string) ($item['step_label'] ?? '')),
      'ps-expert-steps-weight',
    );

    for ($delta = 0; $delta < $slotCount; $delta++) {
      $item = $steps[$delta] ?? ['weight' => $delta];
      $stepLabel = trim((string) ($item['step_label'] ?? ''));
      $summary = $stepLabel !== ''
        ? $stepLabel
        : (string) $this->t('Step @number', ['@number' => $delta + 1]);

      $form['steps'][$delta] = [
        '#type' => 'details',
        '#title' => $summary,
        '#open' => $stepLabel !== '' && $delta === 0,
        '#attributes' => ['class' => ['ps-homepage-step-card']],
        '#tree' => TRUE,
        'content' => [
          '#type' => 'fieldset',
          '#title' => $this->t('Step content'),
        ],
      ];

      $form['steps'][$delta]['content']['step_label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Step label'),
        '#default_value' => $item['step_label'] ?? '',
        '#maxlength' => 255,
        '#placeholder' => $this->t('e.g. Need definition'),
        '#description' => $this->t('Short label shown in the step navigation.'),
      ];
      $form['steps'][$delta]['content']['step_title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Card title'),
        '#default_value' => $item['step_title'] ?? '',
        '#maxlength' => 255,
      ];
      $form['steps'][$delta]['content']['step_body'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Card body'),
        '#default_value' => $item['step_body'] ?? '',
        '#rows' => 4,
      ];

      $form['steps'][$delta]['image'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Image'),
      ];
      $form['steps'][$delta]['image']['media'] = $this->buildMediaLibraryElement(
        $this->t('Photo'),
        $item['image'] ?? NULL,
        FALSE,
        (string) $this->t('Photo credits are edited on the media item (field Credit). Empty image uses the theme demo fallback.'),
      );

      $form['steps'][$delta]['remove'] = $this->buildRemoveItemCheckbox(
        (string) $this->t('Remove this step'),
      );
    }

    $form['steps_help'] = [
      '#type' => 'item',
      '#markup' => (string) $this->t(
        'Drag rows to reorder steps (up to @max). Expand each step below to edit. A step is saved only when its label is filled. Manage media credits in the <a href=":media">media library</a>.',
        [
          '@max' => self::MAX_STEPS,
          ':media' => Url::fromRoute('entity.media.collection')->toString(),
        ],
      ),
    ];

    return $form;
  }

  /**
   * Persists expert journey block form values.
   *
   * @param array<string, mixed> $config
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    unset($config['title'], $config['subtitle']);

    $rows = $form_state->getValue('steps');
    $steps = [];
    $weights = is_array($rows) ? $this->extractRepeaterOrderWeights($rows) : [];
    if (is_array($rows)) {
      foreach ($rows as $delta => $row) {
        if ($delta === 'order' || !is_array($row) || !empty($row['remove'])) {
          continue;
        }

        $content = is_array($row['content'] ?? NULL) ? $row['content'] : [];
        $image = is_array($row['image'] ?? NULL) ? $row['image'] : [];

        $label = trim((string) ($content['step_label'] ?? ''));
        if ($label === '') {
          continue;
        }

        $steps[] = [
          'weight' => $weights[(int) $delta] ?? (int) $delta,
          'image' => $this->persistMediaReference($image['media'] ?? ($row['image'] ?? NULL)),
          'step_label' => $label,
          'step_title' => trim((string) ($content['step_title'] ?? '')),
          'step_body' => trim((string) ($content['step_body'] ?? '')),
        ];
      }
    }
    $config['steps'] = $this->sortItemsByWeight($steps);
  }

}
