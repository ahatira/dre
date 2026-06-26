<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_media\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_media\Service\MediaImportGovernance;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_media\Service\MediaImportGovernance
 */
#[CoversClass(MediaImportGovernance::class)]
#[Group('ps_media')]
final class MediaImportGovernanceTest extends UnitTestCase {

  /**
   * @covers ::shouldLockOnBoCreate
   */
  public function testShouldLockOnBoCreate(): void {
    $service = $this->buildService(['bo_create.default_internal_lock' => TRUE]);
    self::assertTrue($service->shouldLockOnBoCreate());
  }

  /**
   * @covers ::shouldUnpublishMissingMedia
   */
  public function testShouldUnpublishMissingMediaWhenProtectedAndConfigured(): void {
    $lockField = new class {

      public bool $value = TRUE;

    };
    $statusField = new class {

      public bool $value = TRUE;

    };
    $media = $this->createMock(ContentEntityInterface::class);
    $media->method('hasField')->with('field_internal_lock')->willReturn(TRUE);
    $media->method('get')->willReturnCallback(
      static fn(string $field) => match ($field) {
        'field_internal_lock' => $lockField,
        'status' => $statusField,
        default => NULL,
      },
    );

    $service = $this->buildService([
      'missing_from_xml.media_action' => 'unpublish',
      'missing_from_xml.protected_media_action' => 'unpublish',
    ]);

    self::assertTrue($service->shouldUnpublishMissingMedia($media, FALSE));
  }

  /**
   * @param array<string, mixed> $values
   */
  private function buildService(array $values): MediaImportGovernance {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnCallback(
      static fn(string $key, mixed $default = NULL): mixed => $values[$key] ?? $default,
    );

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with(MediaImportGovernance::CONFIG_NAME)->willReturn($config);

    $globalResolver = $this->createMock(ImportGovernanceGlobalResolver::class);
    $globalResolver->method('getGlobalLockStrategy')->willReturn('skip_field');

    return new MediaImportGovernance($configFactory, $globalResolver);
  }

}
