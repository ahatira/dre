<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Ramsey\Uuid\Uuid;

final class OfferReferencePatternForm extends EntityForm {

  private const SEGMENT_TYPES = [
    'literal' => 'Literal',
    'field_map' => 'Field mapping',
    'year_2_digits' => 'Two-digit year',
    'counter' => 'Counter',
  ];

  private const RESOLUTION_MODES = [
    'manual_then_alias' => 'Manual mapping, then alias set, then canonical',
    'alias_then_manual' => 'Alias set, then manual mapping, then canonical',
    'alias_only' => 'Alias set only',
    'canonical' => 'Canonical source value only',
  ];

  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\ps_offer\Entity\OfferReferencePatternInterface $entity */
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
        'exists' => '\Drupal\ps_offer\Entity\OfferReferencePattern::load',
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

    $form['target_bundles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Target bundles'),
      '#options' => ['offer' => $this->t('Offer')],
      '#default_value' => array_combine($entity->getTargetBundles(), $entity->getTargetBundles()),
      '#required' => TRUE,
    ];

    $form['settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Generation settings'),
      '#open' => TRUE,
    ];

    $form['settings']['allow_manual_override'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow manual override'),
      '#default_value' => $entity->allowsManualOverride(),
    ];

    $form['settings']['require_uniqueness'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Require uniqueness'),
      '#default_value' => $entity->requiresUniqueness(),
    ];

    $form['settings']['validate_manual_value_against_pattern'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Validate manual value against pattern'),
      '#default_value' => $entity->validatesManualValueAgainstPattern(),
    ];

    $form['settings']['generate_on_create'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Generate automatically on create'),
      '#default_value' => $entity->generatesOnCreate(),
    ];

    $form['settings']['regenerate_on_source_change'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Regenerate when source values change'),
      '#default_value' => $entity->regeneratesOnSourceChange(),
    ];

    $form['settings']['counter_scope_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Counter scope mode'),
      '#options' => [
        'global' => $this->t('Global'),
        'pattern' => $this->t('Pattern'),
        'prefix' => $this->t('Prefix'),
      ],
      '#default_value' => $entity->getCounterScopeMode(),
    ];

    $segments = $form_state->has('segments') ? $form_state->get('segments') : ($entity->getSegments() ?: []);
    if ($segments === []) {
      $segments = [[
        'uuid' => Uuid::uuid4()->toString(),
        'label' => '',
        'type' => 'literal',
        'weight' => 0,
        'length' => 1,
        'source_field' => '',
        'resolution_mode' => 'manual_then_alias',
        'alias_set_ids' => [],
        'mapping' => [],
        'fallback_value' => '',
        'settings' => [],
      ]];
    }

    if (!$form_state->has('segments')) {
      $form_state->set('segments', $segments);
    }

    $form['segments'] = [
      '#type' => 'details',
      '#title' => $this->t('Segments'),
      '#open' => TRUE,
      '#tree' => TRUE,
      '#description' => $this->t('Segments are evaluated in weight order. Length is enforced by the generator service.'),
    ];

    foreach ($segments as $delta => $segment) {
      $form['segments'][$delta] = $this->buildSegmentElement($delta, $segment);
    }

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $bundles = array_values(array_filter($form_state->getValue('target_bundles', [])));
    if ($bundles === []) {
      $form_state->setErrorByName('target_bundles', $this->t('At least one target bundle is required.'));
    }

    $segments = array_values(array_filter($form_state->getValue('segments', []), static fn ($segment): bool => is_array($segment) && ($segment['type'] ?? '') !== ''));
    if ($segments === []) {
      $form_state->setErrorByName('segments', $this->t('At least one segment is required.'));
      return;
    }

    foreach ($segments as $delta => $segment) {
      $length = (int) ($segment['length'] ?? 0);
      if ($length <= 0) {
        $form_state->setErrorByName('segments][' . $delta . '][length', $this->t('Segment length must be greater than 0.'));
      }

      if (($segment['type'] ?? '') === 'field_map' && trim((string) ($segment['source_field'] ?? '')) === '') {
        $form_state->setErrorByName('segments][' . $delta . '][source_field', $this->t('A source field is required for field mapping segments.'));
      }
    }
  }

  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_offer\Entity\OfferReferencePattern $entity */
    $entity = $this->entity;

    $bundles = array_values(array_filter($form_state->getValue('target_bundles', [])));
    $segments = array_values(array_filter($form_state->getValue('segments', []), static fn ($segment): bool => is_array($segment) && ($segment['type'] ?? '') !== ''));

    $entity->set('target_bundles', $bundles);
    $entity->set('allow_manual_override', (bool) $form_state->getValue('allow_manual_override'));
    $entity->set('require_uniqueness', (bool) $form_state->getValue('require_uniqueness'));
    $entity->set('validate_manual_value_against_pattern', (bool) $form_state->getValue('validate_manual_value_against_pattern'));
    $entity->set('generate_on_create', (bool) $form_state->getValue('generate_on_create'));
    $entity->set('regenerate_on_source_change', (bool) $form_state->getValue('regenerate_on_source_change'));
    $entity->set('counter_scope_mode', (string) $form_state->getValue('counter_scope_mode'));
    $entity->set('segments', $segments);

    $status = parent::save($form, $form_state);
    $this->messenger()->addStatus($this->t('Offer reference pattern %label has been saved.', ['%label' => $entity->label()]));
    $form_state->setRedirectUrl($entity->toUrl('collection'));
    return $status;
  }

  private function buildSegmentElement(int $delta, array $segment): array {
    return [
      '#type' => 'details',
      '#title' => $this->t('Segment @number', ['@number' => $delta + 1]),
      '#open' => FALSE,
      'uuid' => [
        '#type' => 'value',
        '#value' => $segment['uuid'] ?? Uuid::uuid4()->toString(),
      ],
      'label' => [
        '#type' => 'textfield',
        '#title' => $this->t('Label'),
        '#default_value' => $segment['label'] ?? '',
      ],
      'type' => [
        '#type' => 'select',
        '#title' => $this->t('Type'),
        '#options' => $this->translateOptions(self::SEGMENT_TYPES),
        '#default_value' => $segment['type'] ?? '',
        '#required' => TRUE,
      ],
      'weight' => [
        '#type' => 'number',
        '#title' => $this->t('Weight'),
        '#default_value' => $segment['weight'] ?? $delta,
      ],
      'length' => [
        '#type' => 'number',
        '#title' => $this->t('Length'),
        '#default_value' => $segment['length'] ?? 1,
        '#required' => TRUE,
        '#min' => 1,
      ],
      'source_field' => [
        '#type' => 'textfield',
        '#title' => $this->t('Source field'),
        '#default_value' => $segment['source_field'] ?? '',
        '#description' => $this->t('Used by field mapping segments, for example field_operation_type.'),
      ],
      'resolution_mode' => [
        '#type' => 'select',
        '#title' => $this->t('Resolution mode'),
        '#options' => $this->translateOptions(self::RESOLUTION_MODES),
        '#default_value' => $segment['resolution_mode'] ?? 'manual_then_alias',
      ],
      'alias_set_ids' => [
        '#type' => 'textfield',
        '#title' => $this->t('Alias set IDs'),
        '#default_value' => implode(', ', is_array($segment['alias_set_ids'] ?? NULL) ? $segment['alias_set_ids'] : []),
        '#description' => $this->t('Optional comma-separated alias set IDs. Leave empty to use all applicable enabled alias sets.'),
      ],
      'mapping' => [
        '#type' => 'textarea',
        '#title' => $this->t('Mapping'),
        '#default_value' => $this->encodeMapping($segment['mapping'] ?? []),
        '#description' => $this->t('One KEY=VALUE pair per line. Example: RENT=L'),
      ],
      'fallback_value' => [
        '#type' => 'textfield',
        '#title' => $this->t('Fallback value'),
        '#default_value' => $segment['fallback_value'] ?? '',
      ],
      'settings' => [
        '#type' => 'textarea',
        '#title' => $this->t('Settings (JSON)'),
        '#default_value' => !empty($segment['settings']) ? json_encode($segment['settings'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '',
        '#description' => $this->t('Optional JSON settings for advanced segment behavior.'),
      ],
    ];
  }

  private function encodeMapping(array $mapping): string {
    $lines = [];
    foreach ($mapping as $key => $value) {
      $lines[] = $key . '=' . $value;
    }
    return implode(PHP_EOL, $lines);
  }

  private function translateOptions(array $options): array {
    $translated = [];
    foreach ($options as $key => $label) {
      $translated[$key] = $this->t($label);
    }
    return $translated;
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $segments = $form_state->getValue('segments', []);
    foreach ($segments as $delta => $segment) {
      if (!is_array($segment)) {
        continue;
      }

      $segments[$delta]['mapping'] = $this->decodeMapping((string) ($segment['mapping'] ?? ''));
      $segments[$delta]['alias_set_ids'] = $this->decodeList((string) ($segment['alias_set_ids'] ?? ''));
      $segments[$delta]['settings'] = $this->decodeSettings((string) ($segment['settings'] ?? ''));
    }
    $form_state->setValue('segments', $segments);
    parent::submitForm($form, $form_state);
  }

  private function decodeMapping(string $input): array {
    $mapping = [];
    foreach (preg_split('/\r\n|\r|\n/', trim($input)) ?: [] as $line) {
      if ($line === '' || !str_contains($line, '=')) {
        continue;
      }
      [$key, $value] = explode('=', $line, 2);
      $mapping[trim($key)] = trim($value);
    }
    return $mapping;
  }

  private function decodeSettings(string $input): array {
    $input = trim($input);
    if ($input === '') {
      return [];
    }

    $decoded = json_decode($input, TRUE);
    return is_array($decoded) ? $decoded : [];
  }

  private function decodeList(string $input): array {
    $parts = array_map('trim', explode(',', $input));
    return array_values(array_filter($parts, static fn (string $value): bool => $value !== ''));
  }

}