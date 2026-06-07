<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\IconAutocompleteHelperTrait;

/**
 * Configures default feature group icon settings.
 */
final class FeatureSettingsForm extends ConfigFormBase {

  use IconAutocompleteHelperTrait;

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_feature.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_feature_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_feature.settings');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t('Configure the default icon used for feature groups that do not define their own UI Icon.') . '</p>',
    ];

    $form['default_group_icon'] = $this->buildIconPickerElement(
      $this->t('Default feature group icon'),
      $this->getIconDefault($config->get('default_group_icon'), 'bnp_custom:not-available'),
      [
        'required' => TRUE,
        'description' => $this->t('Shown on offer pages when a feature group has no icon configured.'),
      ],
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->configFactory->getEditable('ps_feature.settings')
      ->set(
        'default_group_icon',
        $this->extractIconId($form_state->getValue('default_group_icon'), 'bnp_custom:not-available'),
      )
      ->save();

    parent::submitForm($form, $form_state);
  }

}
