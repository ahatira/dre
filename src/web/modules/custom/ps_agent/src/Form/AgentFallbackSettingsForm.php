<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\StringTranslation\TranslatableMarkup;

final class AgentFallbackSettingsForm extends ConfigFormBase {

  public function getFormId(): string {
    return 'ps_agent_fallback_settings_form';
  }

  protected function getEditableConfigNames(): array {
    return ['ps_agent.fallback'];
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_agent.fallback');

    $form['description'] = [
      '#markup' => $this->t('Upload fallback avatar files used when agent avatar is missing or inaccessible. Allowed formats: png jpg jpeg webp svg.'),
    ];

    $default_fid = $this->resolveCurrentFid('default_fid', (int) $config->get('default_fid'), $form_state);
    $form['default_fid'] = $this->buildManagedFileElement(
      new TranslatableMarkup('Default fallback avatar'),
      $default_fid
    );

    $mr_fid = $this->resolveCurrentFid('mr_fid', (int) $config->get('mr_fid'), $form_state);
    $form['mr_fid'] = $this->buildManagedFileElement(
      new TranslatableMarkup('Fallback avatar for MR'),
      $mr_fid
    );

    $mrs_fid = $this->resolveCurrentFid('mrs_fid', (int) $config->get('mrs_fid'), $form_state);
    $form['mrs_fid'] = $this->buildManagedFileElement(
      new TranslatableMarkup('Fallback avatar for MRS'),
      $mrs_fid
    );

    $ms_fid = $this->resolveCurrentFid('ms_fid', (int) $config->get('ms_fid'), $form_state);
    $form['ms_fid'] = $this->buildManagedFileElement(
      new TranslatableMarkup('Fallback avatar for MS'),
      $ms_fid
    );

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->configFactory->getEditable('ps_agent.fallback');

    foreach (['default_fid', 'mr_fid', 'mrs_fid', 'ms_fid'] as $key) {
      $values = $form_state->getValue($key) ?: [];
      $fid = (int) ($values[0] ?? 0);

      if ($fid > 0) {
        $file = File::load($fid);
        if ($file !== NULL) {
          $file->setPermanent();
          $file->save();
        }
      }

      $config->set($key, $fid);
    }

    $config->save();
    parent::submitForm($form, $form_state);
  }

  private function buildManagedFileElement(TranslatableMarkup $title, int $default_fid): array {
    return [
      '#type' => 'managed_file',
      '#title' => $title,
      '#upload_location' => 'public://ps-agent/fallbacks/',
      '#default_value' => $default_fid > 0 ? [$default_fid] : [],
      '#upload_validators' => [
        'FileExtension' => [
          'extensions' => 'png jpg jpeg webp svg',
        ],
      ],
      '#multiple' => FALSE,
      '#description' => $this->t('One file only. Recommended: square image for best result.'),
    ];
  }

  private function resolveCurrentFid(string $field_name, int $configured_fid, FormStateInterface $form_state): int {
    $value = $form_state->getValue($field_name);
    if (is_array($value)) {
      if (isset($value[0]) && (int) $value[0] > 0) {
        return (int) $value[0];
      }

      if (array_key_exists('fids', $value)) {
        $parsed = (int) $value['fids'];
        return $parsed > 0 ? $parsed : 0;
      }

      if ($form_state->hasValue($field_name)) {
        return 0;
      }
    }

    $input = $form_state->getUserInput();
    if (is_array($input) && isset($input[$field_name]) && is_array($input[$field_name])) {
      $fids = trim((string) ($input[$field_name]['fids'] ?? ''));
      if ($fids !== '') {
        $first = (int) strtok($fids, ' ');
        return $first > 0 ? $first : 0;
      }

      return 0;
    }

    return $configured_fid;
  }

}
