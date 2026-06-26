<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_dictionary\Service\DictionaryImportGovernance;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configures dictionary import governance for CSV and CRM lookups.
 */
final class DictionaryImportGovernanceSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [DictionaryImportGovernance::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_dictionary_import_governance_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config(DictionaryImportGovernance::CONFIG_NAME);

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Control how CSV imports and CRM offer migrations interact with manually curated dictionary entries.',
      ) . '</p>',
    ];

    $form['csv_import'] = [
      '#type' => 'details',
      '#title' => $this->t('CSV import'),
      '#open' => TRUE,
    ];
    $form['csv_import']['preserve_existing_labels'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Preserve existing entry labels on CSV update'),
      '#description' => $this->t('When checked, CSV imports only update weight for existing entries and keep their current labels.'),
      '#default_value' => $config->get('csv_import.preserve_existing_labels'),
    ];
    $form['csv_import']['lock_on_import'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Lock entries imported from CSV'),
      '#description' => $this->t('Reserved for a future dictionary protection model. Currently has no effect.'),
      '#default_value' => $config->get('csv_import.lock_on_import'),
      '#disabled' => TRUE,
    ];

    $form['crm_lookup'] = [
      '#type' => 'details',
      '#title' => $this->t('CRM offer import lookups'),
      '#open' => TRUE,
    ];
    $form['crm_lookup']['preserve_existing_labels_crm'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Preserve existing dictionary labels during CRM offer import'),
      '#description' => $this->t('When checked, offer imports keep existing dictionary entry labels instead of overwriting them from XML-derived values.'),
      '#default_value' => $config->get('crm_lookup.preserve_existing_labels'),
    ];

    $form['manual'] = [
      '#type' => 'details',
      '#title' => $this->t('Manual dictionary curation'),
      '#open' => FALSE,
    ];
    $form['manual']['bo_create_default_internal_lock'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Lock new BO entries by default'),
      '#description' => $this->t('Reserved for a future dictionary protection model. Currently has no effect.'),
      '#default_value' => $config->get('bo_create.default_internal_lock'),
      '#disabled' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(DictionaryImportGovernance::CONFIG_NAME)
      ->set('csv_import.preserve_existing_labels', (bool) $form_state->getValue('preserve_existing_labels'))
      ->set('crm_lookup.preserve_existing_labels', (bool) $form_state->getValue('preserve_existing_labels_crm'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
