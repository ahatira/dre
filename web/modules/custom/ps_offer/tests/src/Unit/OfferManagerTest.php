<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\ps_offer\Service\OfferManager;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Unit tests for OfferManager service.
 *
 * @coversDefaultClass \Drupal\ps_offer\Service\OfferManager
 * @group ps_offer
 */
class OfferManagerTest extends UnitTestCase {

  /**
   * Mock entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Mock logger factory.
   */
  protected LoggerChannelFactoryInterface $loggerFactory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->loggerFactory = $this->createMock(LoggerChannelFactoryInterface::class);
  }

  /**
   * Test OfferManager instantiation.
   */
  public function testOfferManagerInstantiation(): void {
    $manager = new OfferManager($this->entityTypeManager, $this->loggerFactory);
    $this->assertInstanceOf(OfferManager::class, $manager);
  }

  /**
   * Test that createOffer returns an unsaved node.
   */
  public function testCreateOfferReturnsUnsavedNode(): void {
    $manager = new OfferManager($this->entityTypeManager, $this->loggerFactory);
    $offer = $manager->createOffer(['title' => 'Test Offer']);

    $this->assertNotNull($offer);
    $this->assertNull($offer->id());
    $this->assertEquals('Test Offer', $offer->getTitle());
  }

}
