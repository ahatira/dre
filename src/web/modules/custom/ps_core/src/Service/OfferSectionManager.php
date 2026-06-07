<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\ps_core\Annotation\OfferSection;
use Drupal\ps_core\Plugin\OfferSection\OfferSectionInterface;

/**
 * Discovers offer detail section plugins from enabled modules.
 */
final class OfferSectionManager extends DefaultPluginManager {

  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler,
  ) {
    parent::__construct(
      'Plugin/OfferSection',
      $namespaces,
      $module_handler,
      OfferSectionInterface::class,
      OfferSection::class,
    );

    $this->alterInfo('ps_offer_section_info');
    $this->setCacheBackend($cache_backend, 'ps_core_offer_section_plugins');
  }

}
