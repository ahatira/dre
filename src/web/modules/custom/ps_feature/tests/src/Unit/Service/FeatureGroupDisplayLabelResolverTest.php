<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_feature\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Config\LanguageConfigOverride;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_feature\Entity\FeatureGroup;
use Drupal\ps_feature\Service\FeatureGroupDisplayLabelResolver;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_feature\Service\FeatureGroupDisplayLabelResolver
 * @group ps_feature
 */
final class FeatureGroupDisplayLabelResolverTest extends UnitTestCase {

  /**
   * @covers ::resolve
   */
  public function testResolveUsesInstallLabelWhenEntityIsMachineName(): void {
    $group = $this->createMock(FeatureGroup::class);
    $group->method('id')->willReturn('amenagements');
    $group->method('label')->willReturn('AMENAGEMENTS');

    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->with('label')->willReturn('AMENAGEMENTS');

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_feature.feature_group.amenagements')->willReturn($config);

    $language = new Language(['id' => 'en']);
    $languageManager = $this->createMock(LanguageManagerInterface::class);
    $languageManager->method('getCurrentLanguage')->willReturn($language);

    $moduleList = $this->createMock(ModuleExtensionList::class);
    $moduleList->method('getPath')->with('ps_feature')->willReturn('modules/custom/ps_feature');

    $installDir = DRUPAL_ROOT . '/modules/custom/ps_feature/config/install';
    if (!is_dir($installDir)) {
      $this->markTestSkipped('Install config directory not available.');
    }

    $resolver = new FeatureGroupDisplayLabelResolver($configFactory, $languageManager, $moduleList);
    $this->assertSame('Fittings', $resolver->resolve($group));
  }

  /**
   * @covers ::resolve
   */
  public function testResolveUsesFrenchOverride(): void {
    $group = $this->createMock(FeatureGroup::class);
    $group->method('id')->willReturn('equipment');
    $group->method('label')->willReturn('EQUIPMENT');

    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->with('label')->willReturn('EQUIPMENT');

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_feature.feature_group.equipment')->willReturn($config);

    $language = new Language(['id' => 'fr']);
    $languageManager = $this->createMock(LanguageManagerInterface::class);
    $languageManager->method('getCurrentLanguage')->willReturn($language);

    $override = $this->createMock(LanguageConfigOverride::class);
    $override->method('get')->with('label')->willReturn('Équipements');
    $languageManager->method('getLanguageConfigOverride')
      ->with('fr', 'ps_feature.feature_group.equipment')
      ->willReturn($override);

    $moduleList = $this->createMock(ModuleExtensionList::class);
    $moduleList->method('getPath')->with('ps_feature')->willReturn('modules/custom/ps_feature');

    $resolver = new FeatureGroupDisplayLabelResolver($configFactory, $languageManager, $moduleList);
    $this->assertSame('Équipements', $resolver->resolve($group));
  }

  /**
   * @covers ::isMachineLabel
   * @dataProvider machineLabelProvider
   */
  public function testIsMachineLabel(string $label, string $id, bool $expected): void {
    $resolver = new FeatureGroupDisplayLabelResolver(
      $this->createMock(ConfigFactoryInterface::class),
      $this->createMock(LanguageManagerInterface::class),
      $this->createMock(ModuleExtensionList::class),
    );
    $this->assertSame($expected, $resolver->isMachineLabel($label, $id));
  }

  /**
   * @return array<string, array{0: string, 1: string, 2: bool}>
   */
  public static function machineLabelProvider(): array {
    return [
      'screaming snake' => ['AMENAGEMENTS', 'amenagements', TRUE],
      'human label' => ['Fittings', 'amenagements', FALSE],
      'id-like label' => ['equipment', 'equipment', TRUE],
    ];
  }

}
