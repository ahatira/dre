<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_context\Unit\Service;

use Drupal\ps_context\Service\SearchBudgetFilterResolver;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_context\Service\SearchBudgetFilterResolver
 * @group ps_context
 */
final class SearchBudgetFilterResolverTest extends UnitTestCase {

  private SearchBudgetFilterResolver $resolver;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->resolver = new SearchBudgetFilterResolver();
    $this->resolver->setStringTranslation($this->getStringTranslationStub());
  }

  /**
   * @covers ::resolve
   * @dataProvider resolveProvider
   */
  public function testResolve(?string $asset, ?string $op, ?string $expectedUnit, string $expectedFieldLabel): void {
    $config = $this->resolver->resolve($asset, $op);

    self::assertSame($expectedUnit, $config['budget_unit']);
    self::assertSame($expectedFieldLabel, $config['field_label']);
  }

  /**
   * @return array<string, array{0: ?string, 1: ?string, 2: ?string, 3: string}>
   */
  public static function resolveProvider(): array {
    return [
      'flexible' => [NULL, NULL, NULL, 'Budget'],
      'rent bur' => ['BUR', 'LOC', 'PER_M2', 'Rent'],
      'rent cow' => ['COW', 'LOC', 'PER_POSTE', 'Rent'],
      'sale any asset' => ['ENT', 'VEN', 'GLOBAL', 'Price'],
      'flexible with asset only' => ['BUR', NULL, NULL, 'Budget'],
    ];
  }

}
