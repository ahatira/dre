<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\ps_migrate\Service\FeaturePayloadDefaultsNormalizer as PayloadDefaultsNormalizerService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Canonicalizes payload defaults before writing feature definitions.
 *
 * @MigrateProcessPlugin(
 *   id = "feature_normalize_payload_defaults"
 * )
 */
final class FeaturePayloadDefaultsNormalizer extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a FeaturePayloadDefaultsNormalizer object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly PayloadDefaultsNormalizerService $normalizer,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_migrate.feature_payload_defaults_normalizer'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): array {
    return $this->normalizer->normalize($value);
  }

}