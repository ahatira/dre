<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\HealthCheck;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_core\HealthCheck\HealthCheckResult;
use Drupal\ps_core\HealthCheck\HealthCheckStatus;
use Drupal\ps_core\Plugin\HealthCheck\HealthCheckBase;
use Drupal\ps_migrate\Service\ImportPipelinePathResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Verifies CRM import pipeline folders exist and are writable (no mkdir).
 *
 * @HealthCheck(
 *   id = "import_paths",
 *   label = @Translation("Import file paths"),
 *   group = "files",
 *   weight = 0,
 * )
 */
final class ImportPathsHealthCheck extends HealthCheckBase implements ContainerFactoryPluginInterface {

  /**
   * @var list<string>
   */
  private const FOLDER_KEYS = ['incoming', 'processing', 'archive', 'failed'];

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ImportPipelinePathResolver $pathResolver,
    private readonly FileSystemInterface $fileSystem,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_migrate.import_pipeline_path_resolver'),
      $container->get('file_system'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function run(): HealthCheckResult {
    $problems = [];
    $checked = [];

    foreach (self::FOLDER_KEYS as $key) {
      try {
        $checked[] = $this->inspectPath($key, $this->pathResolver->getPath($key), $problems);
      }
      catch (\Throwable $exception) {
        $problems[] = (string) $this->t('@label misconfigured: @error', [
          '@label' => $key,
          '@error' => $exception->getMessage(),
        ]);
        $checked[] = $key . ': misconfigured';
      }
    }

    try {
      $stagingUri = $this->pathResolver->getStagingUri();
      $stagingDir = $this->fileSystem->dirname($stagingUri);
      if ($stagingDir !== '') {
        $checked[] = $this->inspectPath('staging', $stagingDir, $problems);
      }
    }
    catch (\Throwable $exception) {
      $problems[] = (string) $this->t('Staging URI: @error', [
        '@error' => $exception->getMessage(),
      ]);
    }

    if ($problems === []) {
      return new HealthCheckResult(
        HealthCheckStatus::OK,
        (string) $this->t('All @count configured import paths exist and are writable.', [
          '@count' => count($checked),
        ]),
        [
          [
            'title' => (string) $this->t('Pipeline settings'),
            'route' => 'ps_migrate.import_pipeline_settings',
          ],
        ],
        ['cd src && vendor/bin/drush @ps.fr config:get ps_migrate.import_pipeline_settings paths'],
        implode(' · ', $checked),
      );
    }

    $status = str_contains(implode(' ', $problems), 'missing')
      ? HealthCheckStatus::FAIL
      : HealthCheckStatus::WARNING;

    return new HealthCheckResult(
      $status,
      (string) $this->t('@count import path issue(s) detected.', [
        '@count' => count($problems),
      ]),
      [
        [
          'title' => (string) $this->t('Pipeline settings'),
          'route' => 'ps_migrate.import_pipeline_settings',
        ],
      ],
      ['cd src && vendor/bin/drush @ps.fr config:get ps_migrate.import_pipeline_settings paths'],
      implode(' · ', $problems),
    );
  }

  /**
   * @param list<string> $problems
   */
  private function inspectPath(string $label, string $uri, array &$problems): string {
    $realpath = $this->fileSystem->realpath($uri);
    if ($realpath === FALSE || !is_dir($realpath)) {
      $problems[] = (string) $this->t('@label missing (@uri).', [
        '@label' => $label,
        '@uri' => $uri,
      ]);
      return $label . ': missing';
    }

    if (!is_writable($realpath)) {
      $problems[] = (string) $this->t('@label not writable (@uri).', [
        '@label' => $label,
        '@uri' => $uri,
      ]);
      return $label . ': not writable';
    }

    return $label . ': OK';
  }

}
