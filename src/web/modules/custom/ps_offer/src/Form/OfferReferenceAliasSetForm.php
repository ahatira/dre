<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

final class OfferReferenceAliasSetForm extends EntityForm {

  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\ps_offer\Entity\OfferReferenceAliasSetInterface $entity */
    $entity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $entity->label(),
      '#maxlength' => 255,
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ps_offer\Entity\OfferReferenceAliasSet::load',
      ],
      '#disabled' => !$entity->isNew(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $entity->status(),
    ];

    $form['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#default_value' => $entity->getWeight(),
    ];

    $form['applies_to_pattern_ids'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Pattern IDs'),
      '#default_value' => implode(', ', $entity->getAppliesToPatternIds()),
      '#description' => $this->t('Comma-separated pattern IDs. Leave empty to apply to all patterns.'),
    ];

    $form['entries'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Alias entries'),
      '#default_value' => $this->encodeEntries($entity->getEntries()),
      '#description' => $this->t('One alias per line: source_field|source_value|reference_code|weight|description. Example: field_asset_type|ENT|ENT|0|Warehouse alias'),
      '#rows' => 12,
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);
    foreach ($this->decodeEntries((string) $form_state->getValue('entries', '')) as $entry) {
      if (($entry['source_field'] ?? '') === '' || ($entry['source_value'] ?? '') === '' || ($entry['reference_code'] ?? '') === '') {
        $form_state->setErrorByName('entries', $this->t('Each alias entry must define source field, source value, and reference code.'));
        break;
      }
    }
  }

  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_offer\Entity\OfferReferenceAliasSet $entity */
    $entity = $this->entity;
    $entity->set('applies_to_pattern_ids', $this->decodePatternIds((string) $form_state->getValue('applies_to_pattern_ids', '')));
    $entity->set('entries', $this->decodeEntries((string) $form_state->getValue('entries', '')));

    $status = parent::save($form, $form_state);
    $this->messenger()->addStatus($this->t('Offer reference alias set %label has been saved.', ['%label' => $entity->label()]));
    $form_state->setRedirectUrl($entity->toUrl('collection'));
    return $status;
  }

  private function encodeEntries(array $entries): string {
    $lines = [];
    foreach ($entries as $entry) {
      $lines[] = implode('|', [
        (string) ($entry['source_field'] ?? ''),
        (string) ($entry['source_value'] ?? ''),
        (string) ($entry['reference_code'] ?? ''),
        (string) ($entry['weight'] ?? 0),
        str_replace(["\r", "\n", '|'], [' ', ' ', '/'], (string) ($entry['description'] ?? '')),
      ]);
    }
    return implode(PHP_EOL, $lines);
  }

  private function decodePatternIds(string $input): array {
    $parts = array_map('trim', explode(',', $input));
    return array_values(array_filter($parts, static fn (string $value): bool => $value !== ''));
  }

  private function decodeEntries(string $input): array {
    $entries = [];
    $lines = preg_split('/\r\n|\r|\n/', trim($input)) ?: [];
    foreach ($lines as $index => $line) {
      if (trim($line) === '') {
        continue;
      }
      $parts = array_map('trim', explode('|', $line, 5));
      $entries[] = [
        'source_field' => $parts[0] ?? '',
        'source_value' => $parts[1] ?? '',
        'reference_code' => $parts[2] ?? '',
        'weight' => isset($parts[3]) && $parts[3] !== '' ? (int) $parts[3] : $index,
        'description' => $parts[4] ?? '',
      ];
    }
    return $entries;
  }

}