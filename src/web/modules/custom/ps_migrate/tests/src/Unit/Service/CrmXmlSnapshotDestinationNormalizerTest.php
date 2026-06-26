<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\ps_migrate\Service\CrmXmlSnapshotDestinationNormalizer;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_migrate\Service\CrmXmlSnapshotDestinationNormalizer
 */
#[CoversClass(CrmXmlSnapshotDestinationNormalizer::class)]
#[Group('ps_migrate')]
final class CrmXmlSnapshotDestinationNormalizerTest extends UnitTestCase {

  /**
   * @covers ::normalize
   */
  public function testCollapsesSlashSeparatedDestinationProperties(): void {
    $normalizer = new CrmXmlSnapshotDestinationNormalizer();

    self::assertSame(
      [
        'title' => 'Offer title',
        'field_business_id' => 'ABC123',
        'body' => [
          'value' => 'Description',
          'format' => 'plain_text',
        ],
        'field_media_image' => [
          'target_id' => 42,
          'alt' => 'Facade',
        ],
      ],
      $normalizer->normalize([
        'title' => 'Offer title',
        'field_business_id' => 'ABC123',
        'body/value' => 'Description',
        'body/format' => 'plain_text',
        'field_media_image/target_id' => 42,
        'field_media_image/alt' => 'Facade',
      ]),
    );
  }

}
