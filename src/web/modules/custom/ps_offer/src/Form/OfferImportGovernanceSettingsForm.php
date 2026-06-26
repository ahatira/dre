<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_offer\Service\OfferImportGovernance;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configures offer import governance for CRM/XML imports.
 */
final class OfferImportGovernanceSettingsForm extends ConfigFormBase {

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly ImportGovernanceGlobalResolver $globalResolver,
  ) {
    parent::__construct($config_factory, $typed_config_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('ps_core.import_governance_global_resolver'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [OfferImportGovernance::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_import_governance_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config(OfferImportGovernance::CONFIG_NAME);

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Control how CRM/XML imports interact with manually curated offers. Global CRM lock strategy defaults are configured in the @link.',
        [
          '@link' => $this->globalResolver->buildGlobalPipelineSettingsLinkMarkup(
            $this->t('CRM import pipeline settings'),
          ),
        ],
      ) . '</p>',
    ];

    $form['crm'] = [
      '#type' => 'details',
      '#title' => $this->t('CRM row import'),
      '#open' => TRUE,
    ];
    $form['crm']['crm_row_strategy_override'] = [
      '#type' => 'select',
      '#title' => $this->t('Protected offer row strategy'),
      '#description' => $this->t('Applies to offer nodes during migrate row saves when field_internal_lock is enabled. Inherit uses the global CRM pipeline default.'),
      '#options' => [
        OfferImportGovernance::STRATEGY_INHERIT => $this->t('Inherit global CRM strategy'),
        OfferImportGovernance::STRATEGY_SKIP_FIELD => $this->t('Skip protected fields (recommended)'),
        OfferImportGovernance::STRATEGY_SKIP_ROW => $this->t('Skip protected rows entirely'),
      ],
      '#default_value' => $config->get('crm_row_strategy_override'),
    ];

    $inheritanceHint = $this->globalResolver->buildInheritanceHintMarkup();
    if ($inheritanceHint !== '') {
      $form['crm']['inheritance_hint'] = [
        '#type' => 'item',
        '#markup' => $inheritanceHint,
        '#states' => [
          'visible' => [
            ':input[name="crm_row_strategy_override"]' => ['value' => OfferImportGovernance::STRATEGY_INHERIT],
          ],
        ],
      ];
    }

    $form['crm']['allow_crm_overwrite_reference'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow CRM to overwrite reference fields'),
      '#description' => $this->t('When unchecked, existing offers keep their reference mode and manual reference during CRM imports.'),
      '#default_value' => $config->get('allow_crm_overwrite_reference'),
    ];

    $form['missing_from_xml'] = [
      '#type' => 'details',
      '#title' => $this->t('Missing from XML snapshot'),
      '#description' => $this->t('Used by post-import synchronization when an offer disappears from the CRM XML snapshot.'),
      '#open' => TRUE,
    ];
    $form['missing_from_xml']['offer_action'] = [
      '#type' => 'select',
      '#title' => $this->t('Non-protected offers'),
      '#options' => $this->missingActionOptions(),
      '#default_value' => $config->get('missing_from_xml.offer_action'),
    ];
    $form['missing_from_xml']['protected_offer_action'] = [
      '#type' => 'select',
      '#title' => $this->t('Protected offers'),
      '#description' => $this->t('Applies when field_internal_lock is enabled on the offer.'),
      '#options' => $this->missingActionOptions(),
      '#default_value' => $config->get('missing_from_xml.protected_offer_action'),
    ];

    $form['present_in_xml'] = [
      '#type' => 'details',
      '#title' => $this->t('Present in XML snapshot'),
      '#open' => TRUE,
    ];
    $form['present_in_xml']['reactivate'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Republish inactive offers present in the XML snapshot'),
      '#default_value' => $config->get('present_in_xml.reactivate'),
    ];

    $form['manual'] = [
      '#type' => 'details',
      '#title' => $this->t('Manual offer curation'),
      '#open' => FALSE,
    ];
    $form['manual']['bo_create_default_internal_lock'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Lock new BO offers by default'),
      '#description' => $this->t('When checked, offers created in the back office start with field_internal_lock enabled.'),
      '#default_value' => $config->get('bo_create.default_internal_lock'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(OfferImportGovernance::CONFIG_NAME)
      ->set('crm_row_strategy_override', $form_state->getValue('crm_row_strategy_override'))
      ->set('allow_crm_overwrite_reference', (bool) $form_state->getValue('allow_crm_overwrite_reference'))
      ->set('missing_from_xml.offer_action', $form_state->getValue('offer_action'))
      ->set('missing_from_xml.protected_offer_action', $form_state->getValue('protected_offer_action'))
      ->set('present_in_xml.reactivate', (bool) $form_state->getValue('reactivate'))
      ->set('bo_create.default_internal_lock', (bool) $form_state->getValue('bo_create_default_internal_lock'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Returns select options for missing-from-XML actions.
   *
   * @return array<string, string>
   *   Select options keyed by action machine name.
   */
  private function missingActionOptions(): array {
    return [
      OfferImportGovernance::ACTION_UNPUBLISH => $this->t('Unpublish'),
      OfferImportGovernance::ACTION_KEEP_PUBLISHED => $this->t('Keep published'),
    ];
  }

}
