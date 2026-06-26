<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\SnapshotSyncFieldsFormTrait;
use Drupal\ps_core\ImportGovernance\ImportGovernanceSnapshotEntityKey;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldResolver;
use Drupal\ps_core\Service\ImportGovernanceSnapshotFieldSettings;
use Drupal\ps_feature\Service\FeatureCatalogueGovernance;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configures feature catalogue governance for CRM/XML imports.
 */
final class FeatureCatalogueGovernanceSettingsForm extends ConfigFormBase {

  use SnapshotSyncFieldsFormTrait;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
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
      $container->get('entity_type.manager'),
      $container->get('ps_core.import_governance_global_resolver'),
      $container->get('ps_core.import_governance_snapshot_field_resolver'),
      $container->get('ps_core.import_governance_snapshot_field_settings'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [FeatureCatalogueGovernance::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_feature_catalogue_governance_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config(FeatureCatalogueGovernance::CONFIG_NAME);

    $form['intro'] = [
      '#markup' => $this->globalResolver->buildGlobalPipelineSettingsIntroMarkup(
        $this->t('Control how CRM/XML imports interact with the manually curated feature catalogue.'),
      ),
    ];

    $form['import_defaults'] = [
      '#type' => 'details',
      '#title' => $this->t('Import defaults'),
      '#open' => TRUE,
    ];
    $fallbackLabel = $this->getDefaultImportGroupFallbackLabel();
    $form['import_defaults']['import_default_group'] = [
      '#type' => 'select',
      '#title' => $this->t('Default feature group'),
      '#description' => $this->t(
        'Used when a CSV row, CRM element or offer import creates a feature definition without a group code. Falls back to @group when unset.',
        ['@group' => $fallbackLabel],
      ),
      '#options' => $this->getFeatureGroupOptions(),
      '#empty_option' => $this->t('- @group (fallback) -', ['@group' => $fallbackLabel]),
      '#default_value' => $config->get('import_defaults.default_group') ?: '',
    ];

    $form['crm'] = [
      '#type' => 'details',
      '#title' => $this->t('CRM row import'),
      '#open' => TRUE,
    ];
    $form['crm']['crm_row_strategy_override'] = [
      '#type' => 'select',
      '#title' => $this->t('Protected catalogue row strategy'),
      '#description' => $this->t('Applies to feature groups and definitions during migrate row saves. Inherit uses the global CRM pipeline default.'),
      '#options' => [
        FeatureCatalogueGovernance::STRATEGY_INHERIT => $this->t('Inherit global CRM strategy'),
        FeatureCatalogueGovernance::STRATEGY_SKIP_FIELD => $this->t('Skip protected fields (recommended)'),
        FeatureCatalogueGovernance::STRATEGY_SKIP_ROW => $this->t('Skip protected rows entirely'),
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
            ':input[name="crm_row_strategy_override"]' => ['value' => FeatureCatalogueGovernance::STRATEGY_INHERIT],
          ],
        ],
      ];
    }

    $form['crm']['allow_crm_overwrite_display'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow CRM to overwrite display fields'),
      '#description' => $this->t('When unchecked, existing definitions keep their icon and search filter exposure during CRM imports.'),
      '#default_value' => $config->get('allow_crm_overwrite_display'),
    ];

    $form['missing_from_xml'] = [
      '#type' => 'details',
      '#title' => $this->t('Missing from XML snapshot'),
      '#open' => TRUE,
    ];
    $form['missing_from_xml']['group_action'] = [
      '#type' => 'select',
      '#title' => $this->t('Non-protected groups'),
      '#options' => $this->missingActionOptions(),
      '#default_value' => $config->get('missing_from_xml.group_action'),
    ];
    $form['missing_from_xml']['definition_action'] = [
      '#type' => 'select',
      '#title' => $this->t('Non-protected definitions'),
      '#options' => $this->missingActionOptions(),
      '#default_value' => $config->get('missing_from_xml.definition_action'),
    ];
    $form['missing_from_xml']['protected_definition_action'] = [
      '#type' => 'select',
      '#title' => $this->t('Catalogue-protected definitions'),
      '#description' => $this->t('Catalogue-protected groups are always kept active.'),
      '#options' => $this->missingActionOptions(),
      '#default_value' => $config->get('missing_from_xml.protected_definition_action'),
    ];

    $form['present_in_xml'] = [
      '#type' => 'details',
      '#title' => $this->t('Present in XML snapshot'),
      '#open' => TRUE,
    ];
    $form['present_in_xml']['reactivate'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Reactivate inactive catalogue entries'),
      '#description' => $this->t('When checked, groups and definitions present in the XML snapshot are set back to active.'),
      '#default_value' => $config->get('present_in_xml.reactivate'),
    ];
    $this->appendSnapshotSyncFieldElements($form['present_in_xml'], $config, [
      ImportGovernanceSnapshotEntityKey::encode('fb_feature_definition') => $this->t('Feature definitions'),
      ImportGovernanceSnapshotEntityKey::encode('fb_feature_group') => $this->t('Feature groups'),
    ]);

    $form['offer_values'] = [
      '#type' => 'details',
      '#title' => $this->t('Offer feature values'),
      '#open' => TRUE,
    ];
    $form['offer_values']['missing_definition'] = [
      '#type' => 'select',
      '#title' => $this->t('Missing catalogue definition'),
      '#description' => $this->t('Applies when an offer technical element references a feature definition that is not in the catalogue.'),
      '#options' => [
        FeatureCatalogueGovernance::MISSING_DEFINITION_SKIP_LOG => $this->t('Skip item and log a warning'),
        FeatureCatalogueGovernance::MISSING_DEFINITION_CREATE_STUB => $this->t('Create a stub definition from the XML element'),
      ],
      '#default_value' => $config->get('offer_values.missing_definition'),
    ];
    $form['offer_values']['sync_definition_labels'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Sync translated definition labels from offer imports'),
      '#description' => $this->t('When checked, offer imports may update translated feature definition labels, including for catalogue-protected definitions.'),
      '#default_value' => $config->get('offer_values.sync_definition_labels'),
    ];

    $form['manual'] = [
      '#type' => 'details',
      '#title' => $this->t('Manual catalogue curation'),
      '#open' => FALSE,
    ];
    $form['manual']['bo_create_default_internal_lock'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Lock new BO definitions by default'),
      '#default_value' => $config->get('bo_create.default_internal_lock'),
    ];
    $form['manual']['csv_import_lock_on_import'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Lock definitions imported from CSV'),
      '#default_value' => $config->get('csv_import.lock_on_import'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $groupId = trim((string) $form_state->getValue('import_default_group'));
    if ($groupId !== '' && !$this->entityTypeManager->getStorage('fb_feature_group')->load($groupId)) {
      $form_state->setErrorByName(
        'import_default_group',
        $this->t('The selected default feature group does not exist.'),
      );
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(FeatureCatalogueGovernance::CONFIG_NAME)
      ->set('import_defaults.default_group', trim((string) $form_state->getValue('import_default_group')))
      ->set('crm_row_strategy_override', $form_state->getValue('crm_row_strategy_override'))
      ->set('allow_crm_overwrite_display', (bool) $form_state->getValue('allow_crm_overwrite_display'))
      ->set('missing_from_xml.group_action', $form_state->getValue('group_action'))
      ->set('missing_from_xml.definition_action', $form_state->getValue('definition_action'))
      ->set('missing_from_xml.protected_definition_action', $form_state->getValue('protected_definition_action'))
      ->set('present_in_xml.reactivate', (bool) $form_state->getValue('reactivate'))
      ->set('present_in_xml.sync_fields_by_entity', $this->extractSnapshotSyncFieldValues((array) $form_state->getValue('sync_fields_by_entity')))
      ->set('offer_values.missing_definition', $form_state->getValue('missing_definition'))
      ->set('offer_values.sync_definition_labels', (bool) $form_state->getValue('sync_definition_labels'))
      ->set('bo_create.default_internal_lock', (bool) $form_state->getValue('bo_create_default_internal_lock'))
      ->set('csv_import.lock_on_import', (bool) $form_state->getValue('csv_import_lock_on_import'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getSnapshotFieldResolver(): ImportGovernanceSnapshotFieldResolver {
    return $this->snapshotFieldResolver;
  }

  /**
   * {@inheritdoc}
   */
  protected function getSnapshotFieldSettings(): ImportGovernanceSnapshotFieldSettings {
    return $this->snapshotFieldSettings;
  }

  /**
   * Returns select options for missing-from-XML actions.
   *
   * @return array<string, string>
   *   Select options keyed by action machine name.
   */
  private function missingActionOptions(): array {
    return [
      FeatureCatalogueGovernance::ACTION_DEACTIVATE => $this->t('Deactivate'),
      FeatureCatalogueGovernance::ACTION_KEEP_ACTIVE => $this->t('Keep active'),
    ];
  }

  /**
   * Returns the label of the canonical import fallback feature group.
   */
  private function getDefaultImportGroupFallbackLabel(): string {
    $group = $this->entityTypeManager->getStorage('fb_feature_group')
      ->load(FeatureCatalogueGovernance::DEFAULT_IMPORT_GROUP_ID);
    if ($group !== NULL) {
      return (string) $group->label();
    }

    return (string) $this->t('Additional information');
  }

  /**
   * Returns active feature group options for select elements.
   *
   * @return array<string, string>
   *   Options keyed by group ID.
   */
  private function getFeatureGroupOptions(): array {
    $groups = $this->entityTypeManager->getStorage('fb_feature_group')->loadMultiple();
    uasort($groups, static fn($a, $b) => $a->getWeight() <=> $b->getWeight());

    $options = [];
    foreach ($groups as $group) {
      if (!$group->status()) {
        continue;
      }
      $options[$group->id()] = $group->label();
    }

    return $options;
  }

}
