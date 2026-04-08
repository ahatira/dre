<?php

declare(strict_types=1);

namespace Drupal\ps_agent;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Lists Agent entities.
 */
final class AgentListBuilder extends EntityListBuilder
{
  /**
   * Date formatter service.
   */
    protected DateFormatterInterface $dateFormatter;

  /**
   * {@inheritdoc}
   */
    public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): static
    {
        return new static(
            $entity_type,
            $container->get('entity_type.manager')->getStorage($entity_type->id()),
            $container->get('date.formatter'),
        );
    }

  /**
   * Constructs an AgentListBuilder instance.
   */
    public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $date_formatter)
    {
        parent::__construct($entity_type, $storage);
        $this->dateFormatter = $date_formatter;
    }

  /**
   * {@inheritdoc}
   */
    public function buildHeader(): array
    {
        $header['name'] = $this->t('Name');
        $header['job_title'] = $this->t('Job title');
        $header['phone'] = $this->t('Phone');
        $header['webform_id'] = $this->t('Webform ID');
        $header['status'] = $this->t('Status');
        $header['changed'] = $this->t('Updated');

        return $header + parent::buildHeader();
    }

  /**
   * {@inheritdoc}
   */
    public function buildRow(EntityInterface $entity): array
    {
        $row['name'] = $entity->label();
        $row['job_title'] = (string) ($entity->get('job_title')->value ?? '');
        $row['phone'] = (string) ($entity->get('phone')->value ?? '');
        $row['webform_id'] = (string) ($entity->get('webform_id')->value ?? '');
        $row['status'] = $entity->isPublished() ? $this->t('Published') : $this->t('Unpublished');
        $row['changed'] = $this->dateFormatter->format((int) $entity->getChangedTime(), 'short');

        return $row + parent::buildRow($entity);
    }
}
