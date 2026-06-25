<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Service;

/**
 * Canonical feature group registry and legacy group resolution.
 *
 * The catalogue exposes four business groups. Legacy install groups and CRM
 * CODE_GROUP values are mapped onto this canonical set.
 */
final class FeatureCanonicalGroupRegistry {

  /**
   * Canonical feature group machine names.
   *
   * @var string[]
   */
  public const CANONICAL_GROUP_IDS = [
    'equipements',
    'prestations_de_service',
    'type_etat_du_batiment',
    'informations_complementaires',
  ];

  /**
   * Legacy group IDs superseded by the canonical catalogue.
   *
   * @var string[]
   */
  public const LEGACY_GROUP_IDS = [
    'amenagements',
    'exterieurs',
    'hauteurs',
    'acces_vehicules',
    'activite_non_autorisee',
    'structure_du_batiment',
    'normes_certifications_et_labels',
  ];

  /**
   * Maps legacy group IDs to canonical group IDs.
   *
   * @var array<string, string>
   */
  private const LEGACY_GROUP_MAP = [
    'amenagements' => 'equipements',
    'exterieurs' => 'equipements',
    'hauteurs' => 'type_etat_du_batiment',
    'structure_du_batiment' => 'type_etat_du_batiment',
    'acces_vehicules' => 'equipements',
    'activite_non_autorisee' => 'informations_complementaires',
    'normes_certifications_et_labels' => 'informations_complementaires',
  ];

  /**
   * Maps normalized CRM CODE_GROUP values to canonical group IDs.
   *
   * @var array<string, string>
   */
  private const CRM_GROUP_MAP = [
    'am_nagements' => 'equipements',
    'amenagements' => 'equipements',
    'equipements' => 'equipements',
    'equipement' => 'equipements',
    'exterieurs' => 'equipements',
    'exterieur' => 'equipements',
    'hauteurs' => 'type_etat_du_batiment',
    'hauteur' => 'type_etat_du_batiment',
    'structure_du_batiment' => 'type_etat_du_batiment',
    'type_etat_du_batiment' => 'type_etat_du_batiment',
    'etat_du_batiment' => 'type_etat_du_batiment',
    'acces_vehicules' => 'equipements',
    'acces_vehicule' => 'equipements',
    'activite_non_autorisee' => 'informations_complementaires',
    'normes_certifications_et_labels' => 'informations_complementaires',
    'normes_certifications' => 'informations_complementaires',
    'prestations_de_service' => 'prestations_de_service',
    'services' => 'prestations_de_service',
    'service' => 'prestations_de_service',
    'informations_complementaires' => 'informations_complementaires',
    'information_complementaire' => 'informations_complementaires',
  ];

  /**
   * Returns canonical feature group IDs.
   *
   * @return string[]
   *   Canonical group machine names.
   */
  public function getCanonicalGroupIds(): array {
    return self::CANONICAL_GROUP_IDS;
  }

  /**
   * Returns legacy group IDs replaced by the canonical catalogue.
   *
   * @return string[]
   *   Legacy group machine names.
   */
  public function getLegacyGroupIds(): array {
    return self::LEGACY_GROUP_IDS;
  }

  /**
   * Resolves a stored group ID to a canonical group ID.
   */
  public function resolveGroupId(string $groupId): string {
    $groupId = trim($groupId);
    if ($groupId === '') {
      return 'informations_complementaires';
    }

    if ($this->isCanonicalGroupId($groupId)) {
      return $groupId;
    }

    $normalized = $this->normalizeCode($groupId);
    if (isset(self::LEGACY_GROUP_MAP[$normalized])) {
      return self::LEGACY_GROUP_MAP[$normalized];
    }

    if (isset(self::CRM_GROUP_MAP[$normalized])) {
      return self::CRM_GROUP_MAP[$normalized];
    }

    return 'informations_complementaires';
  }

  /**
   * Resolves a CRM CODE_GROUP value to a canonical group ID.
   */
  public function resolveCrmGroupCode(string $crmGroupCode): string {
    $crmGroupCode = trim($crmGroupCode);
    if ($crmGroupCode === '') {
      return 'informations_complementaires';
    }

    $normalized = $this->normalizeCode($crmGroupCode);
    if (isset(self::CRM_GROUP_MAP[$normalized])) {
      return self::CRM_GROUP_MAP[$normalized];
    }

    if (isset(self::LEGACY_GROUP_MAP[$normalized])) {
      return self::LEGACY_GROUP_MAP[$normalized];
    }

    if ($this->isCanonicalGroupId($normalized)) {
      return $normalized;
    }

    return 'informations_complementaires';
  }

  /**
   * Checks whether a group ID is one of the four canonical groups.
   */
  public function isCanonicalGroupId(string $groupId): bool {
    return in_array($groupId, self::CANONICAL_GROUP_IDS, TRUE);
  }

  /**
   * Checks whether a group ID is a legacy group slated for deactivation.
   */
  public function isLegacyGroupId(string $groupId): bool {
    return in_array($groupId, self::LEGACY_GROUP_IDS, TRUE);
  }

  /**
   * Normalizes a CRM or Drupal group code.
   */
  private function normalizeCode(string $code): string {
    $code = trim($code);
    if ($code === '') {
      return '';
    }

    $code = strtolower($code);
    $code = preg_replace('/[^a-z0-9]+/u', '_', $code) ?? $code;

    return trim($code, '_');
  }

}
