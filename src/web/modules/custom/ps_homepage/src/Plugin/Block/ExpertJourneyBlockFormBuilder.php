<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;

/**
 * Block form builder for the homepage expert journey section.
 */
final class ExpertJourneyBlockFormBuilder {

  use HomepageBlockFormTrait;

  private const MAX_STEPS = 8;

  private const MIN_VISIBLE_ROWS = 5;

  /**
   * Builds the expert journey block form.
   *
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $form = [
      '#attributes' => ['class' => ['ps-homepage-expert-steps-form']],
      'editing_language' => $this->buildEditingLanguageNotice(),
      'section_header' => $this->buildSectionHeaderFields($config),
    ];

    $steps = $this->sortItemsByWeight($config['steps'] ?? []);
    $rowCount = min(self::MAX_STEPS, max(count($steps) + 1, self::MIN_VISIBLE_ROWS));

    $form['steps'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['ps-homepage-expert-steps']],
    ];

    for ($delta = 0; $delta < $rowCount; $delta++) {
      $item = $steps[$delta] ?? ['weight' => $delta];
      $stepLabel = trim((string) ($item['step_label'] ?? ''));
      $summary = $stepLabel !== ''
        ? $stepLabel
        : (string) $this->t('Step @number', ['@number' => $delta + 1]);

      $form['steps'][$delta] = [
        '#type' => 'details',
        '#title' => $summary,
        '#open' => $delta === 0,
        '#attributes' => ['class' => ['ps-homepage-step-card']],
        '#tree' => TRUE,
        'weight' => [
          '#type' => 'hidden',
          '#default_value' => (int) ($item['weight'] ?? $delta),
        ],
        'step_label' => [
          '#type' => 'textfield',
          '#title' => $this->t('Step label'),
          '#default_value' => $item['step_label'] ?? '',
          '#maxlength' => 255,
          '#required' => FALSE,
          '#placeholder' => $this->t('e.g. Need definition'),
        ],
        'step_title' => [
          '#type' => 'textfield',
          '#title' => $this->t('Card title'),
          '#default_value' => $item['step_title'] ?? '',
          '#maxlength' => 255,
        ],
        'step_body' => [
          '#type' => 'textarea',
          '#title' => $this->t('Card body'),
          '#default_value' => $item['step_body'] ?? '',
          '#rows' => 4,
        ],
        'image' => $this->buildMediaLibraryElement(
          $this->t('Image'),
          $item['image'] ?? NULL,
          FALSE,
          (string) $this->t('Photo credits are edited on the media item (field Credit). Empty image uses the theme demo fallback.'),
        ),
        'remove' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Remove this step'),
        ],
      ];
    }

    $form['steps_help'] = [
      '#type' => 'item',
      '#markup' => (string) $this->t(
        'Add up to @max steps. A step is saved only when its label is filled. Manage media credits in the <a href=":media">media library</a>.',
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
    $config['title'] = trim((string) $form_state->getValue(['section_header', 'title']));
    $config['subtitle'] = trim((string) $form_state->getValue(['section_header', 'subtitle']));

    $rows = $form_state->getValue('steps');
    $steps = [];
    if (is_array($rows)) {
      foreach ($rows as $delta => $row) {
        if (!is_array($row) || !empty($row['remove'])) {
          continue;
        }
        $label = trim((string) ($row['step_label'] ?? ''));
        if ($label === '') {
          continue;
        }
        $steps[] = [
          'weight' => (int) ($row['weight'] ?? $delta),
          'image' => $this->persistMediaReference($row['image'] ?? NULL),
          'step_label' => $label,
          'step_title' => trim((string) ($row['step_title'] ?? '')),
          'step_body' => trim((string) ($row['step_body'] ?? '')),
        ];
      }
    }
    $config['steps'] = $this->sortItemsByWeight($steps);
  }

}
