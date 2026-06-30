<?php

declare(strict_types=1);

namespace Drupal\ps_email\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_email\Service\EmailAdminOverviewBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Central admin hub for Property Search transactional emails.
 */
final class EmailAdminOverviewController extends ControllerBase {

  public function __construct(
    private readonly EmailAdminOverviewBuilder $overviewBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_email.email_admin_overview_builder'),
    );
  }

  /**
   * Overview of transactional email types, previews and E2E scripts.
   *
   * @return array<string, mixed>
   *   Render array.
   */
  public function overview(): array {
    return $this->overviewBuilder->buildOverview();
  }

}
