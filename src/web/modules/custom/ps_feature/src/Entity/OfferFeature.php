<?php

namespace Drupal\ps_feature\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionLogEntityTrait;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the Offer Feature entity.
 */
#[ContentEntityType(
  id: 'entity_offer_feature',
  label: new TranslatableMarkup('Offer feature'),
  label_collection: new TranslatableMarkup('Offer features'),
  base_table: 'entity_offer_feature',
  revision_table: 'entity_offer_feature_revision',
  entity_keys: [
    'id' => 'id',
    'revision' => 'vid',
    'uuid' => 'uuid',
  ],
  handlers: [
    'storage' => 'Drupal\Core\Entity\Sql\SqlContentEntityStorage',
    'form' => [
      'default' => 'Drupal\ps_feature\Form\OfferFeatureForm',
    ],
  ],
  admin_permission: 'administer ps features',
  revision_metadata_keys: [
    'revision_user' => 'revision_user',
    'revision_created' => 'revision_created',
    'revision_log_message' => 'revision_log_message',
  ],
)]
class OfferFeature extends ContentEntityBase {

  use EntityChangedTrait;
  use RevisionLogEntityTrait;

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::revisionLogBaseFieldDefinitions($entity_type);

    $fields['feature_definition_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Feature Definition ID'))
      ->setDescription(t('Reference to the feature definition config entity.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 128)
      ->setRevisionable(TRUE);

    $fields['feature_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Type driver'))
      ->setDescription(t('The type driver: flag, yes_no, numeric, range, text, dictionary, list, date.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 32)
      ->setRevisionable(TRUE);

    $fields['payload'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Payload JSON'))
      ->setDescription(t('JSON payload according to the type driver schema.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the feature was last edited.'))
      ->setRevisionable(TRUE);

    return $fields;
  }

  /**
   * Gets the feature definition ID.
   *
   * @return string
   *   The feature definition ID.
   */
  public function getFeatureDefinitionId(): string {
    return $this->get('feature_definition_id')->value ?? '';
  }

  /**
   * Gets the feature type driver.
   *
   * @return string
   *   The feature type (e.g., 'flag', 'numeric').
   */
  public function getFeatureType(): string {
    return $this->get('feature_type')->value ?? '';
  }

  /**
   * Gets the payload as array.
   *
   * @return array
   *   The decoded JSON payload.
   */
  public function getPayload(): array {
    $payload = $this->get('payload')->value ?? '{}';
    return json_decode($payload, TRUE) ?? [];
  }

  /**
   * Sets the payload.
   *
   * @param array $payload
   *   The payload array to encode as JSON.
   *
   * @return $this
   */
  public function setPayload(array $payload): static {
    $this->set('payload', json_encode($payload, JSON_UNESCAPED_UNICODE));
    return $this;
  }

}
