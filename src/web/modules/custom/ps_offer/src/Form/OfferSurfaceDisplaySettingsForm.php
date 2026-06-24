<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Surface KPI display labels on offer cards and detail pages.
 */
final class OfferSurfaceDisplaySettingsForm extends ConfigFormBase {

  use OfferSettingsFormTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_surface_display_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_offer.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->offerSettingsConfig();

    $form['intro'] = [
      '#markup' => '<p>' . $this->t('Labels and templates used when rendering offer surface KPIs.') . '</p>',
    ];

    $form['surface_divisible_template'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Divisible surface template'),
      '#description' => $this->t('Use @surface as placeholder for the minimum lot (e.g. "Divisible from @surface").'),
      '#default_value' => $config->get('surface_divisible_template') ?? '',
      '#required' => TRUE,
    ];

    $form['surface_capacity_unit'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Capacity unit label'),
      '#description' => $this->t('Used for coworking offers (e.g. seats, postes).'),
      '#default_value' => $config->get('surface_capacity_unit') ?? '',
      '#required' => TRUE,
    ];

    $form['surface_kpi_separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Surface KPI separator'),
      '#description' => $this->t('Separator between total surface and divisibility suffix.'),
      '#default_value' => $config->get('surface_kpi_separator') ?? '',
      '#required' => TRUE,
      '#size' => 10,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->saveOfferSettingsKeys([
      'surface_divisible_template',
      'surface_capacity_unit',
      'surface_kpi_separator',
    ], $form_state);
    parent::submitForm($form, $form_state);
  }

}
