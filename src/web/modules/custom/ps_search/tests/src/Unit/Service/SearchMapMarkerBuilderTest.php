<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit\Service;

use Drupal\node\NodeInterface;
use Drupal\ps_search\Service\SearchMapMarkerBuilder;
use Drupal\Tests\ps_search\Unit\Stub\TestFieldItemStub;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\SearchMapMarkerBuilder
 * @group ps_search
 */
final class SearchMapMarkerBuilderTest extends UnitTestCase {

  /**
   * @covers ::buildPriceLabel
   */
  public function testBuildPriceLabelFormatsAmountAndCurrency(): void {
    $node = $this->createOfferNode('1250000', 'EUR');
    $builder = new SearchMapMarkerBuilder();

    $this->assertSame('1 250 000 €', $builder->buildPriceLabel($node));
  }

  /**
   * @covers ::buildPriceLabel
   */
  public function testBuildPriceLabelReturnsNcWhenBudgetMissing(): void {
    $node = $this->createMock(NodeInterface::class);
    $node->method('hasField')->with('field_budget_value')->willReturn(FALSE);

    $builder = new SearchMapMarkerBuilder();
    $this->assertSame('NC', $builder->buildPriceLabel($node));
  }

  /**
   * @covers ::buildPriceLabelFromValues
   */
  public function testBuildPriceLabelFromValuesUsesIndexedData(): void {
    $builder = new SearchMapMarkerBuilder();

    $this->assertSame('1 250 000 €', $builder->buildPriceLabelFromValues('1250000', 'EUR'));
    $this->assertSame('NC', $builder->buildPriceLabelFromValues(NULL, 'EUR'));
    $this->assertSame('NC', $builder->buildPriceLabelFromValues(0, 'EUR'));
  }

  /**
   * @covers ::buildPriceMarkerSvg
   */
  public function testDefaultMarkerUsesSingleBubblePath(): void {
    $builder = new SearchMapMarkerBuilder();
    $svg = $builder->buildPriceMarkerSvg('450 €');

    $this->assertStringContainsString('<path d="', $svg);
    $this->assertStringNotContainsString('<rect', $svg);
    $this->assertStringNotContainsString('<polygon', $svg);
    $this->assertStringContainsString('fill="#FFFFFF"', $svg);
    $this->assertStringContainsString('fill="#00915A"', $svg);
    $this->assertStringContainsString('stroke="#00915A"', $svg);
    $this->assertStringContainsString('450 €', $svg);
    $this->assertStringContainsString('<circle', $svg);
  }

  /**
   * @covers ::buildPriceMarkerSvg
   */
  public function testActiveMarkerInvertsColors(): void {
    $builder = new SearchMapMarkerBuilder();
    $svg = $builder->buildPriceMarkerSvg('450 €', TRUE);

    $this->assertStringContainsString('fill="#00915A"', $svg);
    $this->assertStringContainsString('fill="#FFFFFF"', $svg);
  }

  /**
   * Builds a mocked offer node with budget field values.
   */
  private function createOfferNode(string $value, string $currency): NodeInterface {
    $budgetField = new TestFieldItemStub($value);
    $currencyField = new TestFieldItemStub($currency);

    $node = $this->createMock(NodeInterface::class);
    $node->method('hasField')->willReturnMap([
      ['field_budget_value', TRUE],
      ['field_budget_currency', TRUE],
    ]);
    $node->method('get')->willReturnMap([
      ['field_budget_value', $budgetField],
      ['field_budget_currency', $currencyField],
    ]);

    return $node;
  }

}
