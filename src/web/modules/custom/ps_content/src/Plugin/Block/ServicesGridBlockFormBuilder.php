<?php

declare(strict_types=1);

namespace Drupal\ps_content\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_content\Form\ContentBlockFormTrait;
use Drupal\ps_core\Utility\IconIdUtility;

/**
 * Block form builder for the services grid block.
 */
final class ServicesGridBlockFormBuilder {

  use ContentBlockFormTrait;

  private const MAX_ITEMS = 6;

  /**
   * Builds the block configuration form.
   *
   * @param array<string, mixed> $config
   *   Block configuration.
   *
   * @return array<string, mixed>
   *   Form elements.
   */
  public function buildForm(array $config): array {
    $form = [
      'editing_language' => $this->buildContentEditingLanguageNotice(),
      'cards_intro' => [
        '#type' => 'item',
        '#markup' => '<p>' . $this->t('Section title and subtitle are configured in the <strong>Section header</strong> block above this body block in Layout Builder.') . '</p>',
        '#wrapper_attributes' => ['class' => ['messages', 'messages--info', 'ps-section-block-lang-notice']],
      ],
    ];

    $items = $this->sortItemsByWeight($config['items'] ?? []);

    $form['items'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['ps-services-grid-form__items']],
    ];

    for ($delta = 0; $delta < self::MAX_ITEMS; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $cardTitle = trim((string) ($item['card_title'] ?? ''));
      $ctaParents = ['items', (string) $delta, 'cta'];

      $form['items'][$delta] = [
        '#type' => 'details',
        '#title' => $cardTitle !== ''
          ? $cardTitle
          : $this->t('Service card @number', ['@number' => $delta + 1]),
        '#open' => $delta < 4,
        '#attributes' => ['class' => ['ps-services-grid-form__card']],
      ];

      $form['items'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#delta' => self::MAX_ITEMS - 1,
      ];

      $form['items'][$delta]['content'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Card content'),
        '#description' => $this->t('Icon, title and short description shown on the card.'),
      ];
      $form['items'][$delta]['content']['icon'] = $this->buildIconPickerElement(
        $this->t('Icon'),
        (string) ($item['icon'] ?? 'bnp_custom:entrusting-a-property'),
      );
      $form['items'][$delta]['content']['card_title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#default_value' => $item['card_title'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['content']['body'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Description'),
        '#default_value' => $item['body'] ?? '',
        '#rows' => 4,
        '#description' => $this->t('One or two sentences describing the service line.'),
      ];

      $form['items'][$delta]['cta'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Call to action'),
        '#description' => $this->t('Button label and destination at the bottom of the card.'),
      ];
      $form['items'][$delta]['cta']['button_label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Button label'),
        '#default_value' => $item['button_label'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['cta']['link_type'] = $this->buildLinkTypeElement(
        'link_type',
        $item['link_type'] ?? 'url',
        $this->servicesGridLinkTypeOptions(),
      );
      $form['items'][$delta]['cta']['button_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL'),
        '#default_value' => $item['button_url'] ?? '',
        '#maxlength' => 512,
        '#description' => $this->t('Internal path (e.g. /find-property) or absolute URL.'),
        '#states' => [
          'visible' => [
            $this->buildStateSelector($ctaParents, 'link_type') => ['value' => 'url'],
          ],
        ],
      ];
      $form['items'][$delta]['cta']['button_style'] = [
        '#type' => 'select',
        '#title' => $this->t('Button style'),
        '#options' => [
          'primary' => (string) $this->t('Primary (filled)'),
          'outline' => (string) $this->t('Outline'),
        ],
        '#default_value' => $item['button_style'] ?? 'primary',
      ];

      $form['items'][$delta]['remove'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Remove this card'),
        '#return_value' => 1,
      ];
    }

    $form['items_help'] = [
      '#type' => 'item',
      '#markup' => '<p>' . $this->t('Configure up to @max service lines. Cards are sorted by weight (lowest first).', ['@max' => self::MAX_ITEMS]) . '</p>',
    ];

    return $form;
  }

  /**
   * Persists block configuration from form values.
   *
   * @param array<string, mixed> $config
   *   Block configuration to update.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function submitForm(array &$config, FormStateInterface $form_state): void {
    unset($config['title'], $config['subtitle']);

    $rows = $form_state->getValue('items');
    if (!is_array($rows)) {
      $config['items'] = [];
      return;
    }

    $items = [];
    foreach ($rows as $delta => $row) {
      if (!is_array($row) || !empty($row['remove'])) {
        continue;
      }

      $content = is_array($row['content'] ?? NULL) ? $row['content'] : [];
      $cta = is_array($row['cta'] ?? NULL) ? $row['cta'] : [];

      $title = trim((string) ($content['card_title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $linkType = (string) ($cta['link_type'] ?? 'url');
      if (!in_array($linkType, ['url', 'offcanvas'], TRUE)) {
        $linkType = 'url';
      }

      $items[] = [
        'weight' => (int) ($row['weight'] ?? $delta),
        'icon' => IconIdUtility::extractFromSubmission($content['icon'] ?? NULL, 'bnp_custom:entrusting-a-property'),
        'card_title' => $title,
        'body' => trim((string) ($content['body'] ?? '')),
        'button_label' => trim((string) ($cta['button_label'] ?? '')),
        'link_type' => $linkType,
        'button_style' => (string) ($cta['button_style'] ?? 'primary'),
        'button_url' => trim((string) ($cta['button_url'] ?? '')),
        'modal_id' => '',
        'preset_operation' => '',
        'preset_asset' => '',
        'preset_locality' => '',
      ];
    }

    $config['items'] = $this->sortItemsByWeight($items);
  }

  /**
   * @return array<string, string>
   */
  private function servicesGridLinkTypeOptions(): array {
    return [
      'url' => (string) $this->t('URL'),
      'offcanvas' => (string) $this->t('Contact offcanvas'),
    ];
  }

}
