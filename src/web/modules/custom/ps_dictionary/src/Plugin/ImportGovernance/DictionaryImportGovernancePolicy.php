<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyBase;
use Drupal\ps_dictionary\Service\DictionaryImportGovernance;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Import governance policy for business dictionaries.
 *
 * @ImportGovernancePolicy(
 *   id = "dictionary",
 *   admin_label = @Translation("Dictionary"),
 *   description = @Translation("Dictionary CSV import and CRM label preservation rules."),
 *   settings_route = "ps_dictionary.governance_domain_settings",
 *   weight = 30,
 * )
 */
final class DictionaryImportGovernancePolicy extends ImportGovernancePolicyBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly DictionaryImportGovernance $importGovernance,
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
      $container->get('ps_dictionary.import_governance'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeIds(): array {
    return [DictionaryImportGovernance::ENTITY_TYPE_ID];
  }

  /**
   * {@inheritdoc}
   */
  public function shouldSkipProtectedRow(EntityInterface $entity): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldPreserveProtectedFields(EntityInterface $entity): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function resolveEffectiveLockStrategy(string $entityTypeId): string {
    return 'log_only';
  }

}
