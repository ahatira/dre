<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_feature\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_feature\Entity\FeatureGroup;
use Drupal\ps_feature\Service\FeatureGroupIconResolver;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_feature\Service\FeatureGroupIconResolver
 * @group ps_feature
 */
final class FeatureGroupIconResolverTest extends UnitTestCase {

  /**
   * @covers ::resolveParts
   */
  public function testResolvePartsUsesGroupIcon(): void {
    $group = $this->createMock(FeatureGroup::class);
    $group->method('getIcon')->willReturn('bnp_custom:equipement');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with('equipements')->willReturn($group);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_group')->willReturn($storage);

    $settings = $this->createMock(ImmutableConfig::class);
    $settings->method('get')->with('default_group_icon')->willReturn('bnp_custom:infos');
    $settings->method('getCacheTags')->willReturn(['config:ps_feature.settings']);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_feature.settings')->willReturn($settings);

    $resolver = new FeatureGroupIconResolver($configFactory, $entityTypeManager);
    $parts = $resolver->resolveParts('equipements');

    $this->assertSame('bnp_custom', $parts['pack']);
    $this->assertSame('equipement', $parts['id']);
  }

  /**
   * @covers ::resolveParts
   */
  public function testResolvePartsUsesDefaultWhenGroupHasNoIcon(): void {
    $group = $this->createMock(FeatureGroup::class);
    $group->method('getIcon')->willReturn('');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with('amenagements')->willReturn($group);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_group')->willReturn($storage);

    $settings = $this->createMock(ImmutableConfig::class);
    $settings->method('get')->with('default_group_icon')->willReturn('bnp_custom:medal');
    $settings->method('getCacheTags')->willReturn(['config:ps_feature.settings']);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_feature.settings')->willReturn($settings);

    $resolver = new FeatureGroupIconResolver($configFactory, $entityTypeManager);
    $parts = $resolver->resolveParts('amenagements');

    $this->assertSame('medal', $parts['id']);
  }

  /**
   * @covers ::resolveParts
   */
  public function testResolvePartsUsesDefaultForOtherGroup(): void {
    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);

    $settings = $this->createMock(ImmutableConfig::class);
    $settings->method('get')->with('default_group_icon')->willReturn('bnp_custom:not-available');
    $settings->method('getCacheTags')->willReturn(['config:ps_feature.settings']);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_feature.settings')->willReturn($settings);

    $resolver = new FeatureGroupIconResolver($configFactory, $entityTypeManager);
    $parts = $resolver->resolveParts('_other');

    $this->assertSame('not-available', $parts['id']);
  }

}
