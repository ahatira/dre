<?php

declare(strict_types=1);

namespace Drupal\ps_search\Commands;

use Drupal\ps_search\GeoZone\GeoZoneBuilder;
use Drupal\ps_search\GeoZone\GeoZoneImporter;
use Drupal\ps_search\GeoZone\GeoZoneValidator;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for geo zone referential (search v2 M0).
 */
final class SearchGeoZoneCommands extends DrushCommands {

  public function __construct(
    private readonly GeoZoneImporter $importer,
    private readonly GeoZoneBuilder $builder,
    private readonly GeoZoneValidator $validator,
  ) {
    parent::__construct();
  }

  /**
   * Imports geo zones for one country from YAML source into active config.
   */
  #[CLI\Command(name: 'ps:search:geo-zones:import', aliases: ['ps-sgzi'])]
  #[CLI\Argument(name: 'country', description: 'Country code (fr, be, com, …).')]
  #[CLI\Option(name: 'source', description: 'Optional absolute path to a YAML source file.')]
  #[CLI\Usage(name: 'drush @ps.fr ps:search:geo-zones:import fr', description: 'Import French geo zones.')]
  public function import(string $country, array $options = ['source' => NULL]): void {
    $source = is_string($options['source'] ?? NULL) && $options['source'] !== ''
      ? $options['source']
      : NULL;

    try {
      $result = $this->importer->importCountry($country, $source);
    }
    catch (\InvalidArgumentException $exception) {
      $this->logger()->error($exception->getMessage());
      return;
    }

    $this->io()->success(sprintf(
      'Imported %d geo zones for "%s" into %s.',
      $result['zone_count'],
      $result['country'],
      $result['config_name'],
    ));
  }

  /**
   * Validates geo zone config for one country or all installed countries.
   */
  #[CLI\Command(name: 'ps:search:geo-zones:validate', aliases: ['ps-sgzv'])]
  #[CLI\Argument(name: 'country', description: 'Optional country code; validates all when omitted.')]
  #[CLI\Option(name: 'source', description: 'Validate a YAML source file instead of active config.')]
  public function validate(?string $country = NULL, array $options = ['source' => NULL]): void {
    $source = is_string($options['source'] ?? NULL) && $options['source'] !== ''
      ? $options['source']
      : NULL;

    if ($country !== NULL && $country !== '') {
      $errors = $this->importer->validateCountry($country, $source);
      if ($errors === []) {
        $this->io()->success(sprintf('Geo zones for "%s" are valid.', strtolower($country)));
        return;
      }
      foreach ($errors as $error) {
        $this->io()->error($error);
      }
      return;
    }

    if ($source !== NULL) {
      $this->io()->error('The --source option requires a country argument.');
      return;
    }

    $results = $this->importer->validateAllInstalled();
    if ($results === []) {
      $this->io()->success('All installed geo zone configs are valid.');
      return;
    }

    foreach ($results as $countryCode => $errors) {
      $this->io()->section($countryCode);
      foreach ($errors as $error) {
        $this->io()->error($error);
      }
    }
  }

  /**
   * Builds geo zones YAML for one supported country.
   */
  #[CLI\Command(name: 'ps:search:geo-zones:build', aliases: ['ps-sgzb'])]
  #[CLI\Argument(name: 'country', description: 'Country code (fr, be, com, nl, …).')]
  #[CLI\Usage(name: 'drush ps:search:geo-zones:build fr', description: 'Regenerate data/geo_zones/fr.yml.')]
  #[CLI\Usage(name: 'drush ps:search:geo-zones:build com', description: 'Regenerate data/geo_zones/com.yml from fr.yml.')]
  public function build(string $country): void {
    $country = strtolower(trim($country));

    try {
      $payload = $this->builder->buildPayload($country);
      $errors = $this->validator->validateCountryPayload(
        $country,
        is_array($payload['zones'] ?? NULL) ? $payload['zones'] : [],
        is_string($payload['default_zone'] ?? NULL) ? $payload['default_zone'] : NULL,
      );
      if ($errors !== []) {
        foreach ($errors as $error) {
          $this->logger()->error($error);
        }
        return;
      }

      $path = $this->builder->exportToModuleData($country, $payload);
    }
    catch (\Throwable $exception) {
      $this->logger()->error($exception->getMessage());
      return;
    }

    $this->io()->success(sprintf('Geo zones YAML written to %s', $path));
    $this->io()->note(sprintf('Run drush ps:search:geo-zones:import %s to load into active config.', $country));
  }

}
