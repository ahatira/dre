<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\search_api\processor;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferExtraFiltersSearchValueResolver;
use Drupal\search_api\Attribute\SearchApiProcessor;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds additional fields used by the Sprint B filters in Search API.
 */
#[SearchApiProcessor(
  id: 'ps_offer_extra_filters',
  label: new TranslatableMarkup('Offer extra filters fields'),
  description: new TranslatableMarkup('Adds media toggles and feature-based fields for advanced offer filters.'),
  stages: [
    'add_properties' => 0,
  ],
)]
final class OfferExtraFiltersProcessor extends ProcessorPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the processor.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly OfferExtraFiltersSearchValueResolver $valueResolver,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $resolver = $container->has('ps_offer.extra_filters_search_value_resolver')
      ? $container->get('ps_offer.extra_filters_search_value_resolver')
      : new OfferExtraFiltersSearchValueResolver($container->get('ps_features.manager'));

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $resolver,
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
      'ps_offer_has_virtual_tour' => new ProcessorProperty([
        'label' => $this->t('Offer has immersive tour'),
        'description' => $this->t('Whether the offer has at least one immersive tour media.'),
        'type' => 'boolean',
        'processor_id' => $processorId,
      ]),
      'ps_offer_has_video' => new ProcessorProperty([
        'label' => $this->t('Offer has video'),
        'description' => $this->t('Whether the offer has at least one video media.'),
        'type' => 'boolean',
        'processor_id' => $processorId,
      ]),
      'ps_offer_feature_accessibility' => new ProcessorProperty([
        'label' => $this->t('Offer accessibility features'),
        'description' => $this->t('Feature labels mapped to accessibility filters.'),
        'type' => 'string',
        'processor_id' => $processorId,
      ]),
      'ps_offer_feature_equipments' => new ProcessorProperty([
        'label' => $this->t('Offer equipments features'),
        'description' => $this->t('Feature labels from the equipments group.'),
        'type' => 'string',
        'processor_id' => $processorId,
      ]),
      'ps_offer_feature_services' => new ProcessorProperty([
        'label' => $this->t('Offer services features'),
        'description' => $this->t('Feature labels from the services group.'),
        'type' => 'string',
        'processor_id' => $processorId,
      ]),
      'ps_offer_building_condition' => new ProcessorProperty([
        'label' => $this->t('Offer building conditions'),
        'description' => $this->t('Building/premises condition values.'),
        'type' => 'string',
        'processor_id' => $processorId,
      ]),
      'ps_offer_transport_text' => new ProcessorProperty([
        'label' => $this->t('Offer nearby transport text'),
        'description' => $this->t('Transport-related searchable text.'),
        'type' => 'text',
        'processor_id' => $processorId,
      ]),
      'ps_offer_ceiling_height_min' => new ProcessorProperty([
        'label' => $this->t('Offer ceiling height minimum'),
        'description' => $this->t('Minimum ceiling height resolved from feature values.'),
        'type' => 'decimal',
        'processor_id' => $processorId,
      ]),
      'ps_offer_ceiling_height_max' => new ProcessorProperty([
        'label' => $this->t('Offer ceiling height maximum'),
        'description' => $this->t('Maximum ceiling height resolved from feature values.'),
        'type' => 'decimal',
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

    $virtualTourFields = $this->getFieldsHelper()->filterForPropertyPath($allFields, NULL, 'ps_offer_has_virtual_tour');
    foreach ($virtualTourFields as $field) {
      $field->addValue($resolved['has_virtual_tour'] ? 1 : 0);
    }

    $videoFields = $this->getFieldsHelper()->filterForPropertyPath($allFields, NULL, 'ps_offer_has_video');
    foreach ($videoFields as $field) {
      $field->addValue($resolved['has_video'] ? 1 : 0);
    }

    $this->addStringListValues($allFields, 'ps_offer_feature_accessibility', $resolved['accessibility']);
    $this->addStringListValues($allFields, 'ps_offer_feature_equipments', $resolved['equipments']);
    $this->addStringListValues($allFields, 'ps_offer_feature_services', $resolved['services']);
    $this->addStringListValues($allFields, 'ps_offer_building_condition', $resolved['building_conditions']);

    $transportFields = $this->getFieldsHelper()->filterForPropertyPath($allFields, NULL, 'ps_offer_transport_text');
    if ($resolved['transport_text'] !== '') {
      foreach ($transportFields as $field) {
        $field->addValue($resolved['transport_text']);
      }
    }

    $ceilingMinFields = $this->getFieldsHelper()->filterForPropertyPath($allFields, NULL, 'ps_offer_ceiling_height_min');
    if ($resolved['ceiling_height_min'] !== NULL) {
      foreach ($ceilingMinFields as $field) {
        $field->addValue($resolved['ceiling_height_min']);
      }
    }

    $ceilingMaxFields = $this->getFieldsHelper()->filterForPropertyPath($allFields, NULL, 'ps_offer_ceiling_height_max');
    if ($resolved['ceiling_height_max'] !== NULL) {
      foreach ($ceilingMaxFields as $field) {
        $field->addValue($resolved['ceiling_height_max']);
      }
    }
  }

  /**
   * Adds values to a multi-value Search API string property.
   *
   * @param array<int,\Drupal\search_api\Item\FieldInterface> $allFields
   *   Indexed fields.
   * @param string $propertyPath
   *   Property path to fill.
   * @param list<string> $values
   *   Distinct values.
   */
  private function addStringListValues(array $allFields, string $propertyPath, array $values): void {
    if ($values === []) {
      return;
    }

    $fields = $this->getFieldsHelper()->filterForPropertyPath($allFields, NULL, $propertyPath);
    foreach ($fields as $field) {
      foreach ($values as $value) {
        $field->addValue($value);
      }
    }
  }

}
