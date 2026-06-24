<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_agent\Entity\AgentInterface;
use Drupal\ps_offer\Service\OfferContactResolver;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoverClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @coversDefaultClass \Drupal\ps_offer\Service\OfferContactResolver
 */
#[CoverClass(OfferContactResolver::class)]
#[Group('ps_offer')]
final class OfferContactResolverTest extends UnitTestCase {

  /**
   * @covers ::resolveAgent
   */
  public function testResolveAgentPrefersPrimaryOverSecondaryAndDefault(): void {
    $primary = $this->createAgent(1, 'primary@example.com');
    $secondary = $this->createAgent(2, 'secondary@example.com');
    $default = $this->createAgent(3, 'default@example.com');

    $offer = $this->createMock(NodeInterface::class);
    $offer->method('bundle')->willReturn('offer');
    $offer->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_primary_agent', 'field_secondary_agents'], TRUE));
    $offer->method('get')->willReturnCallback(function (string $field) use ($primary, $secondary): EntityReferenceFieldItemListInterface&MockObject {
      if ($field === 'field_primary_agent') {
        return $this->referenceFieldWithEntities([$primary]);
      }
      return $this->referenceFieldWithEntities([$secondary]);
    });

    $resolver = new OfferContactResolver(
      $this->configFactoryWithDefaultAgent(3),
      $this->entityTypeManagerWithAgent($default),
    );

    $this->assertSame($primary, $resolver->resolveAgent($offer));
    $this->assertSame('primary@example.com', $resolver->resolveContactEmail($offer));
  }

  /**
   * @covers ::resolveAgent
   */
  public function testResolveAgentUsesSecondaryWhenPrimaryMissing(): void {
    $secondary = $this->createAgent(2, 'secondary@example.com');

    $offer = $this->createMock(NodeInterface::class);
    $offer->method('bundle')->willReturn('offer');
    $offer->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_primary_agent', 'field_secondary_agents'], TRUE));
    $offer->method('get')->willReturnCallback(function (string $field) use ($secondary): EntityReferenceFieldItemListInterface&MockObject {
      return match ($field) {
        'field_primary_agent' => $this->emptyReferenceField(),
        'field_secondary_agents' => $this->referenceFieldWithEntities([$secondary]),
        default => $this->emptyReferenceField(),
      };
    });

    $resolver = new OfferContactResolver(
      $this->configFactoryWithDefaultAgent(0),
      $this->entityTypeManagerWithAgent(NULL),
    );

    $this->assertSame($secondary, $resolver->resolveAgent($offer));
  }

  /**
   * @covers ::resolveContactEmail
   */
  public function testResolveContactEmailFallsBackToSiteMail(): void {
    $offer = $this->createMock(NodeInterface::class);
    $offer->method('bundle')->willReturn('offer');
    $offer->method('hasField')->willReturn(FALSE);

    $resolver = new OfferContactResolver(
      $this->configFactoryWithDefaultAgent(0, 'site@example.com'),
      $this->entityTypeManagerWithAgent(NULL),
    );

    $this->assertSame('site@example.com', $resolver->resolveContactEmail($offer));
    $this->assertFalse($resolver->hasResolvedAgent($offer));
  }

  private function createAgent(int $id, string $email): AgentInterface&MockObject {
    $agent = $this->createMock(AgentInterface::class);
    $agent->method('id')->willReturn($id);
    $agent->method('getEmail')->willReturn($email);
    $agent->method('hasField')->willReturn(FALSE);
    return $agent;
  }

  /**
   * @param list<AgentInterface> $entities
   *
   * @return EntityReferenceFieldItemListInterface&MockObject
   */
  private function referenceFieldWithEntities(array $entities): EntityReferenceFieldItemListInterface&MockObject {
    $field = $this->createMock(EntityReferenceFieldItemListInterface::class);
    $field->method('isEmpty')->willReturn($entities === []);
    $field->method('referencedEntities')->willReturn($entities);
    return $field;
  }

  /**
   * @return EntityReferenceFieldItemListInterface&MockObject
   */
  private function emptyReferenceField(): EntityReferenceFieldItemListInterface&MockObject {
    $field = $this->createMock(EntityReferenceFieldItemListInterface::class);
    $field->method('isEmpty')->willReturn(TRUE);
    return $field;
  }

  private function configFactoryWithDefaultAgent(int $agent_id, string $site_mail = ''): ConfigFactoryInterface&MockObject {
    $offer_settings = $this->createMock(ImmutableConfig::class);
    $offer_settings->method('get')->willReturnCallback(static fn (string $key): mixed => match ($key) {
      'default_contact_agent' => $agent_id,
      default => NULL,
    });

    $site_settings = $this->createMock(ImmutableConfig::class);
    $site_settings->method('get')->willReturnCallback(static fn (string $key): mixed => match ($key) {
      'mail' => $site_mail,
      default => NULL,
    });

    $config_factory = $this->createMock(ConfigFactoryInterface::class);
    $config_factory->method('get')->willReturnCallback(static fn (string $name): ImmutableConfig&MockObject => match ($name) {
      'ps_offer.settings' => $offer_settings,
      'system.site' => $site_settings,
      default => $this->createMock(ImmutableConfig::class),
    });

    return $config_factory;
  }

  private function entityTypeManagerWithAgent(?AgentInterface $agent): EntityTypeManagerInterface&MockObject {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->willReturn($agent);

    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_type_manager->method('getStorage')->with('ps_agent')->willReturn($storage);

    return $entity_type_manager;
  }

}
