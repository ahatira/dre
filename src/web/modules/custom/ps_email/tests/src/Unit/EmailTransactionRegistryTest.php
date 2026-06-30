<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_email\Unit;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\ps_email\Service\EmailTransactionRegistry;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @coversDefaultClass \Drupal\ps_email\Service\EmailTransactionRegistry
 * @group ps_email
 */
#[CoversClass(EmailTransactionRegistry::class)]
final class EmailTransactionRegistryTest extends UnitTestCase {

  /**
   * @covers ::getDefinitions
   */
  public function testGetDefinitionsFiltersDisabledModules(): void {
    $moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $moduleHandler->method('moduleExists')->willReturnCallback(
      static fn (string $module): bool => in_array($module, ['ps_form', 'ps_search'], TRUE),
    );

    $registry = new EmailTransactionRegistry($moduleHandler);
    $registry->setStringTranslation($this->createMock(TranslationInterface::class));

    $definitions = $registry->getDefinitions();
    $ids = array_map(static fn ($definition) => $definition->id, $definitions);

    self::assertSame(['contact_confirmation', 'search_alert_digest'], $ids);
  }

  /**
   * @covers ::getDefinitions
   */
  public function testGetDefinitionsSortsByWeight(): void {
    $moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $moduleHandler->method('moduleExists')->willReturn(TRUE);

    $registry = new EmailTransactionRegistry($moduleHandler);
    $registry->setStringTranslation($this->createMock(TranslationInterface::class));

    $definitions = $registry->getDefinitions();
    $weights = array_map(static fn ($definition) => $definition->weight, $definitions);
    $sorted = $weights;
    sort($sorted);

    self::assertSame($weights, $sorted);
  }

}
