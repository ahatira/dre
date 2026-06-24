<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Config\StorableConfigBase;
use Drupal\ps_offer\Service\OfferAddressCountryConfigurator;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoverClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Drupal\ps_offer\Service\OfferAddressCountryConfigurator
 */
#[CoverClass(OfferAddressCountryConfigurator::class)]
#[Group('ps_offer')]
final class OfferAddressCountryConfiguratorTest extends UnitTestCase {

  /**
   * @covers ::resolveAvailableCountryCodes
   */
  #[DataProvider('countryCodeProvider')]
  public function testResolveAvailableCountryCodes(string $psCountryCode, array $expected): void {
    $configurator = new OfferAddressCountryConfigurator(
      $this->createMock(ConfigFactoryInterface::class),
    );

    $this->assertSame($expected, $configurator->resolveAvailableCountryCodes($psCountryCode));
  }

  /**
   * @return iterable<string, array{0: string, 1: string[]}>
   */
  public static function countryCodeProvider(): iterable {
    yield 'France' => ['fr', ['FR']];
    yield 'Spain uppercase input' => ['ES', ['ES']];
    yield 'International' => ['com', []];
    yield 'Empty string' => ['', []];
  }

  /**
   * @covers ::applyForCountry
   */
  public function testApplyForCountryUpdatesConfigWhenDifferent(): void {
    $editable = $this->createEditableConfig(['FR']);
    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('getEditable')
      ->with('field.field.node.offer.field_address')
      ->willReturn($editable);

    $configurator = new OfferAddressCountryConfigurator($configFactory);
    $this->assertTrue($configurator->applyForCountry('es'));
    $this->assertSame(['ES'], $editable->get('settings.available_countries'));
  }

  /**
   * @covers ::applyForCountry
   */
  public function testApplyForCountrySkipsWhenAlreadyMatching(): void {
    $editable = $this->createEditableConfig(['ES']);
    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('getEditable')
      ->with('field.field.node.offer.field_address')
      ->willReturn($editable);

    $configurator = new OfferAddressCountryConfigurator($configFactory);
    $this->assertFalse($configurator->applyForCountry('es'));
  }

  /**
   * @param string[] $availableCountries
   */
  private function createEditableConfig(array $availableCountries): StorableConfigBase&MockObject {
    $state = ['available_countries' => $availableCountries];

    $config = $this->getMockBuilder(StorableConfigBase::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['get', 'set', 'save', 'isNew'])
      ->getMockForAbstractClass();

    $config->method('isNew')->willReturn(FALSE);
    $config->method('set')
      ->willReturnCallback(static function (string $key, mixed $value) use ($config, &$state): StorableConfigBase&MockObject {
        if ($key === 'settings.available_countries' && is_array($value)) {
          $state['available_countries'] = $value;
        }
        return $config;
      });
    $config->method('get')
      ->willReturnCallback(static function (string $key) use (&$state): mixed {
        return match ($key) {
          'settings.available_countries' => $state['available_countries'],
          default => NULL,
        };
      });

    return $config;
  }

}
