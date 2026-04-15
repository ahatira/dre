<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\search_api\processor;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferSurfaceDivisionSearchValueResolver;
use Drupal\search_api\Attribute\SearchApiProcessor;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds stable surface/division fields for offer indexing.
 */
#[SearchApiProcessor(
  id: 'ps_offer_surface_division',
  label: new TranslatableMarkup('Offer surface/division fields'),
  description: new TranslatableMarkup('Adds derived main surface, division total surface, and consistency status.'),
  stages: [
    'add_properties' => 0,
  ],
)]
final class OfferSurfaceDivisionProcessor extends ProcessorPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the processor.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly OfferSurfaceDivisionSearchValueResolver $valueResolver,
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
      $container->get('ps_offer.surface_division_search_value_resolver'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index): bool {
    return isset($index->getDatasources()['entity:node']);
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(?DatasourceInterface $datasource = NULL): array {
    if ($datasource) {
      return [];
    }

    $processorId = $this->getPluginId();

    return [
      'ps_offer_surface_main_value' => new ProcessorProperty([
        'label' => $this->t('Offer main surface value'),
        'description' => $this->t('Primary offer surface value.'),
        'type' => 'decimal',
        'processor_id' => $processorId,
      ]),
      'ps_offer_surface_main_unit' => new ProcessorProperty([
        'label' => $this->t('Offer main surface unit'),
        'description' => $this->t('Primary offer surface unit code.'),
        'type' => 'string',
        'processor_id' => $processorId,
      ]),
      'ps_offer_surface_total_divisions' => new ProcessorProperty([
        'label' => $this->t('Offer total divisions surface'),
        'description' => $this->t('Total surface from linked divisions (M2-only safe sum).'),
        'type' => 'decimal',
        'processor_id' => $processorId,
      ]),
      'ps_offer_surface_consistency_status' => new ProcessorProperty([
        'label' => $this->t('Offer surface consistency status'),
        'description' => $this->t('Consistency between main surface and division total (ok/warning/mismatch/unknown).'),
        'type' => 'string',
        'processor_id' => $processorId,
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item): void {
    $entity = $item->getOriginalObject()?->getValue();
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return;
    }

    $resolved = $this->valueResolver->resolve($entity);
    $allFields = $item->getFields(FALSE);

    $mainValueFields = $this->getFieldsHelper()
      ->filterForPropertyPath($allFields, NULL, 'ps_offer_surface_main_value');
    if ($resolved['main_surface_value'] !== NULL) {
      foreach ($mainValueFields as $field) {
        $field->addValue($resolved['main_surface_value']);
      }
    }

    $mainUnitFields = $this->getFieldsHelper()
      ->filterForPropertyPath($allFields, NULL, 'ps_offer_surface_main_unit');
    if ($resolved['main_surface_unit'] !== NULL) {
      foreach ($mainUnitFields as $field) {
        $field->addValue($resolved['main_surface_unit']);
      }
    }

    $divisionsTotalFields = $this->getFieldsHelper()
      ->filterForPropertyPath($allFields, NULL, 'ps_offer_surface_total_divisions');
    if ($resolved['total_surface_divisions'] !== NULL) {
      foreach ($divisionsTotalFields as $field) {
        $field->addValue($resolved['total_surface_divisions']);
      }
    }

    $statusFields = $this->getFieldsHelper()
      ->filterForPropertyPath($allFields, NULL, 'ps_offer_surface_consistency_status');
    foreach ($statusFields as $field) {
      $field->addValue($resolved['surface_consistency_status']);
    }
  }

}
