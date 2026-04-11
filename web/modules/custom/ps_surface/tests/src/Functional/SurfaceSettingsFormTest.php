<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_surface\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests ps_surface settings form.
 *
 * @group ps_surface
 */
class SurfaceSettingsFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['ps', 'ps_dictionary', 'ps_surface'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that settings form is accessible.
   */
  public function testSettingsFormAccess(): void {
    // Create an admin user.
    $admin = $this->createUser(['administer ps_surface settings']);

    // Log in as admin.
    $this->drupalLogin($admin);

    // Navigate to settings page.
    $this->drupalGet('/admin/ps/config/surface');
    $this->assertSession()->statusCodeEquals(200);

    // Verify page title.
    $this->assertSession()->pageTextContains('Surface Settings');
  }

  /**
   * Tests validation settings can be configured.
   */
  public function testValidationSettingsConfiguration(): void {
    // Create an admin user.
    $admin = $this->createUser(['administer ps_surface settings']);

    // Log in as admin.
    $this->drupalLogin($admin);

    // Navigate to settings form.
    $this->drupalGet('/admin/ps/config/surface');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->fieldExists('Allow negative values');

    // Check initial state.
    $this->assertSession()->checkboxNotChecked('Allow negative values');

    // Enable negative values.
    $this->getSession()->getPage()->checkField('Allow negative values');
    $this->getSession()->getPage()->pressButton('Save');

    // Verify success message.
    $this->assertSession()->pageTextContains('Surface settings have been saved');

    // Reload page and verify setting persisted.
    $this->drupalGet('/admin/ps/config/surface');
    $this->assertSession()->checkboxChecked('Allow negative values');
  }

}
