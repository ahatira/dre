<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;

/**
 * Block form builder for the homepage expert journey section.
 */
final class ExpertJourneyBlockFormBuilder {

  use HomepageBlockFormTrait;

  private const MAX_STEPS = 8;

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $form = [];

    $form += $this->buildLanguageTabs($config, function (string $langcode, array $config): array {
      $fields = $this->buildHeadingFields($langcode, $config);
      $fields['cta_title_' . $langcode] = [
        '#type' => 'textfield',
        '#title' => $this->t('CTA title'),
        '#default_value' => $config['cta_title_' . $langcode] ?? '',
        '#maxlength' => 255,
      ];
      $fields['cta_body_' . $langcode] = [
        '#type' => 'textarea',
        '#title' => $this->t('CTA body'),
        '#default_value' => $config['cta_body_' . $langcode] ?? '',
        '#rows' => 3,
      ];
      $fields['cta_button_label_' . $langcode] = [
        '#type' => 'textfield',
        '#title' => $this->t('CTA button label'),
        '#default_value' => $config['cta_button_label_' . $langcode] ?? '',
        '#maxlength' => 255,
      ];
      $fields['cta_button_url_' . $langcode] = [
        '#type' => 'textfield',
        '#title' => $this->t('CTA button URL'),
        '#default_value' => $config['cta_button_url_' . $langcode] ?? '',
        '#maxlength' => 512,
        '#states' => [
          'visible' => [
            ':input[name="settings[cta_link_type]"]' => ['!value' => 'offcanvas'],
          ],
        ],
      ];

      return [
        'content_' . $langcode => [
          '#type' => 'details',
          '#title' => $this->t('Content (@language)', ['@language' => strtoupper($langcode)]),
          '#open' => $langcode === 'en',
        ] + $fields,
      ];
    });

    $form['media'] = [
      '#type' => 'details',
      '#title' => $this->t('Image'),
      '#open' => TRUE,
    ];
    $form['media']['image'] = $this->buildManagedFileElement(
      $this->t('Journey image'),
      $config['image'] ?? NULL,
      'public://homepage/expert/',
      TRUE,
    );
    $form['media']['image_alt'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Image alt text'),
      '#default_value' => $config['image_alt'] ?? '',
      '#maxlength' => 255,
    ];

    $form['cta_link_type'] = [
      '#type' => 'select',
      '#title' => $this->t('CTA link type'),
      '#options' => $this->expertCtaLinkTypeOptions(),
      '#default_value' => $config['cta_link_type'] ?? 'url',
    ];
    $form['modal_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Modal ID'),
      '#default_value' => $config['modal_id'] ?? '',
      '#maxlength' => 128,
      '#states' => [
        'visible' => [
          ':input[name="settings[cta_link_type]"]' => ['value' => 'modal'],
        ],
      ],
    ];

    $steps = $this->sortItemsByWeight($config['steps'] ?? []);
    if ($steps === []) {
      $steps = self::defaultSteps();
    }

    $form['steps'] = [
      '#type' => 'table',
      '#header' => [$this->t('Weight'), $this->t('Step (EN)'), $this->t('Step (FR)'), $this->t('Remove')],
      '#tree' => TRUE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'expert-weight',
        ],
      ],
    ];

    for ($delta = 0; $delta < self::MAX_STEPS; $delta++) {
      $item = $steps[$delta] ?? ['weight' => $delta];
      $form['steps'][$delta]['#attributes']['class'][] = 'draggable';
      $form['steps'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#attributes' => ['class' => ['expert-weight']],
      ];
      $form['steps'][$delta]['step_label_en'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['step_label_en'] ?? '',
        '#maxlength' => 255,
      ];
      $form['steps'][$delta]['step_label_fr'] = [
        '#type' => 'textfield',
        '#title_display' => 'invisible',
        '#default_value' => $item['step_label_fr'] ?? '',
        '#maxlength' => 255,
      ];
      $form['steps'][$delta]['remove'] = [
        '#type' => 'checkbox',
        '#title_display' => 'invisible',
        '#return_value' => 1,
      ];
    }

    return $form;
  }

  /**
   * @param array<string, mixed> $config
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    foreach (['en', 'fr'] as $langcode) {
      $group = 'content_' . $langcode;
      $config['title_' . $langcode] = trim((string) $form_state->getValue(['lang_' . $langcode, $group, 'title_' . $langcode]));
      $config['subtitle_' . $langcode] = trim((string) $form_state->getValue(['lang_' . $langcode, $group, 'subtitle_' . $langcode]));
      $config['cta_title_' . $langcode] = trim((string) $form_state->getValue(['lang_' . $langcode, $group, 'cta_title_' . $langcode]));
      $config['cta_body_' . $langcode] = trim((string) $form_state->getValue(['lang_' . $langcode, $group, 'cta_body_' . $langcode]));
      $config['cta_button_label_' . $langcode] = trim((string) $form_state->getValue(['lang_' . $langcode, $group, 'cta_button_label_' . $langcode]));
      $config['cta_button_url_' . $langcode] = trim((string) $form_state->getValue(['lang_' . $langcode, $group, 'cta_button_url_' . $langcode]));
    }

    $config['image'] = $this->persistManagedFile($form_state->getValue(['media', 'image']));
    $config['image_alt'] = trim((string) $form_state->getValue(['media', 'image_alt']));
    $config['cta_link_type'] = (string) $form_state->getValue('cta_link_type');
    $config['modal_id'] = trim((string) $form_state->getValue('modal_id'));

    $rows = $form_state->getValue('steps');
    $steps = [];
    if (is_array($rows)) {
      foreach ($rows as $delta => $row) {
        if (!is_array($row) || !empty($row['remove'])) {
          continue;
        }
        $labelEn = trim((string) ($row['step_label_en'] ?? ''));
        $labelFr = trim((string) ($row['step_label_fr'] ?? ''));
        if ($labelEn === '' && $labelFr === '') {
          continue;
        }
        $steps[] = [
          'weight' => (int) ($row['weight'] ?? $delta),
          'step_label_en' => $labelEn,
          'step_label_fr' => $labelFr,
        ];
      }
    }
    $config['steps'] = $this->sortItemsByWeight($steps);
  }

  /**
   * @return list<array<string, mixed>>
   */
  public static function defaultSteps(): array {
    return [
      ['weight' => 0, 'step_label_en' => 'Define your needs', 'step_label_fr' => 'Définir vos besoins'],
      ['weight' => 1, 'step_label_en' => 'Shortlist opportunities', 'step_label_fr' => 'Présélectionner les opportunités'],
      ['weight' => 2, 'step_label_en' => 'Visit and compare', 'step_label_fr' => 'Visiter et comparer'],
      ['weight' => 3, 'step_label_en' => 'Negotiate and secure', 'step_label_fr' => 'Négocier et sécuriser'],
      ['weight' => 4, 'step_label_en' => 'Move in with confidence', 'step_label_fr' => 'S\'installer sereinement'],
    ];
  }

}
