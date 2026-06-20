<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the CRM import run entity.
 */
#[ContentEntityType(
  id: 'import_run',
  label: new TranslatableMarkup('CRM import run'),
  label_collection: new TranslatableMarkup('CRM import runs'),
  label_singular: new TranslatableMarkup('CRM import run'),
  label_plural: new TranslatableMarkup('CRM import runs'),
  label_count: [
    'singular' => '@count CRM import run',
    'plural' => '@count CRM import runs',
  ],
  handlers: [
    'list_builder' => 'Drupal\\ps_migrate\\ImportRunListBuilder',
    'access' => 'Drupal\\ps_migrate\\ImportRunAccessControlHandler',
    'view_builder' => 'Drupal\\Core\\Entity\\EntityViewBuilder',
    'route_provider' => [
      'html' => 'Drupal\\Core\\Entity\\Routing\\AdminHtmlRouteProvider',
    ],
    'views_data' => 'Drupal\\views\\EntityViewsData',
  ],
  base_table: 'import_run',
  admin_permission: 'manage ps_migrate',
  entity_keys: [
    'id' => 'id',
    'uuid' => 'uuid',
    'owner' => 'uid',
    'label' => 'filename',
  ],
  links: [
    'canonical' => '/admin/ps/import/runs/{import_run}',
    'collection' => '/admin/ps/import/runs',
  ],
)]
final class ImportRun extends ContentEntityBase implements ImportRunInterface {

  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function getFilename(): string {
    return (string) $this->get('filename')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getPipelineStatus(): string {
    return (string) $this->get('pipeline_status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getImportMode(): string {
    return (string) $this->get('import_mode')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getStats(): array {
    $raw = (string) $this->get('stats')->value;
    if ($raw === '') {
      return [];
    }
    $decoded = json_decode($raw, TRUE);
    return is_array($decoded) ? $decoded : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getMessages(): string {
    return (string) $this->get('messages')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Submitter'))
      ->setSetting('target_type', 'user')
      ->setDefaultValue(0);

    $fields['filename'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Filename'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255);

    $fields['pipeline_status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Status'))
      ->setRequired(TRUE)
      ->setSetting('allowed_values', [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_SUCCESS => 'Success',
        self::STATUS_FAILED => 'Failed',
      ])
      ->setDefaultValue(self::STATUS_PENDING);

    $fields['import_mode'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Mode'))
      ->setRequired(TRUE)
      ->setSetting('allowed_values', [
        self::MODE_FULL => 'Full',
        self::MODE_DELTA => 'Delta',
      ])
      ->setDefaultValue(self::MODE_FULL);

    $fields['source_uri'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Source URI'))
      ->setSetting('max_length', 512);

    $fields['file_uri'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Final file URI'))
      ->setSetting('max_length', 512);

    $fields['started'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Started'));

    $fields['finished'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Finished'));

    $fields['stats'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Statistics'));

    $fields['messages'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Messages'));

    return $fields;
  }

}
