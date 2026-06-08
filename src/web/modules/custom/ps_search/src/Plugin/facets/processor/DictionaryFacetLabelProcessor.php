<?php

declare(strict_types=1);

namespace Drupal\ps_search\Plugin\facets\processor;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\facets\FacetInterface;
use Drupal\facets\Processor\BuildProcessorInterface;
use Drupal\facets\Processor\ProcessorPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Replaces facet raw codes with ps_dictionary entry labels.
 *
 * @FacetsProcessor(
 *   id = "ps_dictionary_label",
 *   label = @Translation("Dictionary label"),
 *   description = @Translation("Display ps_dictionary entry labels for indexed codes."),
 *   stages = {
 *     "build" = 5,
 *   }
 * )
 */
final class DictionaryFacetLabelProcessor extends ProcessorPluginBase implements BuildProcessorInterface, ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'dictionary_type' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state, FacetInterface $facet): array {
    $form['dictionary_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Dictionary type'),
      '#default_value' => $this->getConfiguration()['dictionary_type'],
      '#description' => $this->t('Dictionary type machine name, e.g. asset_type or operation_type.'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet, array $results): array {
    $dictionary_type = (string) ($this->getConfiguration()['dictionary_type'] ?? '');
    if ($dictionary_type === '') {
      return $results;
    }

    $labels = $this->loadLabels($dictionary_type);

    foreach ($results as $result) {
      $code = (string) $result->getRawValue();
      if (isset($labels[$code])) {
        $result->setDisplayValue($labels[$code]);
      }
    }

    return $results;
  }

  /**
   * Loads dictionary labels keyed by business code.
   *
   * @return array<string, string>
   *   Labels keyed by dictionary code.
   */
  private function loadLabels(string $dictionary_type): array {
    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryInterface[] $entries */
    $entries = $storage->loadByProperties(['type' => $dictionary_type]);

    $labels = [];
    foreach ($entries as $entry) {
      $labels[$entry->getCode()] = $entry->label();
    }

    return $labels;
  }

}
