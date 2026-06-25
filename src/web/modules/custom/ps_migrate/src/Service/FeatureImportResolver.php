<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureCanonicalGroupRegistry;

/**
 * Resolves CRM technical elements to catalogue groups and definition IDs.
 */
final class FeatureImportResolver {

  public function __construct(
    private readonly FeatureCanonicalGroupRegistry $groupRegistry,
    private readonly FeatureMigrationKeyBuilder $keyBuilder,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Builds a stable feature definition ID from a CRM CODE_ELEMENT value.
   */
  public function buildDefinitionId(string $featureCode): string {
    return $this->keyBuilder->buildDefinitionId('', $featureCode);
  }

  /**
   * Resolves a CRM element to a canonical feature group ID.
   *
   * Priority:
   * 1. Existing catalogue definition group (BO/XML already stored).
   * 2. CRM CODE_GROUP mapped to a canonical group.
   * 3. Fallback to informations_complementaires.
   */
  public function resolveGroupId(string $featureCode, string $crmGroupCode): string {
    $definitionId = $this->buildDefinitionId($featureCode);
    if ($definitionId !== '') {
      $existing = $this->entityTypeManager->getStorage('fb_feature_definition')->load($definitionId);
      if ($existing instanceof FeatureDefinition) {
        $group = (string) $existing->getGroup();
        if ($group !== '') {
          return $this->groupRegistry->resolveGroupId($group);
        }
      }
    }

    if (trim($crmGroupCode) !== '') {
      return $this->groupRegistry->resolveCrmGroupCode($crmGroupCode);
    }

    return 'informations_complementaires';
  }

  /**
   * Loads an existing feature definition by CRM CODE_ELEMENT.
   */
  public function loadDefinition(string $featureCode): ?FeatureDefinition {
    $definitionId = $this->buildDefinitionId($featureCode);
    if ($definitionId === '') {
      return NULL;
    }

    $definition = $this->entityTypeManager->getStorage('fb_feature_definition')->load($definitionId);
    return $definition instanceof FeatureDefinition ? $definition : NULL;
  }

}
