<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ps_form\Form\Trait\ContactConfigFormTrait;
use Drupal\ps_form\Service\ContactNeedRouter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Contact hub webform enablement and ordering settings.
 */
final class ContactHubSettingsForm extends ConfigFormBase {

  use ContactConfigFormTrait;

  private const HUB_WEBFORM_WEIGHT_GROUP = 'ps-contact-hub-webform-weight';

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly ContactNeedRouter $contactNeedRouter,
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
      $container->get('ps_form.contact_need_router'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_form_contact_hub_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $enabledWebforms = array_fill_keys($this->contactNeedRouter->getConfiguredEnabledHubWebformIds(), TRUE);
    $definitions = $this->contactNeedRouter->getAllManagedWebforms();
    $tableOrder = $this->contactNeedRouter->getAdminWebformTableOrder();
    $rowCount = count($tableOrder);

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Choose which direct-access webforms appear on <code>/form/contact</code> and drag rows to set their order.',
      ) . '</p>',
    ];

    $form['hub'] = [
      '#type' => 'details',
      '#title' => $this->t('Contact hub webforms'),
      '#description' => $this->t('Drag rows using the handle in the <em>ID</em> column. Check <em>Hub</em> to include a webform on <code>/form/contact</code>. Paths are derived from the webform id (<code>/form/{id}</code>) unless a dedicated URL exists. <em>Status</em> reflects the webform status.'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['hub']['webforms'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('ID'),
        $this->t('Title'),
        $this->t('Path'),
        $this->t('Hub'),
        $this->t('Weight'),
        $this->t('Status'),
        $this->t('Actions'),
      ],
      '#empty' => $this->t('No webforms found.'),
      '#tree' => TRUE,
      '#attributes' => ['class' => ['ps-contact-hub-webforms-table']],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => self::HUB_WEBFORM_WEIGHT_GROUP,
          'hidden' => FALSE,
        ],
      ],
    ];

    foreach ($tableOrder as $weight => $webformId) {
      $definition = $definitions[$webformId] ?? NULL;
      if ($definition === NULL) {
        continue;
      }

      $form['hub']['webforms'][$webformId] = [
        '#attributes' => [
          'class' => ['draggable', 'ps-contact-hub-webforms-table__row'],
          'data-webform-id' => $webformId,
        ],
        '#weight' => $weight,
      ];

      $form['hub']['webforms'][$webformId]['id'] = ['#plain_text' => $webformId];
      $form['hub']['webforms'][$webformId]['title'] = ['#plain_text' => $definition['title']];
      $form['hub']['webforms'][$webformId]['path'] = ['#plain_text' => $definition['path'] ?? '—'];

      $form['hub']['webforms'][$webformId]['hub'] = $this->contactNeedRouter->isHubToggleableWebform($webformId)
        ? [
          '#type' => 'checkbox',
          '#title' => $this->t('Include @title on contact hub', ['@title' => $definition['title']]),
          '#title_display' => 'invisible',
          '#default_value' => isset($enabledWebforms[$webformId]),
          '#wrapper_attributes' => ['class' => ['form-item--hub']],
        ]
        : ['#plain_text' => '—'];

      $form['hub']['webforms'][$webformId]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => $weight,
        '#delta' => max(10, $rowCount + 5),
        '#attributes' => ['class' => [self::HUB_WEBFORM_WEIGHT_GROUP, 'visually-hidden']],
        '#wrapper_attributes' => ['class' => ['tabledrag-cell']],
      ];

      $form['hub']['webforms'][$webformId]['opened'] = [
        '#plain_text' => $definition['opened'] ? $this->t('Open') : $this->t('Closed'),
      ];

      $form['hub']['webforms'][$webformId]['actions'] = [
        '#type' => 'link',
        '#title' => $this->t('Edit'),
        '#url' => Url::fromRoute('entity.webform.edit_form', ['webform' => $webformId]),
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $hubToggleable = $this->buildHubToggleableLookup();
    $enabledWebforms = $this->extractEnabledHubWebformsFromTable(
      $this->getHubWebformTableValues($form_state),
      $hubToggleable,
    );
    if ($enabledWebforms === []) {
      $form_state->setErrorByName('hub][webforms', $this->t('Enable at least one webform for the contact hub.'));
      return;
    }

    $openEnabled = array_filter(
      $enabledWebforms,
      fn (string $webformId): bool => $this->contactNeedRouter->isHubVisibleWebform($webformId),
    );
    if ($openEnabled === []) {
      $form_state->setErrorByName('hub][webforms', $this->t('At least one enabled webform must be open to appear on the contact hub.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $enabledWebforms = $this->extractEnabledHubWebformsFromTable(
      $this->getHubWebformTableValues($form_state),
      $this->buildHubToggleableLookup(),
    );

    $this->configFactory->getEditable('ps_form.settings')
      ->set('contact_hub_enabled_webforms', $enabledWebforms)
      ->save();

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_form.hub_settings');
  }

  /**
   * Reads submitted hub webform table values.
   *
   * @return array<string, array<string, mixed>>
   *   Table rows keyed by webform id.
   */
  private function getHubWebformTableValues(FormStateInterface $form_state): array {
    $hub = $form_state->getValue('hub');
    if (!is_array($hub) || !isset($hub['webforms']) || !is_array($hub['webforms'])) {
      return [];
    }

    return $hub['webforms'];
  }

  /**
   * Extracts enabled hub webforms from the drag-and-drop table (weight order).
   *
   * @param array<string, array<string, mixed>> $tableRows
   *   Raw table values keyed by webform id.
   * @param array<string, true> $hubToggleable
   *   Webform ids that can be enabled on the hub.
   *
   * @return list<string>
   *   Enabled hub webform ids in weight order.
   */
  private function extractEnabledHubWebformsFromTable(array $tableRows, array $hubToggleable): array {
    $rows = [];
    foreach ($tableRows as $webformId => $row) {
      if (!is_string($webformId) || !is_array($row)) {
        continue;
      }

      $rows[] = [
        'webform' => $webformId,
        'weight' => (int) ($row['weight'] ?? 0),
        'hub' => !empty($row['hub']),
      ];
    }

    usort($rows, static fn (array $a, array $b): int => $a['weight'] <=> $b['weight']);

    $enabledWebforms = [];
    foreach ($rows as $row) {
      if (!isset($hubToggleable[$row['webform']]) || !$row['hub']) {
        continue;
      }
      $enabledWebforms[] = $row['webform'];
    }

    return $enabledWebforms;
  }

  /**
   * Builds a lookup of webform ids that can be toggled on the contact hub.
   *
   * @return array<string, true>
   *   Toggleable webform ids.
   */
  private function buildHubToggleableLookup(): array {
    $lookup = [];
    foreach (array_keys($this->contactNeedRouter->getAllManagedWebforms()) as $webformId) {
      if ($this->contactNeedRouter->isHubToggleableWebform($webformId)) {
        $lookup[$webformId] = TRUE;
      }
    }

    return $lookup;
  }

}
