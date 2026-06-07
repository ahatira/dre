<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_feature\Unit\Service;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureDefinitionIconResolver;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_feature\Service\FeatureDefinitionIconResolver
 * @group ps_feature
 */
final class FeatureDefinitionIconResolverTest extends UnitTestCase {

  /**
   * @covers ::resolveParts
   */
  public function testResolvePartsReturnsIconWhenConfigured(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('getIcon')->willReturn('bnp_custom:bus-borders');

    $resolver = new FeatureDefinitionIconResolver($this->createMock(EntityTypeManagerInterface::class));
    $parts = $resolver->resolveParts($definition);

    $this->assertNotNull($parts);
    $this->assertSame('bnp_custom', $parts['pack']);
    $this->assertSame('bus-borders', $parts['id']);
  }

  /**
   * @covers ::resolveParts
   */
  public function testResolvePartsReturnsNullWhenNoIcon(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('getIcon')->willReturn('');

    $resolver = new FeatureDefinitionIconResolver($this->createMock(EntityTypeManagerInterface::class));
    $this->assertNull($resolver->resolveParts($definition));
  }

  /**
   * @covers ::buildRenderable
   */
  public function testBuildRenderableReturnsEmptyWhenNoIcon(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('getIcon')->willReturn('');

    $resolver = new FeatureDefinitionIconResolver($this->createMock(EntityTypeManagerInterface::class));
    $this->assertSame([], $resolver->buildRenderable($definition));
  }

  /**
   * @covers ::resolveParts
   */
  public function testResolvePartsLoadsDefinitionById(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('getIcon')->willReturn('bnp_custom:metro-borders');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with('metro_access')->willReturn($definition);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_definition')->willReturn($storage);

    $resolver = new FeatureDefinitionIconResolver($entityTypeManager);
    $parts = $resolver->resolveParts('metro_access');

    $this->assertNotNull($parts);
    $this->assertSame('metro-borders', $parts['id']);
  }

}
