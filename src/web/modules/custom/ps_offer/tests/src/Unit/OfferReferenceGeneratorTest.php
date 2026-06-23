<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\ps_offer\Entity\OfferReferencePattern;
use Drupal\ps_offer\Service\OfferReferenceAliasResolverInterface;
use Drupal\ps_offer\Service\OfferReferenceGenerator;
use Drupal\Tests\UnitTestCase;

final class OfferReferenceGeneratorTest extends UnitTestCase {

  public function testGeneratorBuildsConfiguredReference(): void {
    $time = $this->createMock(TimeInterface::class);
    $time->method('getCurrentTime')->willReturn(strtotime('2026-05-20 12:00:00 UTC'));
    $aliasResolver = $this->createMock(OfferReferenceAliasResolverInterface::class);
    $aliasResolver->method('resolve')->willReturn(NULL);

    $generator = new OfferReferenceGenerator($time, $aliasResolver);

    $pattern = new OfferReferencePattern([
      'id' => 'default',
      'label' => 'Default',
      'segments' => [
        [
          'label' => 'Offer type',
          'type' => 'literal',
          'weight' => 0,
          'length' => 1,
          'fallback_value' => 'O',
        ],
        [
          'label' => 'Operation code',
          'type' => 'field_map',
          'weight' => 10,
          'length' => 1,
          'source_field' => 'field_operation_type',
          'resolution_mode' => 'manual_then_alias',
          'alias_set_ids' => [],
          'mapping' => ['RENT' => 'L', 'SALE' => 'V'],
          'fallback_value' => '',
        ],
        [
          'label' => 'Asset code',
          'type' => 'field_map',
          'weight' => 20,
          'length' => 3,
          'source_field' => 'field_asset_type',
          'resolution_mode' => 'manual_then_alias',
          'alias_set_ids' => [],
          'mapping' => ['BUR' => 'BUR', 'ENT' => 'ENT'],
          'fallback_value' => '',
        ],
        [
          'label' => 'Creation year',
          'type' => 'year_2_digits',
          'weight' => 30,
          'length' => 2,
          'fallback_value' => '',
        ],
        [
          'label' => 'Counter',
          'type' => 'counter',
          'weight' => 40,
          'length' => 5,
          'fallback_value' => '',
        ],
      ],
    ], 'ps_offer_reference_pattern');

    $reference = $generator->generate($pattern, [
      'field_operation_type' => 'RENT',
      'field_asset_type' => 'BUR',
    ], 1);

    $this->assertSame('OLBUR2600001', $reference);
  }

  public function testGeneratorUsesFallbackWhenMappingIsMissing(): void {
    $time = $this->createMock(TimeInterface::class);
    $time->method('getCurrentTime')->willReturn(strtotime('2026-05-20 12:00:00 UTC'));
    $aliasResolver = $this->createMock(OfferReferenceAliasResolverInterface::class);
    $aliasResolver->method('resolve')->willReturn(NULL);

    $generator = new OfferReferenceGenerator($time, $aliasResolver);

    $pattern = new OfferReferencePattern([
      'id' => 'fallback',
      'label' => 'Fallback',
      'segments' => [
        [
          'label' => 'Operation code',
          'type' => 'field_map',
          'weight' => 0,
          'length' => 1,
          'source_field' => 'field_operation_type',
          'resolution_mode' => 'manual_then_alias',
          'alias_set_ids' => [],
          'mapping' => ['RENT' => 'L'],
          'fallback_value' => 'X',
        ],
      ],
    ], 'ps_offer_reference_pattern');

    $reference = $generator->generate($pattern, [
      'field_operation_type' => 'LEASE',
    ], 1);

    $this->assertSame('X', $reference);
  }

  public function testGeneratorUsesAliasBeforeManualMappingWhenConfigured(): void {
    $time = $this->createMock(TimeInterface::class);
    $time->method('getCurrentTime')->willReturn(strtotime('2026-05-20 12:00:00 UTC'));
    $aliasResolver = $this->createMock(OfferReferenceAliasResolverInterface::class);
    $aliasResolver
      ->expects($this->once())
      ->method('resolve')
      ->willReturn('LOG');

    $generator = new OfferReferenceGenerator($time, $aliasResolver);

    $pattern = new OfferReferencePattern([
      'id' => 'alias',
      'label' => 'Alias',
      'segments' => [
        [
          'label' => 'Asset code',
          'type' => 'field_map',
          'weight' => 0,
          'length' => 3,
          'source_field' => 'field_asset_type',
          'resolution_mode' => 'alias_then_manual',
          'alias_set_ids' => ['default'],
          'mapping' => ['ENT' => 'WAR'],
          'fallback_value' => '',
        ],
      ],
    ], 'ps_offer_reference_pattern');

    $reference = $generator->generate($pattern, [
      'field_asset_type' => 'ENT',
    ], 1);

    $this->assertSame('LOG', $reference);
  }

  public function testGeneratorThrowsWhenSegmentExceedsLength(): void {
    $time = $this->createMock(TimeInterface::class);
    $time->method('getCurrentTime')->willReturn(strtotime('2026-05-20 12:00:00 UTC'));
    $aliasResolver = $this->createMock(OfferReferenceAliasResolverInterface::class);
    $aliasResolver->method('resolve')->willReturn(NULL);

    $generator = new OfferReferenceGenerator($time, $aliasResolver);

    $pattern = new OfferReferencePattern([
      'id' => 'invalid',
      'label' => 'Invalid',
      'segments' => [
        [
          'label' => 'Operation code',
          'type' => 'field_map',
          'weight' => 0,
          'length' => 1,
          'source_field' => 'field_operation_type',
          'resolution_mode' => 'manual_then_alias',
          'alias_set_ids' => [],
          'mapping' => ['RENT' => 'LONG'],
          'fallback_value' => '',
        ],
      ],
    ], 'ps_offer_reference_pattern');

    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Segment "Operation code" exceeds its configured length of 1.');

    $generator->generate($pattern, [
      'field_operation_type' => 'RENT',
    ], 1);
  }

}