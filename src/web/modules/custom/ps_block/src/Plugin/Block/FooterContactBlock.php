<?php

declare(strict_types=1);

namespace Drupal\ps_block\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Stellar footer contact column (phones + email with icons).
 */
#[Block(
  id: 'ps_block_footer_contact',
  admin_label: new TranslatableMarkup('Footer contact (Stellar)'),
  category: new TranslatableMarkup('Property Search'),
)]
final class FooterContactBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'title' => '',
      'phones' => [],
      'email' => '',
      'email_label' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form = parent::blockForm($form, $form_state);
    $config = $this->configuration;

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Heading'),
      '#default_value' => $config['title'],
      '#required' => TRUE,
    ];

    $form['phones'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Phone lines'),
      '#description' => $this->t('One per line: Label|+33…'),
      '#default_value' => $this->formatPhonesForForm($config['phones'] ?? []),
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email address'),
      '#default_value' => $config['email'],
    ];

    $form['email_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email link label'),
      '#default_value' => $config['email_label'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['title'] = trim((string) $form_state->getValue('title'));
    $this->configuration['phones'] = $this->parsePhonesFromForm((string) $form_state->getValue('phones'));
    $this->configuration['email'] = trim((string) $form_state->getValue('email'));
    $this->configuration['email_label'] = trim((string) $form_state->getValue('email_label'));
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $phones = array_values(array_filter(
      $this->configuration['phones'] ?? [],
      static fn (array $phone): bool => ($phone['number'] ?? '') !== '',
    ));

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:footer-contact',
      '#props' => [
        'title' => $this->configuration['title'] ?? '',
        'phones' => $phones,
        'email' => $this->configuration['email'] ?? '',
        'email_label' => $this->configuration['email_label'] ?? '',
      ],
    ];
  }

  /**
   * Formats phone rows for the block configuration textarea.
   *
   * @param array<int, array<string, string>> $phones
   *   Phone rows from block configuration.
   */
  private function formatPhonesForForm(array $phones): string {
    $lines = [];
    foreach ($phones as $phone) {
      $label = trim((string) ($phone['label'] ?? ''));
      $number = trim((string) ($phone['number'] ?? ''));
      if ($number === '') {
        continue;
      }
      $lines[] = $label !== '' ? $label . '|' . $number : $number;
    }
    return implode("\n", $lines);
  }

  /**
   * Parses phone rows from the block configuration textarea.
   *
   * @return array<int, array<string, string>>
   *   Normalized phone rows.
   */
  private function parsePhonesFromForm(string $value): array {
    $phones = [];
    foreach (preg_split('/\R/', $value) ?: [] as $line) {
      $line = trim($line);
      if ($line === '') {
        continue;
      }
      if (str_contains($line, '|')) {
        [$label, $number] = array_map('trim', explode('|', $line, 2));
      }
      else {
        $label = '';
        $number = $line;
      }
      if ($number === '') {
        continue;
      }
      $phones[] = [
        'label' => $label,
        'number' => $number,
      ];
    }
    return $phones;
  }

}
