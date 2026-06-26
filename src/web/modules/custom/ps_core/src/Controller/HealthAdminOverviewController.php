<?php

declare(strict_types=1);

namespace Drupal\ps_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_core\Service\HealthCheckOverviewBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Platform health dashboard for Property Search operations.
 */
final class HealthAdminOverviewController extends ControllerBase {

  public function __construct(
    private readonly HealthCheckOverviewBuilder $overviewBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_core.health_check_overview_builder'),
    );
  }

  /**
   * Overview of platform health checks.
   */
  public function overview(): array {
    return $this->overviewBuilder->buildOverview();
  }

}
