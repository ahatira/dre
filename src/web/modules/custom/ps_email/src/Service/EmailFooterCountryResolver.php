<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

/**
 * Resolves the active multisite country code from the site directory.
 */
final class EmailFooterCountryResolver {

  /**
   * Resolves country code (fr, com, be, …) for the bootstrapped site.
   */
  public function resolveCountryCode(): string {
    if (!function_exists('ps_load_countries_manifest')) {
      $manifestLoader = DRUPAL_ROOT . '/sites/countries.php';
      if (is_readable($manifestLoader)) {
        require_once $manifestLoader;
      }
    }

    if (function_exists('ps_load_countries_manifest')) {
      $siteDir = basename(\Drupal::getContainer()->getParameter('site.path'));
      foreach (ps_load_countries_manifest()['countries'] as $code => $config) {
        if (is_array($config) && ($config['site_dir'] ?? '') === $siteDir) {
          return (string) $code;
        }
      }
    }

    return 'com';
  }

}
