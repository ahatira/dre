<?php

declare(strict_types=1);

namespace Drupal\ps_search\Entity;

use Drupal\Core\Entity\Attribute\ContentEntityType;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the Search alert entity.
 */
#[ContentEntityType(
  id: 'search_alert',
  label: new \Drupal\Core\StringTranslation\TranslatableMarkup('Search alert'),
  label_collection: new \Drupal\Core\StringTranslation\TranslatableMarkup('Search alerts'),
  label_singular: new \Drupal\Core\StringTranslation\TranslatableMarkup('search alert'),
  label_plural: new \Drupal\Core\StringTranslation\TranslatableMarkup('search alerts'),
  label_count: [
    'singular' => '@count search alert',
    'plural' => '@count search alerts',
  ],
  handlers: [
    'list_builder' => 'Drupal\\ps_search\\SearchAlertListBuilder',
    'access' => 'Drupal\\ps_search\\SearchAlertAccessControlHandler',
    'form' => [
      'default' => 'Drupal\\ps_search\\Form\\SearchAlertForm',
      'add' => 'Drupal\\ps_search\\Form\\SearchAlertForm',
      'edit' => 'Drupal\\ps_search\\Form\\SearchAlertForm',
      'delete' => 'Drupal\\Core\\Entity\\ContentEntityDeleteForm',
    ],
    'route_provider' => [
      'html' => 'Drupal\\Core\\Entity\\Routing\\AdminHtmlRouteProvider',
    ],
    'views_data' => 'Drupal\\views\\EntityViewsData',
  ],
  base_table: 'search_alert',
  admin_permission: 'administer search alerts',
  entity_keys: [
    'id' => 'id',
    'uuid' => 'uuid',
    'owner' => 'uid',
    'label' => 'alert_name',
    'langcode' => 'langcode',
  ],
  links: [
    'canonical' => '/admin/ps/content/search-alerts/{search_alert}',
    'edit-form' => '/admin/ps/content/search-alerts/{search_alert}/edit',
    'delete-form' => '/admin/ps/content/search-alerts/{search_alert}/delete',
    'collection' => '/admin/ps/content/search-alerts',
  ],
)]
final class SearchAlert extends ContentEntityBase implements SearchAlertInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function getAlertName(): string {
    return (string) $this->get('alert_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getProfEmail(): string {
    return (string) $this->get('prof_email')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getFrequence(): string {
    return (string) $this->get('frequence')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getCriteria(): array {
    $raw = (string) $this->get('criteria')->value;
    if ($raw === '') {
      return [];
    }
    $decoded = json_decode($raw, TRUE);
    return is_array($decoded) ? $decoded : [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCriteriaHash(): string {
    return (string) $this->get('criteria_hash')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Owner'))
      ->setSetting('target_type', 'user')
      ->setDefaultValue(0);

    $fields['alert_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Alert title'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255);

    $fields['prof_email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Professional email'))
      ->setRequired(TRUE);

    $fields['frequence'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Frequency'))
      ->setRequired(TRUE)
      ->setSetting('allowed_values', [
        self::FREQUENCE_DAILY => t('Daily'),
        self::FREQUENCE_WEEKLY => t('Weekly'),
      ])
      ->setDefaultValue(self::FREQUENCE_WEEKLY);

    $fields['optout_email'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Opt out of email communications'))
      ->setDefaultValue(FALSE);

    $fields['optout_sms'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Opt out of SMS communications'))
      ->setDefaultValue(FALSE);

    $fields['optout_tel'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Opt out of phone communications'))
      ->setDefaultValue(FALSE);

    $fields['criteria'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Search criteria'))
      ->setRequired(TRUE);

    $fields['criteria_hash'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Criteria hash'))
      ->setSetting('max_length', 64)
      ->setRequired(TRUE);

    $fields['search_url'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Search URL'))
      ->setSetting('max_length', 2048);

    $fields['search_path'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Search path'))
      ->setSetting('max_length', 512);

    $fields['alert_status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Alert status'))
      ->setRequired(TRUE)
      ->setSetting('allowed_values', [
        self::STATUS_ACTIVE => t('Active'),
        self::STATUS_PAUSED => t('Paused'),
      ])
      ->setDefaultValue(self::STATUS_ACTIVE);

    $fields['last_sent'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Last notification sent'));

    $fields['last_match_count'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Last match count'))
      ->setDefaultValue(0);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'));

    return $fields;
  }

}
