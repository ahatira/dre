<?php

declare(strict_types=1);

namespace Drupal\ps_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin form for configuring SEO URL slug mappings.
 */
final class SeoUrlMappingsForm extends ConfigFormBase {

  protected function getEditableConfigNames(): array {
    return ['ps_search.seo_url_mappings'];
  }

  public function getFormId(): string {
    return 'ps_search_seo_url_mappings';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_search.seo_url_mappings');

    $form['search_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search page URL slug'),
      '#default_value' => $config->get('search_path') ?? 'find-property',
      '#required' => TRUE,
      '#pattern' => '[a-z0-9][a-z0-9\-]*[a-z0-9]',
      '#description' => $this->t('Public slug for the flexible search page (e.g. find-property). Translatable per language via Configuration translation.'),
      '#field_prefix' => '/',
      '#size' => 40,
    ];

    $form['operation_types'] = [
      '#type' => 'details',
      '#title' => $this->t('Operation type slugs'),
      '#description' => $this->t('URL slugs for each transaction type (e.g. LOC → a-louer).'),
      '#open' => TRUE,
    ];

    foreach ($config->get('operation_types') ?? [] as $value => $slug) {
      $form['operation_types']['op_' . strtolower($value)] = [
        '#type' => 'textfield',
        '#title' => $this->t('Slug for @value', ['@value' => $value]),
        '#default_value' => $slug,
        '#required' => TRUE,
        '#pattern' => '[a-z0-9][a-z0-9\-]*[a-z0-9]',
        '#description' => $this->t('Lowercase letters, digits and hyphens only.'),
        '#field_prefix' => '/',
        '#size' => 30,
        // Store the internal value as a data attribute for submit processing.
        '#attributes' => ['data-internal-value' => $value],
      ];
    }

    $form['asset_types'] = [
      '#type' => 'details',
      '#title' => $this->t('Asset type slugs'),
      '#description' => $this->t('URL slugs for each asset/property type (e.g. BUR → bureau).'),
      '#open' => TRUE,
    ];

    foreach ($config->get('asset_types') ?? [] as $value => $slug) {
      $form['asset_types']['asset_' . strtolower($value)] = [
        '#type' => 'textfield',
        '#title' => $this->t('Slug for @value', ['@value' => $value]),
        '#default_value' => $slug,
        '#required' => TRUE,
        '#pattern' => '[a-z0-9][a-z0-9\-]*[a-z0-9]',
        '#description' => $this->t('Lowercase letters, digits and hyphens only.'),
        '#field_prefix' => '/',
        '#size' => 30,
        '#attributes' => ['data-internal-value' => $value],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->config('ps_search.seo_url_mappings');

    $searchPath = strtolower(trim((string) $form_state->getValue('search_path'), '/'));
    $config->set('search_path', $searchPath);

    $operationTypes = [];
    foreach (array_keys($config->get('operation_types') ?? []) as $value) {
      $fieldName = 'op_' . strtolower($value);
      $slug = trim((string) $form_state->getValue($fieldName));
      $operationTypes[$value] = strtolower($slug);
    }

    $assetTypes = [];
    foreach (array_keys($config->get('asset_types') ?? []) as $value) {
      $fieldName = 'asset_' . strtolower($value);
      $slug = trim((string) $form_state->getValue($fieldName));
      $assetTypes[$value] = strtolower($slug);
    }

    $config
      ->set('operation_types', $operationTypes)
      ->set('asset_types', $assetTypes)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
