<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\ps_offer\Entity\OfferReferenceSegmentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Offer module settings form.
 */
final class OfferSettingsForm extends ConfigFormBase {

  /**
   * Constructs the settings form.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typedConfigManager,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($config_factory, $typedConfigManager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'ps_offer.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_offer.settings');
    $segments = $this->loadConfiguredSegments();

    $form['offer_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Offer Settings'),
      '#description' => $this->t('Configure property offer behavior and display.'),
    ];

    $form['offer_settings']['divisible_default'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Offers are divisible by default'),
      '#description' => $this->t('When checked, new offers will be marked as divisible by default.'),
      '#default_value' => $config->get('divisible_default') ?? FALSE,
    ];

    $form['offer_settings']['auto_publish'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Auto-publish offers on import'),
      '#description' => $this->t('Automatically publish offers when imported from CRM.'),
      '#default_value' => $config->get('auto_publish') ?? FALSE,
    ];

    $form['offer_settings']['results_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Default results per page'),
      '#description' => $this->t('Number of offers to display per page in collections.'),
      '#default_value' => $config->get('results_per_page') ?? 20,
      '#min' => 1,
      '#max' => 100,
    ];

    $form['reference_builder'] = [
      '#type' => 'details',
      '#title' => $this->t('Reference Builder'),
      '#open' => TRUE,
      '#description' => $this->t('Reference rules are managed as configuration entities for better reliability and auditability.'),
    ];

    $form['reference_builder']['total_length'] = [
      '#type' => 'number',
      '#title' => $this->t('Reference total length'),
      '#default_value' => (int) ($config->get('reference_builder.total_length') ?? 12),
      '#min' => 1,
      '#max' => 64,
      '#required' => TRUE,
    ];

    $form['reference_builder']['links'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['container-inline']],
    ];

    $form['reference_builder']['links']['manage_segments'] = [
      '#type' => 'link',
      '#title' => $this->t('Manage reference segments'),
      '#url' => Url::fromRoute('entity.ps_offer_reference_segment.collection'),
      '#attributes' => ['class' => ['button']],
    ];

    $form['reference_builder']['links']['add_segment'] = [
      '#type' => 'link',
      '#title' => $this->t('Add reference segment'),
      '#url' => Url::fromRoute('entity.ps_offer_reference_segment.add_form'),
      '#attributes' => ['class' => ['button', 'button--primary']],
    ];

    $form['reference_builder']['segments_preview'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Label'),
        $this->t('Type'),
        $this->t('Source'),
        $this->t('Length'),
        $this->t('Enabled'),
        $this->t('Weight'),
      ],
      '#empty' => $this->t('No segment configured yet.'),
    ];

    foreach ($segments as $segment) {
      $form['reference_builder']['segments_preview'][] = [
        'label' => ['#plain_text' => $segment->label()],
        'segment_type' => ['#plain_text' => $segment->getSegmentType()],
        'source_field' => ['#plain_text' => $segment->getSourceField()],
        'length' => ['#plain_text' => (string) $segment->getLength()],
        'enabled' => ['#plain_text' => $segment->isEnabled() ? (string) $this->t('Yes') : (string) $this->t('No')],
        'weight' => ['#plain_text' => (string) $segment->getWeight()],
      ];
    }

    $legacy_segments = $config->get('reference_builder.segments');
    if ($segments === [] && is_array($legacy_segments) && $legacy_segments !== []) {
      $form['reference_builder']['legacy_notice'] = [
        '#type' => 'item',
        '#markup' => (string) $this->t('Legacy segment settings were detected. You can migrate them to configuration entities using the button below.'),
      ];

      $form['reference_builder']['migrate_legacy_segments'] = [
        '#type' => 'submit',
        '#value' => $this->t('Migrate legacy segments to configuration entities'),
        '#submit' => ['::migrateLegacySegmentsSubmit'],
        '#limit_validation_errors' => [],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $segments = $this->loadConfiguredSegments();
    if ($segments === []) {
      return;
    }

    $enabled_segments = array_filter($segments, static fn(OfferReferenceSegmentInterface $segment): bool => $segment->isEnabled());
    $enabled_length = array_sum(array_map(static fn(OfferReferenceSegmentInterface $segment): int => $segment->getLength(), $enabled_segments));
    $auto_count = count(array_filter($enabled_segments, static fn(OfferReferenceSegmentInterface $segment): bool => $segment->getSegmentType() === 'auto'));
    $total_length = max(1, (int) $form_state->getValue('total_length'));

    if ($enabled_segments === []) {
      $form_state->setErrorByName('total_length', $this->t('At least one segment must be enabled.'));
    }

    if ($auto_count !== 1) {
      $form_state->setErrorByName('total_length', $this->t('Exactly one enabled auto segment is required.'));
    }

    if ($enabled_length !== $total_length) {
      $form_state->setErrorByName('total_length', $this->t('Total length (@total) must match the sum of enabled segment lengths (@sum).', [
        '@total' => (string) $total_length,
        '@sum' => (string) $enabled_length,
      ]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_offer.settings')
      ->set('divisible_default', $form_state->getValue('divisible_default'))
      ->set('auto_publish', $form_state->getValue('auto_publish'))
      ->set('results_per_page', $form_state->getValue('results_per_page'))
      ->set('reference_builder.total_length', max(1, (int) $form_state->getValue('total_length')))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Submit callback to migrate legacy settings segments to config entities.
   */
  public function migrateLegacySegmentsSubmit(array &$form, FormStateInterface $form_state): void {
    $config = $this->config('ps_offer.settings');
    $legacy_segments = $config->get('reference_builder.segments');
    if (!is_array($legacy_segments) || $legacy_segments === []) {
      $this->messenger()->addWarning($this->t('No legacy segments to migrate.'));
      $form_state->setRebuild(TRUE);
      return;
    }

    $storage = $this->getSegmentStorage();
    if ($storage->loadMultiple() !== []) {
      $this->messenger()->addWarning($this->t('Segments already exist. Migration skipped.'));
      $form_state->setRebuild(TRUE);
      return;
    }

    foreach ($legacy_segments as $delta => $legacy_segment) {
      if (!is_array($legacy_segment)) {
        continue;
      }

      $id = (string) ($legacy_segment['id'] ?? ('segment_' . ($delta + 1)));
      $options = is_array($legacy_segment['options'] ?? NULL) ? $legacy_segment['options'] : [];
      $custom_map = is_array($options['custom_map'] ?? NULL) ? $options['custom_map'] : [];

      $storage->create([
        'id' => $id,
        'label' => (string) ($legacy_segment['label'] ?? ''),
        'enabled' => !empty($legacy_segment['enabled']),
        'weight' => (int) ($legacy_segment['weight'] ?? ($delta * 10)),
        'segment_type' => (string) ($legacy_segment['type'] ?? 'custom'),
        'source_field' => (string) ($legacy_segment['source_field'] ?? ''),
        'length' => max(1, (int) ($legacy_segment['length'] ?? 1)),
        'static_value' => (string) ($options['static_value'] ?? ''),
        'custom_map_text' => $this->customMapToText($custom_map),
        'start_index' => max(1, (int) ($options['start_index'] ?? 1)),
        'date_source_field' => (string) ($options['date_source_field'] ?? 'publish_on'),
        'date_format' => (string) ($options['date_format'] ?? 'YY'),
        'auto_start' => max(1, (int) ($options['auto_start'] ?? 1)),
      ])->save();
    }

    $this->messenger()->addStatus($this->t('Legacy segments migrated successfully.'));
    $form_state->setRebuild(TRUE);
  }

  /**
   * Loads configured segments ordered by weight.
   *
   * @return array<int, \Drupal\ps_offer\Entity\OfferReferenceSegmentInterface>
   *   Ordered list.
   */
  protected function loadConfiguredSegments(): array {
    $segments = $this->getSegmentStorage()->loadMultiple();
    if ($segments === []) {
      return [];
    }

    uasort($segments, static fn(OfferReferenceSegmentInterface $a, OfferReferenceSegmentInterface $b): int => $a->getWeight() <=> $b->getWeight());
    return array_values($segments);
  }

  /**
   * Returns the segment storage.
   */
  protected function getSegmentStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('ps_offer_reference_segment');
  }

  /**
   * Converts a custom map array into textarea text.
   *
   * @param array<string, string> $map
   *   Custom map values.
   */
  protected function customMapToText(array $map): string {
    $lines = [];
    foreach ($map as $key => $value) {
      $lines[] = strtoupper((string) $key) . '=' . strtoupper((string) $value);
    }
    return implode(PHP_EOL, $lines);
  }

}
