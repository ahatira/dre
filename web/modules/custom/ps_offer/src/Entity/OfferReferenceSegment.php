<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_offer\Form\OfferReferenceSegmentDeleteForm;
use Drupal\ps_offer\Form\OfferReferenceSegmentForm;
use Drupal\ps_offer\OfferReferenceSegmentListBuilder;

/**
 * Defines the Offer Reference Segment config entity.
 */
#[ConfigEntityType(
  id: 'ps_offer_reference_segment',
  label: new TranslatableMarkup('Offer Reference Segment'),
  label_collection: new TranslatableMarkup('Offer Reference Segments'),
  label_singular: new TranslatableMarkup('offer reference segment'),
  label_plural: new TranslatableMarkup('offer reference segments'),
  label_count: [
    'singular' => '@count offer reference segment',
    'plural' => '@count offer reference segments',
  ],
  handlers: [
    'list_builder' => OfferReferenceSegmentListBuilder::class,
    'form' => [
      'add' => OfferReferenceSegmentForm::class,
      'edit' => OfferReferenceSegmentForm::class,
      'delete' => OfferReferenceSegmentDeleteForm::class,
    ],
    'route_provider' => [
      'html' => 'Drupal\\Core\\Entity\\Routing\\AdminHtmlRouteProvider',
    ],
  ],
  admin_permission: 'administer offer content',
  config_prefix: 'reference_segment',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
    'uuid' => 'uuid',
    'weight' => 'weight',
  ],
  config_export: [
    'id',
    'label',
    'enabled',
    'weight',
    'segment_type',
    'source_field',
    'length',
    'static_value',
    'custom_map_text',
    'start_index',
    'date_source_field',
    'date_format',
    'auto_start',
  ],
  links: [
    'add-form' => '/admin/ps/config/offers/reference/add',
    'edit-form' => '/admin/ps/config/offers/reference/{ps_offer_reference_segment}/edit',
    'delete-form' => '/admin/ps/config/offers/reference/{ps_offer_reference_segment}/delete',
    'collection' => '/admin/ps/config/offers/reference',
  ],
)]
final class OfferReferenceSegment extends ConfigEntityBase implements OfferReferenceSegmentInterface {

  /**
   * Segment ID.
   */
  protected string $id = '';

  /**
   * Segment label.
   */
  protected string $label = '';

  /**
   * Whether this segment is enabled.
   */
  protected bool $enabled = TRUE;

  /**
   * Display and processing order.
   */
  protected int $weight = 0;

  /**
   * Segment type.
   */
  protected string $segment_type = 'custom';

  /**
   * Source field machine name.
   */
  protected string $source_field = '';

  /**
   * Segment fixed length.
   */
  protected int $length = 1;

  /**
   * Static value for static type.
   */
  protected string $static_value = '';

  /**
   * Custom mapping text using KEY=VALUE lines.
   */
  protected string $custom_map_text = '';

  /**
   * 1-based start index for start type.
   */
  protected int $start_index = 1;

  /**
   * Date source field for date type.
   */
  protected string $date_source_field = 'publish_on';

  /**
   * Date format key.
   */
  protected string $date_format = 'YY';

  /**
   * Auto-start counter value.
   */
  protected int $auto_start = 1;

  /**
   * {@inheritdoc}
   */
  public function isEnabled(): bool {
    return $this->enabled;
  }

  /**
   * {@inheritdoc}
   */
  public function getSegmentType(): string {
    return $this->segment_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceField(): string {
    return $this->source_field;
  }

  /**
   * {@inheritdoc}
   */
  public function getLength(): int {
    return max(1, $this->length);
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(): int {
    return $this->weight;
  }

  /**
   * {@inheritdoc}
   */
  public function getStaticValue(): string {
    return $this->static_value;
  }

  /**
   * {@inheritdoc}
   */
  public function getCustomMapText(): string {
    return $this->custom_map_text;
  }

  /**
   * {@inheritdoc}
   */
  public function getStartIndex(): int {
    return max(1, $this->start_index);
  }

  /**
   * {@inheritdoc}
   */
  public function getDateSourceField(): string {
    return $this->date_source_field;
  }

  /**
   * {@inheritdoc}
   */
  public function getDateFormat(): string {
    return $this->date_format;
  }

  /**
   * {@inheritdoc}
   */
  public function getAutoStart(): int {
    return max(1, $this->auto_start);
  }

}
