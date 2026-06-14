<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_homepage\Form\HomepageBlockFormTrait;
use Drupal\ps_search\Service\SearchPresetOptionsProvider;

/**
 * Block form builder for the homepage services grid.
 */
final class ServicesBlockFormBuilder {

  use HomepageBlockFormTrait;

  private const MAX_ITEMS = 6;

  public function __construct(
    private readonly SearchPresetOptionsProvider $presetOptionsProvider,
  ) {}

  /**
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public function buildForm(array $config): array {
    $form = [
      'editing_language' => $this->buildEditingLanguageNotice(),
      'section_header' => $this->buildSectionHeaderFields($config),
    ];

    $items = $this->sortItemsByWeight($config['items'] ?? []);

    $form['items'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Weight'),
        $this->t('Icon'),
        $this->t('Title'),
        $this->t('Body'),
        $this->t('Button label'),
        $this->t('Link'),
        $this->t('Style'),
        $this->t('Actions'),
      ],
      '#empty' => $this->t('No service cards yet.'),
      '#tree' => TRUE,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'services-weight',
        ],
      ],
      '#description' => $this->t('Up to @max cards. Leave unused rows empty or check Remove. Drag to reorder.', ['@max' => self::MAX_ITEMS]),
    ];

    for ($delta = 0; $delta < self::MAX_ITEMS; $delta++) {
      $item = $items[$delta] ?? ['weight' => $delta];
      $parents = ['items', (string) $delta];

      $form['items'][$delta]['#attributes']['class'][] = 'draggable';
      $form['items'][$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => (int) ($item['weight'] ?? $delta),
        '#attributes' => ['class' => ['services-weight']],
      ];
      $form['items'][$delta]['icon'] = $this->buildIconPickerElement(
        $this->t('Icon'),
        (string) ($item['icon'] ?? 'bnp_custom:offices'),
      );
      $form['items'][$delta]['card_title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#title_display' => 'invisible',
        '#default_value' => $item['card_title'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['body'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Body'),
        '#title_display' => 'invisible',
        '#default_value' => $item['body'] ?? '',
        '#rows' => 2,
      ];
      $form['items'][$delta]['button_label'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Button label'),
        '#title_display' => 'invisible',
        '#default_value' => $item['button_label'] ?? '',
        '#maxlength' => 255,
      ];
      $form['items'][$delta]['link_type'] = $this->buildLinkTypeElement(
        'link_type',
        $item['link_type'] ?? 'search_preset',
        $this->serviceLinkTypeOptions(),
      ) + ['#title_display' => 'invisible'];
      $form['items'][$delta]['button_style'] = [
        '#type' => 'select',
        '#title' => $this->t('Style'),
        '#title_display' => 'invisible',
        '#options' => [
          'outline' => (string) $this->t('Outline'),
          'primary' => (string) $this->t('Primary'),
        ],
        '#default_value' => $item['button_style'] ?? 'outline',
      ];
      $form['items'][$delta] += $this->buildPresetFields($parents, $item, $this->presetOptionsProvider);
      $form['items'][$delta]['button_url'] = [
        '#type' => 'textfield',
        '#title' => $this->t('URL'),
        '#title_display' => 'invisible',
        '#default_value' => $item['button_url'] ?? '',
        '#maxlength' => 512,
        '#states' => [
          'visible' => [
            $this->buildStateSelector($parents, 'link_type') => ['value' => 'url'],
          ],
        ],
      ];
      $form['items'][$delta]['modal_id'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Modal ID'),
        '#title_display' => 'invisible',
        '#default_value' => $item['modal_id'] ?? '',
        '#maxlength' => 128,
        '#states' => [
          'visible' => [
            $this->buildStateSelector($parents, 'link_type') => ['value' => 'modal'],
          ],
        ],
      ];
      $form['items'][$delta]['remove'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Remove'),
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
    $config['title'] = trim((string) $form_state->getValue(['section_header', 'title']));
    $config['subtitle'] = trim((string) $form_state->getValue(['section_header', 'subtitle']));

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

      $title = trim((string) ($row['card_title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $items[] = [
        'weight' => (int) ($row['weight'] ?? $delta),
        'icon' => IconIdUtility::extractFromSubmission($row['icon'] ?? NULL, 'bnp_custom:offices'),
        'card_title' => $title,
        'body' => trim((string) ($row['body'] ?? '')),
        'button_label' => trim((string) ($row['button_label'] ?? '')),
        'link_type' => (string) ($row['link_type'] ?? 'url'),
        'button_style' => (string) ($row['button_style'] ?? 'outline'),
        'preset_operation' => (string) ($row['preset_operation'] ?? ''),
        'preset_asset' => (string) ($row['preset_asset'] ?? ''),
        'preset_locality' => trim((string) ($row['preset_locality'] ?? '')),
        'button_url' => trim((string) ($row['button_url'] ?? '')),
        'modal_id' => trim((string) ($row['modal_id'] ?? '')),
      ];
    }

    $config['items'] = $this->sortItemsByWeight($items);
  }

}
