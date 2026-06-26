<?php

declare(strict_types=1);

namespace Drupal\ps_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin form for SEO migration 301 redirects (clean-break URL map).
 */
final class SeoRedirectsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_search.seo_redirects'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_search_seo_redirects';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_search.seo_redirects');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t('One-off 301 redirects for legacy search URLs during platform migration. These are separate from contrib Redirect module entries and are not translatable.') . '</p>',
    ];

    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable SEO migration redirects'),
      '#default_value' => (bool) ($config->get('enabled') ?? TRUE),
    ];

    $form['redirects'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Redirect map'),
      '#default_value' => $this->formatRedirects($config->get('redirects') ?? []),
      '#rows' => 12,
      '#description' => $this->t('One redirect per line: /old-path/=/new-path/ (301). Paths are root-relative. Query strings from the incoming request are preserved. Example: /a-louer/bureaux/paris/=/a-louer/bureaux/paris-75/'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_search.seo_redirects')
      ->set('enabled', (bool) $form_state->getValue('enabled'))
      ->set('redirects', $this->parseRedirects((string) $form_state->getValue('redirects')))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Formats redirect map entries for the textarea field.
   *
   * @param array<string, string> $redirects
   *   Configured source to target path map.
   *
   * @return string
   *   One redirect per line as source=target.
   */
  private function formatRedirects(array $redirects): string {
    $lines = [];
    foreach ($redirects as $source => $target) {
      $lines[] = $source . '=' . $target;
    }

    return implode("\n", $lines);
  }

  /**
   * Parses textarea lines into a redirect map.
   *
   * @return array<string, string>
   *   Normalized source to target path map.
   */
  private function parseRedirects(string $raw): array {
    $redirects = [];
    foreach (preg_split('/\R/', $raw) ?: [] as $line) {
      $line = trim($line);
      if ($line === '' || !str_contains($line, '=')) {
        continue;
      }

      [$source, $target] = array_map('trim', explode('=', $line, 2));
      if ($source === '' || $target === '') {
        continue;
      }

      $source = $this->normalizePath($source);
      $target = $this->normalizePath($target);
      if ($source === $target) {
        continue;
      }

      $redirects[$source] = $target;
    }

    return $redirects;
  }

  /**
   * Normalizes a path to root-relative form with trailing slash.
   */
  private function normalizePath(string $path): string {
    $path = trim($path);
    if ($path === '') {
      return '/';
    }

    if (!str_starts_with($path, '/')) {
      $path = '/' . $path;
    }

    return rtrim($path, '/') . '/';
  }

}
