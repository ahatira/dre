<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\Attribute\QueueWorker;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_migrate\Service\ImportPipeline;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Processes one CRM XML file from the import queue.
 */
#[QueueWorker(
  id: 'ps_migrate.import_file',
  title: new TranslatableMarkup('CRM import file processor'),
  cron: ['time' => 60],
)]
final class ImportFileQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ImportPipeline $importPipeline,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_migrate.import_pipeline'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data): void {
    if (!is_array($data)) {
      throw new \InvalidArgumentException('Invalid CRM import queue item.');
    }

    $this->importPipeline->processQueueItem($data);
  }

}
