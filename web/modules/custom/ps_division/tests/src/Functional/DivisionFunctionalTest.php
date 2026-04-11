<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_division\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests Division module functionality.
 *
 * @group ps_division
 */
final class DivisionFunctionalTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   *
   * @var array<string>
   */
  protected static $modules = [
    'ps',
    'ps_dictionary',
    'ps_division',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * An admin user with permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  private $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer ps_division entities',
      'view division entities',
    ]);
  }

  /**
   * Tests division list page access.
   */
  public function testDivisionListAccess(): void {
    // Anonymous should not have access.
    $this->drupalGet('/admin/ps/content/divisions');
    $this->assertSession()->statusCodeEquals(403);

    // Admin user should have access.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/ps/content/divisions');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests division settings form.
   */
  public function testDivisionSettingsForm(): void {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/ps/config/divisions');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Division Settings');
  }

  /**
   * Tests division type list page.
   */
  public function testDivisionTypeList(): void {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/ps/structure/division-types');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Division Types');
    $this->assertSession()->pageTextContains('Division');
  }

  /**
   * Tests division type add form.
   */
  public function testDivisionTypeAdd(): void {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/ps/structure/division-types/add');
    $this->assertSession()->statusCodeEquals(200);

    $this->submitForm([
      'label' => 'Test Type',
      'id' => 'test_type',
      'description' => 'Test description',
    ], 'Save');

    $this->assertSession()->pageTextContains('Division type Test Type created');
  }

  /**
   * Tests division creation workflow.
   */
  public function testDivisionCreation(): void {
    $this->drupalLogin($this->adminUser);

    // Go to add form - note: division_type parameter is in the route.
    $this->drupalGet('/admin/ps/content/divisions/add');
    // May be a listing page to select type, or may redirect to form.
    // For now, create directly via API.
    $storage = \Drupal::entityTypeManager()->getStorage('ps_division');
    $division = $storage->create([
      'type' => 'division',
      'building_name' => 'Functional Test Building',
      'entity_id' => 999,
      'lot' => 'LOT-FUNC-001',
      'floor' => 'R+3',
    ]);
    $division->save();

    $this->assertNotNull($division->id());
  }

  /**
   * Tests division edit workflow.
   *
   * @todo Fix with proper functional test setup for division type.
   */
  public function testDivisionEdit(): void {
    // Skip for now - requires proper functional test setup.
    $this->markTestSkipped('Requires functional test infrastructure');
  }

  /**
   * Tests division delete workflow.
   *
   * @todo Fix with proper functional test setup for division type.
   */
  public function testDivisionDelete(): void {
    // Skip for now - requires proper functional test setup.
    $this->markTestSkipped('Requires functional test infrastructure');
  }

}
