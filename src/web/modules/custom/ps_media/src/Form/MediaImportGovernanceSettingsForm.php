<?php

declare(strict_types=1);

namespace Drupal\ps_media\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\SnapshotSyncFieldsFormTrait;
use Drupal\ps_core\ImportGovernance\ImportGovernanceSnapshotEntityKey;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldResolver;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldSettings;
use Drupal\ps_media\Service\MediaImportGovernance;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configures media import governance for CRM/XML imports.
 */
final class MediaImportGovernanceSettingsForm extends ConfigFormBase {

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
    return [MediaImportGovernance::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_media_import_governance_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config(MediaImportGovernance::CONFIG_NAME);

    $form['intro'] = [
      '#markup' => $this->globalResolver->buildGlobalPipelineSettingsIntroMarkup(
        $this->t('Control how CRM/XML imports interact with manually curated offer media.'),
      ),
    ];

    $form['crm'] = [
      '#type' => 'details',
      '#title' => $this->t('CRM row import'),
      '#open' => TRUE,
    ];
    $form['crm']['crm_row_strategy_override'] = [
      '#type' => 'select',
      '#title' => $this->t('Protected media row strategy'),
      '#description' => $this->t('Applies to media entities during migrate row saves when field_internal_lock is enabled. Inherit uses the global CRM pipeline default.'),
      '#options' => [
        MediaImportGovernance::STRATEGY_INHERIT => $this->t('Inherit global CRM strategy'),
        MediaImportGovernance::STRATEGY_SKIP_FIELD => $this->t('Skip protected fields (recommended)'),
        MediaImportGovernance::STRATEGY_SKIP_ROW => $this->t('Skip protected rows entirely'),
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
            ':input[name="crm_row_strategy_override"]' => ['value' => MediaImportGovernance::STRATEGY_INHERIT],
          ],
        ],
      ];
    }

    $form['crm']['allow_crm_overwrite_alt'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow CRM to overwrite alt text and link titles'),
      '#description' => $this->t('When unchecked, existing media keeps its image alt text and virtual tour link title during CRM imports.'),
      '#default_value' => $config->get('allow_crm_overwrite_alt'),
    ];

    $form['missing_from_xml'] = [
      '#type' => 'details',
      '#title' => $this->t('Missing from XML snapshot'),
      '#open' => TRUE,
    ];
    $form['missing_from_xml']['media_action'] = [
      '#type' => 'select',
      '#title' => $this->t('Non-protected media'),
      '#options' => $this->missingActionOptions(),
      '#default_value' => $config->get('missing_from_xml.media_action'),
    ];
    $form['missing_from_xml']['protected_media_action'] = [
      '#type' => 'select',
      '#title' => $this->t('Protected media'),
      '#description' => $this->t('Applies when field_internal_lock is enabled on the media entity.'),
      '#options' => $this->missingActionOptions(),
      '#default_value' => $config->get('missing_from_xml.protected_media_action'),
    ];

    $form['present_in_xml'] = [
      '#type' => 'details',
      '#title' => $this->t('Present in XML snapshot'),
      '#open' => TRUE,
    ];
    $form['present_in_xml']['reactivate'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Republish inactive media present in the XML snapshot'),
      '#default_value' => $config->get('present_in_xml.reactivate'),
    ];
    $this->appendSnapshotSyncFieldElements($form['present_in_xml'], $config, [
      ImportGovernanceSnapshotEntityKey::encode('media', 'image') => $this->t('Image media'),
      ImportGovernanceSnapshotEntityKey::encode('media', 'visite_guided') => $this->t('Virtual tour media'),
    ]);

    $form['manual'] = [
      '#type' => 'details',
      '#title' => $this->t('Manual media curation'),
      '#open' => FALSE,
    ];
    $form['manual']['bo_create_default_internal_lock'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Lock new BO media by default'),
      '#description' => $this->t('When checked, media created in the back office start with field_internal_lock enabled.'),
      '#default_value' => $config->get('bo_create.default_internal_lock'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(MediaImportGovernance::CONFIG_NAME)
      ->set('crm_row_strategy_override', $form_state->getValue('crm_row_strategy_override'))
      ->set('allow_crm_overwrite_alt', (bool) $form_state->getValue('allow_crm_overwrite_alt'))
      ->set('missing_from_xml.media_action', $form_state->getValue('media_action'))
      ->set('missing_from_xml.protected_media_action', $form_state->getValue('protected_media_action'))
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
      MediaImportGovernance::ACTION_UNPUBLISH => $this->t('Unpublish'),
      MediaImportGovernance::ACTION_KEEP_PUBLISHED => $this->t('Keep published'),
    ];
  }

}
