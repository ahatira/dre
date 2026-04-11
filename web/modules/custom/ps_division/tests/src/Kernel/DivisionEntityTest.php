<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_division\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_division\Entity\Division;
use Drupal\ps_division\Entity\DivisionType;

/**
 * Tests Division entity CRUD operations.
 *
 * @group ps_division
 */
final class DivisionEntityTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   *
   * @var array<string>
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'text',
    'views',
    'ps',
    'ps_dictionary',
    'ps_division',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('ps_division');
    $this->installConfig(['ps_division']);
    $this->installSchema('system', ['sequences']);

    // Create division type.
    $type = DivisionType::create([
      'id' => 'test_type',
      'label' => 'Test Division Type',
      'description' => 'Test type for kernel tests',
    ]);
    $type->save();
  }

  /**
   * Tests division entity creation.
   */
  public function testDivisionCreation(): void {
    $division = Division::create([
      'type' => 'test_type',
      'building_name' => 'Test Building',
      'lot' => 'LOT-TEST',
    ]);

    $this->assertInstanceOf(Division::class, $division);
    $this->assertTrue($division->isNew());
  }

  /**
   * Tests division entity save and load.
   */
  public function testDivisionSaveAndLoad(): void {
    $division = Division::create([
      'type' => 'test_type',
      'building_name' => 'Building Alpha',
      'lot' => 'LOT-ALPHA',
    ]);

    $result = $division->save();
    $this->assertEquals(SAVED_NEW, $result);

    $id = $division->id();
    $this->assertNotNull($id);

    $loaded = Division::load($id);
    $this->assertInstanceOf(Division::class, $loaded);
    $this->assertEquals('Building Alpha', $loaded->getBuildingName());
    $this->assertEquals('LOT-ALPHA', $loaded->getLot());
  }

  /**
   * Tests division entity getters and setters.
   */
  public function testDivisionGettersSetters(): void {
    $division = Division::create(['type' => 'test_type']);

    $division->setBuildingName('New Building');
    $this->assertEquals('New Building', $division->getBuildingName());

    $division->setLot('LOT-999');
    $this->assertEquals('LOT-999', $division->getLot());

    $division->setAvailability('Coming soon');
    $this->assertEquals('Coming soon', $division->getAvailability());
  }

  /**
   * Tests division total surface calculation.
   */
  public function testDivisionTotalSurface(): void {
    $division = Division::create(['type' => 'test_type']);

    // Without surfaces field, should return 0.
    $total = $division->getTotalSurface();
    $this->assertEquals(0.0, $total);
  }

  /**
   * Tests division entity label.
   */
  public function testDivisionLabel(): void {
    $division = Division::create([
      'type' => 'test_type',
      'building_name' => 'Label Test Building',
    ]);

    $this->assertEquals('Label Test Building', $division->label());
  }

}
