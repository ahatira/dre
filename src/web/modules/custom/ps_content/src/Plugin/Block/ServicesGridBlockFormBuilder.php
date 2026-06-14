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

  private const MIN_VISIBLE_SLOTS = 4;

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
    $items = $this->sortItemsByWeight($config['items'] ?? []);
    $slotCount = $this->computeRepeaterSlotCount(
      $items,
      static fn (array $item): bool => trim((string) ($item['card_title'] ?? '')) !== '',
      self::MAX_ITEMS,
      self::MIN_VISIBLE_SLOTS,
    );

    $form = [
      'editing_language' => $this->buildContentEditingLanguageNotice(),
      'cards_intro' => $this->buildBodyBlockSectionHeaderNotice(),
    ];

    $form['items'] = [
      '#type' => 'container',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['ps-services-grid-form__items']],
    ];

    $form['items']['order'] = $this->buildRepeaterOrderTable(
      $slotCount,
      $items,
      static fn (array $item): string => trim((string) ($item['card_title'] ?? '')),
      'ps-services-grid-weight',
    );

    for ($delta = 0; $delta < $slotCount; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $cardTitle = trim((string) ($item['card_title'] ?? ''));
      $ctaParents = ['items', (string) $delta, 'cta'];

      $form['items'][$delta] = [
        '#type' => 'details',
        '#title' => $cardTitle !== ''
          ? $cardTitle
          : $this->t('Service card @number', ['@number' => $delta + 1]),
        '#open' => $cardTitle !== '' && $delta < 2,
        '#attributes' => ['class' => ['ps-services-grid-form__card']],
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
      $form['items'][$delta]['cta']['button_style'] = $this->buildButtonStyleElement(
        (string) ($item['button_style'] ?? 'primary'),
      ) + [
        '#states' => [
          'visible' => [
            ':input[name="settings[items][' . $delta . '][cta][button_label]"]' => ['!empty' => TRUE],
          ],
        ],
      ];

      $form['items'][$delta]['remove'] = $this->buildRemoveItemCheckbox(
        (string) $this->t('Remove this card'),
      );
    }

    $form['items_help'] = $this->buildRepeaterOrderHelp(self::MAX_ITEMS);

    $form['#attributes']['class'][] = 'ps-services-grid-form';

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
    $weights = $this->extractRepeaterOrderWeights($rows);
    foreach ($rows as $delta => $row) {
      if ($delta === 'order' || !is_array($row) || !empty($row['remove'])) {
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

      $buttonStyle = (string) ($cta['button_style'] ?? 'primary');
      if (!in_array($buttonStyle, ['outline', 'primary'], TRUE)) {
        $buttonStyle = 'primary';
      }

      $items[] = [
        'weight' => $weights[(int) $delta] ?? (int) $delta,
        'icon' => IconIdUtility::extractFromSubmission($content['icon'] ?? NULL, 'bnp_custom:entrusting-a-property'),
        'card_title' => $title,
        'body' => trim((string) ($content['body'] ?? '')),
        'button_label' => trim((string) ($cta['button_label'] ?? '')),
        'link_type' => $linkType,
        'button_style' => $buttonStyle,
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
