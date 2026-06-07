<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\IconAutocompleteHelperTrait;

/**
 * Form handler for the Feature Group add and edit forms.
 */
class FeatureGroupForm extends EntityForm {

  use IconAutocompleteHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\ps_feature\Entity\FeatureGroup $feature_group */
    $feature_group = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Group name'),
      '#maxlength' => 255,
      '#default_value' => $feature_group->label(),
      '#description' => $this->t('The name shown for this feature group (e.g., Amenities, Technical features).'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $feature_group->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ps_feature\Entity\FeatureGroup::load',
      ],
      '#disabled' => !$feature_group->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $feature_group->getDescription(),
      '#description' => $this->t('Optional description documenting this group usage.'),
      '#rows' => 3,
    ];

    $form['icon'] = $this->buildIconPickerElement(
      $this->t('Group icon'),
      $feature_group->getIcon(),
      [
        'description' => $this->t('UI Icon shown next to the group title on offer pages. Leave empty to use the default icon from feature settings.'),
      ],
    );

    $form['asset_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Relevant asset types'),
      '#description' => $this->t('Select the asset types this group applies to. Leave empty for all types.'),
      '#options' => $this->getAssetTypeOptions(),
      '#default_value' => $feature_group->getAssetTypes(),
    ];

    $form['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#description' => $this->t('Display order of the group. Groups with lower weight are shown first.'),
      '#default_value' => $feature_group->getWeight(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Active'),
      '#description' => $this->t('Inactive groups are not shown in forms.'),
      '#default_value' => $feature_group->status(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $asset_types = array_filter($form_state->getValue('asset_types'));
    $form_state->setValue('asset_types', array_values($asset_types));

    $form_state->setValue(
      'icon',
      $this->extractIconId($form_state->getValue('icon'), ''),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_feature\Entity\FeatureGroup $feature_group */
    $feature_group = $this->entity;

    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $feature_group->label()];
    $message = $result == SAVED_NEW
      ? $this->t('Feature group %label has been created.', $message_args)
      : $this->t('Feature group %label has been updated.', $message_args);
    $this->messenger()->addStatus($message);

    $form_state->setRedirectUrl($feature_group->toUrl('collection'));
    return $result;
  }

  /**
   * Gets asset type options.
   *
   * @return array
   *   Array of asset type options.
   */
  protected function getAssetTypeOptions(): array {
    return [
      'BUR' => $this->t('Office'),
      'COW' => $this->t('Coworking'),
      'ENT' => $this->t('Warehouse/Logistics'),
      'ACT' => $this->t('Activity unit'),
      'COM' => $this->t('Retail unit'),
      'TER' => $this->t('Land'),
    ];
  }

}
