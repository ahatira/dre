<?php

declare(strict_types=1);

namespace Drupal\ps_email\Form;

use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_email\Form\Trait\EmailConfigFormTrait;
use Drupal\ps_email\Service\EmailFooterBlockRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Email footer layout and contact data settings.
 */
final class EmailFooterLayoutForm extends ConfigFormBase {

  use EmailConfigFormTrait;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly EmailFooterBlockRegistry $emailFooterBlockRegistry,
    private readonly UuidInterface $uuid,
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
      $container->get('language_manager'),
      $container->get('ps_email.email_footer_block_registry'),
      $container->get('uuid'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_email_footer_layout_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_email.shell', 'ps_email.footer_layout'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $shell = $this->config('ps_email.shell');
    $layout = $this->config('ps_email.footer_layout');
    $pluginLabels = $this->emailFooterBlockRegistry->getLabels();

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Configure footer contact data and the block order for transactional emails.',
      ) . '</p>',
    ];

    $this->addTranslatableIntro($form, $this->languageManager);

    $form['reuse_site_footer'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Reuse site footer social block when fields below are empty'),
      '#default_value' => (bool) ($layout->get('reuse_site_footer') ?? $shell->get('reuse_site_footer')),
    ];

    $form['contact_data'] = [
      '#type' => 'details',
      '#title' => $this->t('Contact data'),
      '#open' => TRUE,
    ];

    $form['contact_data']['footer_address'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Footer address'),
      '#default_value' => (string) $shell->get('footer_address'),
      '#rows' => 2,
    ];

    $form['contact_data']['footer_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer phone (display)'),
      '#default_value' => (string) $shell->get('footer_phone'),
      '#maxlength' => 64,
    ];

    $form['contact_data']['footer_phone_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer phone link (tel: URI)'),
      '#default_value' => (string) $shell->get('footer_phone_link'),
      '#maxlength' => 64,
    ];

    $form['contact_data']['footer_offers_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Footer offers URL'),
      '#default_value' => (string) $shell->get('footer_offers_url'),
    ];

    $form['contact_data']['footer_offers_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer offers label'),
      '#default_value' => (string) $shell->get('footer_offers_label'),
      '#maxlength' => 255,
    ];

    $form['contact_data']['footer_services'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Footer services line'),
      '#default_value' => (string) $shell->get('footer_services'),
      '#maxlength' => 255,
    ];

    $form['contact_data']['legal_markup'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Legal footer markup'),
      '#description' => $this->t('HTML allowed. Keep email-safe tags only (p, a, strong, em).'),
      '#default_value' => (string) $shell->get('legal_markup'),
      '#rows' => 6,
    ];

    // phpcs:ignore DrupalPractice.CodeAnalysis.VariableAnalysis.UnusedVariable -- populated by helper.
    $form['footer_layout'] = [
      '#type' => 'details',
      '#title' => $this->t('Footer layout'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['footer_layout']['footer_components'] = $this->buildComponentsTable(
      $this->t('Dark footer blocks'),
      is_array($layout->get('footer_components')) ? $layout->get('footer_components') : [],
      $pluginLabels,
      TRUE,
    );

    $form['footer_layout']['legal_components'] = $this->buildComponentsTable(
      $this->t('Legal zone blocks'),
      is_array($layout->get('legal_components')) ? $layout->get('legal_components') : [],
      $pluginLabels,
      FALSE,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * Builds a reorderable components table for a footer zone.
   *
   * @param array<int, array<string, mixed>> $components
   * @param array<string, string> $pluginLabels
   *
   * @return array<string, mixed>
   */
  private function buildComponentsTable(string|TranslatableMarkup $title, array $components, array $pluginLabels, bool $withRegion): array {
    $header = [
      $this->t('Block'),
      $this->t('Weight'),
    ];
    if ($withRegion) {
      $header[] = $this->t('Column');
    }

    $element = [
      '#type' => 'table',
      '#title' => $title,
      '#header' => $header,
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'footer-weight',
        ],
      ],
      '#tree' => TRUE,
    ];

    if ($components === []) {
      $components[] = [
        'uuid' => $this->uuid->generate(),
        'plugin' => array_key_first($pluginLabels) ?: 'address',
        'weight' => 0,
        'region' => 'contact',
        'settings' => [],
      ];
    }

    foreach ($components as $delta => $component) {
      $element[$delta]['#attributes']['class'][] = 'draggable';
      $element[$delta]['plugin'] = [
        '#type' => 'select',
        '#title' => $this->t('Block'),
        '#title_display' => 'invisible',
        '#options' => $pluginLabels,
        '#default_value' => (string) ($component['plugin'] ?? ''),
        '#required' => TRUE,
      ];
      $element[$delta]['weight'] = [
        '#type' => 'weight',
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => (int) ($component['weight'] ?? $delta * 10),
        '#delta' => 50,
        '#attributes' => ['class' => ['footer-weight']],
      ];
      if ($withRegion) {
        $element[$delta]['region'] = [
          '#type' => 'select',
          '#title' => $this->t('Column'),
          '#title_display' => 'invisible',
          '#options' => [
            'contact' => $this->t('Contact column'),
            'links' => $this->t('Links column'),
          ],
          '#default_value' => (string) ($component['region'] ?? 'contact'),
        ];
      }
      $element[$delta]['uuid'] = [
        '#type' => 'value',
        '#value' => (string) ($component['uuid'] ?? $this->uuid->generate()),
      ];
      $element[$delta]['settings'] = [
        '#type' => 'value',
        '#value' => is_array($component['settings'] ?? NULL) ? $component['settings'] : [],
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $reuse = (bool) $form_state->getValue('reuse_site_footer');

    $this->config('ps_email.shell')
      ->set('reuse_site_footer', $reuse)
      ->set('footer_address', trim((string) $form_state->getValue(['contact_data', 'footer_address'])))
      ->set('footer_phone', trim((string) $form_state->getValue(['contact_data', 'footer_phone'])))
      ->set('footer_phone_link', trim((string) $form_state->getValue(['contact_data', 'footer_phone_link'])))
      ->set('footer_offers_url', trim((string) $form_state->getValue(['contact_data', 'footer_offers_url'])))
      ->set('footer_offers_label', trim((string) $form_state->getValue(['contact_data', 'footer_offers_label'])))
      ->set('footer_services', trim((string) $form_state->getValue(['contact_data', 'footer_services'])))
      ->set('legal_markup', trim((string) $form_state->getValue(['contact_data', 'legal_markup'])))
      ->save();

    $layoutValues = $form_state->getValue('footer_layout') ?? [];
    $this->config('ps_email.footer_layout')
      ->set('reuse_site_footer', $reuse)
      ->set('footer_components', $this->normalizeComponents($layoutValues['footer_components'] ?? [], TRUE))
      ->set('legal_components', $this->normalizeComponents($layoutValues['legal_components'] ?? [], FALSE))
      ->save();

    parent::submitForm($form, $form_state);
    $this->setSubmitRedirect($form_state, 'ps_email.shell_footer');
  }

  /**
   * @param array<int|string, array<string, mixed>> $rows
   *
   * @return list<array<string, mixed>>
   */
  private function normalizeComponents(array $rows, bool $withRegion): array {
    $components = [];
    foreach ($rows as $row) {
      if (!is_array($row) || empty($row['plugin'])) {
        continue;
      }
      $component = [
        'uuid' => (string) ($row['uuid'] ?? $this->uuid->generate()),
        'plugin' => (string) $row['plugin'],
        'weight' => (int) ($row['weight'] ?? 0),
        'settings' => is_array($row['settings'] ?? NULL) ? $row['settings'] : [],
      ];
      if ($withRegion) {
        $component['region'] = (string) ($row['region'] ?? 'contact');
      }
      if ($component['plugin'] === 'rich_text' && $component['settings'] === []) {
        $component['settings'] = ['markup_key' => 'legal_markup'];
      }
      $components[] = $component;
    }

    usort($components, static fn (array $a, array $b): int => $a['weight'] <=> $b['weight']);
    return $components;
  }

}
