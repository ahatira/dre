<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrateProcessInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Downloads or copies files with configurable HTTP timeout and retries.
 *
 * Wraps the core file_copy plugin for remote media URLs.
 *
 * @MigrateProcessPlugin(
 *   id = "retry_file_copy"
 * )
 */
final class RetryFileCopy extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly MigrateProcessInterface $fileCopyPlugin,
    private readonly LoggerInterface $logger,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $configuration += [
      'move' => FALSE,
    ];

    $settings = $container->get('config.factory')->get('ps_migrate.import_pipeline_settings');
    $timeout = max(1, (int) ($settings->get('media_download_timeout') ?? 30));
    $configuration['guzzle_options'] = array_merge(
      $configuration['guzzle_options'] ?? [],
      [
        'timeout' => $timeout,
        'connect_timeout' => min(10, $timeout),
      ],
    );

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('plugin.manager.migrate.process')->createInstance('file_copy', $configuration),
      $container->get('logger.channel.ps_migrate'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): mixed {
    if ($row->isStub()) {
      return NULL;
    }

    $retryCount = max(0, (int) $this->configFactory
      ->get('ps_migrate.import_pipeline_settings')
      ->get('media_download_retry_count'));
    $maxAttempts = $retryCount + 1;
    $lastException = NULL;

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
      try {
        return $this->fileCopyPlugin->transform($value, $migrate_executable, $row, $destination_property);
      }
      catch (MigrateException $exception) {
        $lastException = $exception;
        if ($attempt >= $maxAttempts) {
          break;
        }

        $sourceUrl = is_array($value) ? (string) ($value[0] ?? '') : '';
        $this->logger->warning('Media download attempt @attempt/@max failed for @url: @message', [
          '@attempt' => $attempt,
          '@max' => $maxAttempts,
          '@url' => $sourceUrl,
          '@message' => $exception->getMessage(),
        ]);
        usleep(500_000);
      }
    }

    throw $lastException ?? new MigrateException('Media download failed after retries.');
  }

}
