<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_email\Kernel;

use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_email\Service\EmailTransactionRegistry;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Kernel tests for the transactional email admin hub route.
 *
 * @group ps_email
 */
#[RunTestsInSeparateProcesses]
final class EmailAdminRouteKernelTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'ps_core',
    'ps_email',
  ];

  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['ps_core', 'ps_email']);
  }

  /**
   * Ensures the email hub route uses the dedicated overview controller.
   */
  public function testEmailHubRouteUsesDedicatedController(): void {
    /** @var \Drupal\Core\Routing\RouteProviderInterface $routeProvider */
    $routeProvider = $this->container->get('router.route_provider');
    self::assertInstanceOf(RouteProviderInterface::class, $routeProvider);

    self::assertSame(
      '\\Drupal\\ps_email\\Controller\\EmailAdminOverviewController::overview',
      $routeProvider->getRouteByName('ps_email.admin')->getDefault('_controller')
    );
    self::assertSame(
      'access ps_email hub',
      $routeProvider->getRouteByName('ps_email.admin')->getRequirement('_permission')
    );
  }

  /**
   * Ensures the email transaction registry service is registered.
   */
  public function testEmailTransactionRegistryService(): void {
    $registry = $this->container->get('ps_email.email_transaction_registry');
    self::assertInstanceOf(EmailTransactionRegistry::class, $registry);
    self::assertSame([], $registry->getDefinitions());
  }

}
