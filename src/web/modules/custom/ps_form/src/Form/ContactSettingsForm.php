<?php

declare(strict_types=1);

namespace Drupal\ps_form\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\ps_form\Service\ContactDisplayModeManager;
use Drupal\ps_form\Service\ContactNeedRouter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Site-wide contact hub, display mode, and urgency contact settings.
 */
final class ContactSettingsForm extends ConfigFormBase {

  /**
   * Tabledrag weight group for hub webform rows.
   */
  private const HUB_WEBFORM_WEIGHT_GROUP = 'ps-contact-hub-webform-weight';

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly ContactDisplayModeManager $displayModeManager,
    private readonly ContactNeedRouter $contactNeedRouter,
    private readonly LanguageManagerInterface $languageManager,
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
      $container->get('ps_form.contact_display_mode'),
      $container->get('ps_form.contact_need_router'),
      $container->get('language_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_form_contact_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_form.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_form.settings');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Configure the contact hub (webforms shown, display mode) and the urgency phone block in webform panels. Changes apply immediately without redeploy.',
      ) . '</p>',
    ];

    if (count($this->languageManager->getLanguages()) > 1) {
      $default_langcode = $this->languageManager
        ->getDefaultLanguage(LanguageInterface::TYPE_CONTENT)
        ->getId();
      $form['intro_multilingual'] = [
        '#markup' => '<p><em>' . $this->t(
          'Urgency contact wording is translatable. Edit default language (@lang) here; use the Translate tab for other languages.',
          ['@lang' => $default_langcode],
        ) . '</em></p>',
      ];
    }

    $form['display'] = [
      '#type' => 'details',
      '#title' => $this->t('Display mode'),
      '#open' => TRUE,
    ];

    $form['display']['contact_display_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Contact display mode'),
      '#options' => $this->displayModeManager->getModeLabels(),
      '#default_value' => $this->displayModeManager->getMode(),
      '#required' => TRUE,
    ];

    $form['display']['contact_modal_dialog_options'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Modal dialog options (JSON)'),
      '#description' => $this->t('Drupal AJAX modal options. Example keys: width, height, dialogClass.'),
      '#default_value' => (string) ($config->get('contact_modal_dialog_options') ?: $this->displayModeManager->getDefaultModalDialogOptionsJson()),
      '#rows' => 6,
      '#states' => [
        'visible' => [
          ':input[name="contact_display_mode"]' => ['value' => ContactDisplayModeManager::MODE_MODAL],
        ],
        'required' => [
          ':input[name="contact_display_mode"]' => ['value' => ContactDisplayModeManager::MODE_MODAL],
        ],
      ],
    ];

    $enabledWebforms = array_fill_keys($this->contactNeedRouter->getConfiguredEnabledHubWebformIds(), TRUE);
    $definitions = $this->contactNeedRouter->getAllManagedWebforms();
    $tableOrder = $this->contactNeedRouter->getAdminWebformTableOrder();
    $rowCount = count($tableOrder);

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

      $form['hub']['webforms'][$webformId]['id'] = [
        '#plain_text' => $webformId,
      ];

      $form['hub']['webforms'][$webformId]['title'] = [
        '#plain_text' => $definition['title'],
      ];

      $form['hub']['webforms'][$webformId]['path'] = [
        '#plain_text' => $definition['path'] ?? '—',
      ];

      $form['hub']['webforms'][$webformId]['hub'] = $this->contactNeedRouter->isHubToggleableWebform($webformId)
        ? [
          '#type' => 'checkbox',
          '#title' => $this->t('Include @title on contact hub', ['@title' => $definition['title']]),
          '#title_display' => 'invisible',
          '#default_value' => isset($enabledWebforms[$webformId]),
          '#wrapper_attributes' => ['class' => ['form-item--hub']],
        ]
        : [
          '#plain_text' => '—',
        ];

      $form['hub']['webforms'][$webformId]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => $weight,
        '#delta' => max(10, $rowCount + 5),
        '#attributes' => [
          'class' => [self::HUB_WEBFORM_WEIGHT_GROUP, 'visually-hidden'],
        ],
        '#wrapper_attributes' => [
          'class' => ['tabledrag-cell'],
        ],
      ];

      $form['hub']['webforms'][$webformId]['opened'] = [
        '#plain_text' => $definition['opened']
          ? $this->t('Open')
          : $this->t('Closed'),
      ];

      $form['hub']['webforms'][$webformId]['actions'] = [
        '#type' => 'link',
        '#title' => $this->t('Edit'),
        '#url' => Url::fromRoute('entity.webform.edit_form', ['webform' => $webformId]),
      ];
    }

    $urgency_states = [
      'visible' => [
        ':input[name="urgency_help_enabled"]' => ['checked' => TRUE],
      ],
    ];

    $form['urgency'] = [
      '#type' => 'details',
      '#title' => $this->t('Urgency contact'),
      '#description' => $this->t('Phone and opening hours shown below webform actions (contact hub, search alert, share, etc.).'),
      '#open' => TRUE,
    ];

    $form['urgency']['urgency_help_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display urgency help block'),
      '#default_value' => (bool) ($config->get('urgency_help_enabled') ?? TRUE),
    ];

    $form['urgency']['urgency_help_lead'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Lead text'),
      '#description' => $this->t('Text before the phone number, e.g. “In a hurry? Call us at”.'),
      '#default_value' => (string) ($config->get('urgency_help_lead') ?? 'In a hurry? Call us at'),
      '#maxlength' => 255,
      '#states' => $urgency_states,
    ];

    $form['urgency']['urgency_help_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone number (display)'),
      '#default_value' => (string) ($config->get('urgency_help_phone') ?? ''),
      '#maxlength' => 64,
      '#states' => $urgency_states + [
        'required' => [
          ':input[name="urgency_help_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['urgency']['urgency_help_phone_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone link (tel: URI)'),
      '#description' => $this->t('Optional. Leave empty to derive from the display number (FR numbers starting with 0).'),
      '#default_value' => (string) ($config->get('urgency_help_phone_link') ?? ''),
      '#maxlength' => 64,
      '#states' => $urgency_states,
    ];

    $form['urgency']['urgency_help_hours'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opening hours'),
      '#default_value' => (string) ($config->get('urgency_help_hours') ?? ''),
      '#maxlength' => 255,
      '#states' => $urgency_states,
    ];

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
      $form_state->setErrorByName(
        'hub][webforms',
        $this->t('Enable at least one webform for the contact hub.'),
      );
    }
    else {
      $openEnabled = array_filter(
        $enabledWebforms,
        fn (string $webformId): bool => $this->contactNeedRouter->isHubVisibleWebform($webformId),
      );
      if ($openEnabled === []) {
        $form_state->setErrorByName(
          'hub][webforms',
          $this->t('At least one enabled webform must be open to appear on the contact hub.'),
        );
      }
    }

    if ($form_state->getValue('contact_display_mode') !== ContactDisplayModeManager::MODE_MODAL) {
      return;
    }

    $raw = trim((string) $form_state->getValue('contact_modal_dialog_options'));
    if ($raw === '') {
      $form_state->setErrorByName('contact_modal_dialog_options', $this->t('Modal dialog options are required when modal mode is selected.'));
      return;
    }

    try {
      $decoded = json_decode($raw, TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException $exception) {
      $form_state->setErrorByName('contact_modal_dialog_options', $this->t('Invalid JSON: @message', [
        '@message' => $exception->getMessage(),
      ]));
      return;
    }

    if (!is_array($decoded)) {
      $form_state->setErrorByName('contact_modal_dialog_options', $this->t('Modal dialog options must be a JSON object.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $mode = (string) $form_state->getValue('contact_display_mode');
    $modalOptions = trim((string) $form_state->getValue('contact_modal_dialog_options'));

    $hubToggleable = $this->buildHubToggleableLookup();
    $enabledWebforms = $this->extractEnabledHubWebformsFromTable(
      $this->getHubWebformTableValues($form_state),
      $hubToggleable,
    );
    $this->configFactory->getEditable('ps_form.settings')
      ->set('contact_display_mode', $mode)
      ->set('contact_modal_dialog_options', $mode === ContactDisplayModeManager::MODE_MODAL ? $modalOptions : '')
      ->set('contact_hub_enabled_webforms', $enabledWebforms)
      ->set('urgency_help_enabled', (bool) $form_state->getValue('urgency_help_enabled'))
      ->set('urgency_help_lead', trim((string) $form_state->getValue('urgency_help_lead')))
      ->set('urgency_help_phone', trim((string) $form_state->getValue('urgency_help_phone')))
      ->set('urgency_help_phone_link', trim((string) $form_state->getValue('urgency_help_phone_link')))
      ->set('urgency_help_hours', trim((string) $form_state->getValue('urgency_help_hours')))
      ->save();

    parent::submitForm($form, $form_state);
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
