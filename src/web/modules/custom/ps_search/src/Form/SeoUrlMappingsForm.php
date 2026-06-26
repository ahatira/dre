<?php

declare(strict_types=1);

namespace Drupal\ps_search\Form;

use Drupal\Core\Config\Config;
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
    $examples = $this->buildIntroPathExamples($config);

    $form['intro'] = [
      '#markup' => '<p>'
        . $this->t('Configure the search base slug and the operation / asset segments that build SEO filter paths at the site root (they are not nested under the base slug).')
        . '</p><p>'
        . $this->t('Examples with the slugs configured on this form: base page @base; filtered paths @op, @op_asset, @op_asset_locality, @asset, @asset_locality.', $examples)
        . '</p><p>'
        . $this->t('Translatable slugs per language are edited on the Translate tab when additional languages are enabled.')
        . '</p>',
    ];

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

    $form['asset_slug_aliases'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Legacy asset slug aliases'),
      '#default_value' => $this->formatAssetSlugAliases($config->get('asset_slug_aliases') ?? []),
      '#rows' => 4,
      '#description' => $this->t('One alias per line: legacy-slug=canonical-slug (e.g. bureau=bureaux). Used for inbound URL resolution and 301 redirects to the canonical asset slug. Translatable per language via Configuration translation.'),
    ];

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
      ->set('asset_slug_aliases', $this->parseAssetSlugAliases((string) $form_state->getValue('asset_slug_aliases')))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Builds illustrative path examples from the active SEO URL config.
   *
   * @return array<string, string>
   *   Placeholder values for the intro examples string.
   */
  private function buildIntroPathExamples(Config $config): array {
    $searchPath = trim((string) ($config->get('search_path') ?? 'find-property'), '/');
    $operationTypes = $config->get('operation_types') ?? [];
    $assetTypes = $config->get('asset_types') ?? [];

    $opSlug = $operationTypes['LOC'] ?? reset($operationTypes) ?: 'operation';
    $assetSlug = $assetTypes['BUR'] ?? reset($assetTypes) ?: 'asset';
    $localitySlug = 'paris';

    return [
      '@base' => '/' . $searchPath,
      '@op' => '/' . $opSlug,
      '@op_asset' => '/' . $opSlug . '/' . $assetSlug,
      '@op_asset_locality' => '/' . $opSlug . '/' . $assetSlug . '/' . $localitySlug,
      '@asset' => '/' . $assetSlug,
      '@asset_locality' => '/' . $assetSlug . '/' . $localitySlug . '/',
    ];
  }

  /**
   * @param array<string, string> $aliases
   */
  private function formatAssetSlugAliases(array $aliases): string {
    $lines = [];
    foreach ($aliases as $legacy => $canonical) {
      $lines[] = strtolower(trim((string) $legacy)) . '=' . strtolower(trim((string) $canonical));
    }
    return implode("\n", $lines);
  }

  /**
   * @return array<string, string>
   */
  private function parseAssetSlugAliases(string $raw): array {
    $aliases = [];
    foreach (preg_split('/\R/', $raw) ?: [] as $line) {
      $line = trim($line);
      if ($line === '' || !str_contains($line, '=')) {
        continue;
      }
      [$legacy, $canonical] = array_map('trim', explode('=', $line, 2));
      $legacy = strtolower($legacy);
      $canonical = strtolower($canonical);
      if ($legacy !== '' && $canonical !== '') {
        $aliases[$legacy] = $canonical;
      }
    }
    return $aliases;
  }

}
