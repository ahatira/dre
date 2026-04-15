<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Plugin\search_api\processor;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferPriceSearchValueResolver;
use Drupal\search_api\Attribute\SearchApiProcessor;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Adds stable main-price fields for offer indexing.
 */
#[SearchApiProcessor(
  id: 'ps_offer_price_main',
  label: new TranslatableMarkup('Offer main price fields'),
  description: new TranslatableMarkup('Adds derived main offer price fields (display, amount, normalized, on_request).'),
  stages: [
    'add_properties' => 0,
  ],
)]
final class OfferPriceMainProcessor extends ProcessorPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the processor.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly OfferPriceSearchValueResolver $valueResolver,
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
      $container->get('ps_offer.price_search_value_resolver'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(\Drupal\search_api\IndexInterface $index): bool {
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
      'ps_offer_price_display_main' => new ProcessorProperty([
        'label' => $this->t('Offer main price display'),
        'description' => $this->t('Primary offer price as display string.'),
        'type' => 'string',
        'processor_id' => $processorId,
      ]),
      'ps_offer_price_amount_main' => new ProcessorProperty([
        'label' => $this->t('Offer main price amount'),
        'description' => $this->t('Primary offer price amount before normalization.'),
        'type' => 'decimal',
        'processor_id' => $processorId,
      ]),
      'ps_offer_price_normalized_main' => new ProcessorProperty([
        'label' => $this->t('Offer main normalized price'),
        'description' => $this->t('Primary offer price normalized for comparison.'),
        'type' => 'decimal',
        'processor_id' => $processorId,
      ]),
      'ps_offer_price_on_request' => new ProcessorProperty([
        'label' => $this->t('Offer main price on request'),
        'description' => $this->t('Whether the primary offer price is on request.'),
        'type' => 'boolean',
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

    $displayFields = $this->getFieldsHelper()
      ->filterForPropertyPath($allFields, NULL, 'ps_offer_price_display_main');
    if ($resolved['display'] !== NULL) {
      foreach ($displayFields as $field) {
        $field->addValue($resolved['display']);
      }
    }

    $amountFields = $this->getFieldsHelper()
      ->filterForPropertyPath($allFields, NULL, 'ps_offer_price_amount_main');
    if ($resolved['amount'] !== NULL) {
      foreach ($amountFields as $field) {
        $field->addValue($resolved['amount']);
      }
    }

    $normalizedFields = $this->getFieldsHelper()
      ->filterForPropertyPath($allFields, NULL, 'ps_offer_price_normalized_main');
    if ($resolved['normalized'] !== NULL) {
      foreach ($normalizedFields as $field) {
        $field->addValue($resolved['normalized']);
      }
    }

    $onRequestFields = $this->getFieldsHelper()
      ->filterForPropertyPath($allFields, NULL, 'ps_offer_price_on_request');
    foreach ($onRequestFields as $field) {
      $field->addValue($resolved['on_request'] ? 1 : 0);
    }
  }

}
