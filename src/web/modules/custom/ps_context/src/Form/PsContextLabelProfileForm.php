<?php

declare(strict_types=1);

namespace Drupal\ps_context\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_context\LabelProfileKeys;

/**
 * Add / edit form for ps_context.label_profile config entities.
 */
final class PsContextLabelProfileForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\ps_context\Entity\PsContextLabelProfileInterface $entity */
    $entity = $this->entity;
    $labels = $entity->getLabels();

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $entity->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\\Drupal\\ps_context\\Entity\\PsContextLabelProfile::load',
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
      '#description' => $this->t('Lower weight profiles are merged first. Use higher weight for more specific overrides.'),
      '#size' => 5,
    ];

    $form['context'] = [
      '#type' => 'details',
      '#title' => $this->t('Matching context'),
      '#open' => TRUE,
    ];

    $form['context']['asset_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Asset type'),
      '#default_value' => $entity->getAssetType(),
      '#description' => $this->t('Asset code (BUR, COW, …) or * for any.'),
      '#required' => TRUE,
      '#size' => 10,
    ];

    $form['context']['operation_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Operation type'),
      '#default_value' => $entity->getOperationType(),
      '#description' => $this->t('Operation code (LOC, VEN, …) or * for any.'),
      '#required' => TRUE,
      '#size' => 10,
    ];

    $groups = [
      'hero' => $this->t('Homepage hero'),
      'search' => $this->t('Search filters'),
      'offer' => $this->t('Offer form & display'),
    ];

    foreach ($groups as $prefix => $title) {
      $form[$prefix] = [
        '#type' => 'details',
        '#title' => $title,
        '#open' => $prefix === 'search',
      ];
    }

    foreach (LabelProfileKeys::KEYS as $key => $description) {
      $group = LabelProfileKeys::formGroup($key);
      $form[$group]['labels'][$key] = [
        '#type' => 'textfield',
        '#title' => $key,
        '#description' => $description,
        '#default_value' => $labels[$key] ?? '',
        '#maxlength' => 255,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_context\Entity\PsContextLabelProfileInterface $entity */
    $entity = $this->entity;
    $entity->set('asset_type', strtoupper(trim((string) $form_state->getValue('asset_type'))));
    $entity->set('operation_type', strtoupper(trim((string) $form_state->getValue('operation_type'))));

    $raw_labels = [];
    foreach (array_keys(LabelProfileKeys::KEYS) as $key) {
      $group = LabelProfileKeys::formGroup($key);
      $raw_labels[$key] = $form_state->getValue([$group, 'labels', $key]);
    }
    $labels = [];
    foreach (LabelProfileKeys::KEYS as $key => $_description) {
      $value = trim((string) ($raw_labels[$key] ?? ''));
      if ($value !== '') {
        $labels[$key] = $value;
      }
    }
    $entity->set('labels', $labels);

    $status = parent::save($form, $form_state);
    $this->messenger()->addStatus($this->t('Label profile %label saved.', [
      '%label' => $entity->label(),
    ]));
    $form_state->setRedirectUrl($entity->toUrl('collection'));
    return $status;
  }

}
