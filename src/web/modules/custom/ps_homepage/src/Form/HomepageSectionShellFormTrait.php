<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Shared form helpers for homepage section header/footer LB blocks.
 */
trait HomepageSectionShellFormTrait {

  use StringTranslationTrait;

  /**
   * @return array<string, mixed>
   */
  protected function buildSectionHeaderForm(array $config): array {
    return [
      '#type' => 'details',
      '#title' => $this->t('Section header'),
      '#open' => TRUE,
      'title' => [
        '#type' => 'textfield',
        '#title' => $this->t('Section title'),
        '#default_value' => $config['title'] ?? '',
        '#maxlength' => 255,
        '#required' => TRUE,
      ],
      'subtitle' => [
        '#type' => 'textarea',
        '#title' => $this->t('Section subtitle'),
        '#default_value' => $config['subtitle'] ?? '',
        '#rows' => 3,
        '#maxlength' => 512,
      ],
      'align' => [
        '#type' => 'select',
        '#title' => $this->t('Alignment'),
        '#options' => [
          'center' => $this->t('Center'),
          'left' => $this->t('Left'),
        ],
        '#default_value' => $config['align'] ?? 'center',
      ],
      'accent' => [
        '#type' => 'select',
        '#title' => $this->t('Accent'),
        '#options' => [
          'bar' => $this->t('Separator bar'),
          'none' => $this->t('None'),
        ],
        '#default_value' => $config['accent'] ?? 'bar',
      ],
    ];
  }

  /**
   * @return array<string, mixed>
   */
  protected function buildSectionFooterForm(array $config): array {
    return [
      '#type' => 'details',
      '#title' => $this->t('Section footer'),
      '#open' => TRUE,
      'cta_label' => [
        '#type' => 'textfield',
        '#title' => $this->t('Button label'),
        '#default_value' => $config['cta_label'] ?? '',
        '#maxlength' => 255,
      ],
      'cta_url' => [
        '#type' => 'textfield',
        '#title' => $this->t('Button URL'),
        '#default_value' => $config['cta_url'] ?? '',
        '#maxlength' => 512,
      ],
      'cta_style' => [
        '#type' => 'select',
        '#title' => $this->t('Button style'),
        '#options' => [
          'outline' => $this->t('Outline'),
          'primary' => $this->t('Primary'),
        ],
        '#default_value' => $config['cta_style'] ?? 'outline',
      ],
    ];
  }

  /**
   * @param array<string, mixed> $configuration
   */
  protected function submitSectionHeaderForm(array &$configuration, FormStateInterface $form_state): void {
    $configuration['title'] = trim((string) $form_state->getValue('title'));
    $configuration['subtitle'] = trim((string) $form_state->getValue('subtitle'));
    $configuration['align'] = (string) $form_state->getValue('align');
    $configuration['accent'] = (string) $form_state->getValue('accent');
  }

  /**
   * @param array<string, mixed> $configuration
   */
  protected function submitSectionFooterForm(array &$configuration, FormStateInterface $form_state): void {
    $configuration['cta_label'] = trim((string) $form_state->getValue('cta_label'));
    $configuration['cta_url'] = trim((string) $form_state->getValue('cta_url'));
    $configuration['cta_style'] = (string) $form_state->getValue('cta_style');
  }

}
