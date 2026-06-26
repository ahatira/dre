<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\SnapshotSyncFieldsFormTrait;
use Drupal\ps_core\ImportGovernance\ImportGovernanceSnapshotEntityKey;
use Drupal\ps_agent\Service\AgentImportGovernance;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldResolver;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configures agent import governance for CRM/XML imports.
 */
final class AgentImportGovernanceSettingsForm extends ConfigFormBase {

  use SnapshotSyncFieldsFormTrait;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly ImportGovernanceGlobalResolver $globalResolver,
    private readonly ImportGovernanceSnapshotFieldResolver $snapshotFieldResolver,
    private readonly ImportGovernanceSnapshotFieldSettings $snapshotFieldSettings,
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
      $container->get('ps_core.import_governance_snapshot_field_resolver'),
      $container->get('ps_core.import_governance_snapshot_field_settings'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [AgentImportGovernance::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_agent_import_governance_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config(AgentImportGovernance::CONFIG_NAME);

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Control how CRM/XML imports interact with manually curated agents. Global CRM lock strategy defaults are configured in the @link.',
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
      '#title' => $this->t('Protected agent row strategy'),
      '#description' => $this->t('Applies to agent entities during migrate row saves when internal_lock is enabled. Inherit uses the global CRM pipeline default.'),
      '#options' => [
        AgentImportGovernance::STRATEGY_INHERIT => $this->t('Inherit global CRM strategy'),
        AgentImportGovernance::STRATEGY_SKIP_FIELD => $this->t('Skip protected fields (recommended)'),
        AgentImportGovernance::STRATEGY_SKIP_ROW => $this->t('Skip protected rows entirely'),
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
            ':input[name="crm_row_strategy_override"]' => ['value' => AgentImportGovernance::STRATEGY_INHERIT],
          ],
        ],
      ];
    }

    $form['crm']['allow_crm_overwrite_contact'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow CRM to overwrite contact fields'),
      '#description' => $this->t('When unchecked, existing agents keep their email and phone during CRM imports.'),
      '#default_value' => $config->get('allow_crm_overwrite_contact'),
    ];

    $form['missing_from_xml'] = [
      '#type' => 'details',
      '#title' => $this->t('Missing from XML snapshot'),
      '#open' => TRUE,
    ];
    $form['missing_from_xml']['agent_action'] = [
      '#type' => 'select',
      '#title' => $this->t('Non-protected agents'),
      '#options' => $this->missingActionOptions(),
      '#default_value' => $config->get('missing_from_xml.agent_action'),
    ];
    $form['missing_from_xml']['protected_agent_action'] = [
      '#type' => 'select',
      '#title' => $this->t('Protected agents'),
      '#description' => $this->t('Applies when internal_lock is enabled on the agent.'),
      '#options' => $this->missingActionOptions(),
      '#default_value' => $config->get('missing_from_xml.protected_agent_action'),
    ];

    $form['present_in_xml'] = [
      '#type' => 'details',
      '#title' => $this->t('Present in XML snapshot'),
      '#open' => TRUE,
    ];
    $form['present_in_xml']['reactivate'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Reactivate inactive agents present in the XML snapshot'),
      '#default_value' => $config->get('present_in_xml.reactivate'),
    ];
    $this->appendSnapshotSyncFieldElements($form['present_in_xml'], $config, [
      ImportGovernanceSnapshotEntityKey::encode(AgentImportGovernance::ENTITY_TYPE_ID) => $this->t('Agents'),
    ]);

    $form['manual'] = [
      '#type' => 'details',
      '#title' => $this->t('Manual agent curation'),
      '#open' => FALSE,
    ];
    $form['manual']['bo_create_default_internal_lock'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Lock new BO agents by default'),
      '#default_value' => $config->get('bo_create.default_internal_lock'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(AgentImportGovernance::CONFIG_NAME)
      ->set('crm_row_strategy_override', $form_state->getValue('crm_row_strategy_override'))
      ->set('allow_crm_overwrite_contact', (bool) $form_state->getValue('allow_crm_overwrite_contact'))
      ->set('missing_from_xml.agent_action', $form_state->getValue('agent_action'))
      ->set('missing_from_xml.protected_agent_action', $form_state->getValue('protected_agent_action'))
      ->set('present_in_xml.reactivate', (bool) $form_state->getValue('reactivate'))
      ->set('present_in_xml.sync_fields_by_entity', $this->extractSnapshotSyncFieldValues((array) $form_state->getValue('sync_fields_by_entity')))
      ->set('bo_create.default_internal_lock', (bool) $form_state->getValue('bo_create_default_internal_lock'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  protected function getSnapshotFieldResolver(): ImportGovernanceSnapshotFieldResolver {
    return $this->snapshotFieldResolver;
  }

  protected function getSnapshotFieldSettings(): ImportGovernanceSnapshotFieldSettings {
    return $this->snapshotFieldSettings;
  }

  /**
   * @return array<string, string>
   */
  private function missingActionOptions(): array {
    return [
      AgentImportGovernance::ACTION_DEACTIVATE => $this->t('Deactivate'),
      AgentImportGovernance::ACTION_KEEP_ACTIVE => $this->t('Keep active'),
    ];
  }

}
