<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests ps_search location autocomplete endpoint.
 *
 * @group ps_search
 */
final class LocationAutocompleteControllerTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field',
    'text',
    'address',
    'ps_offer',
    'ps_search',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Verifies endpoint returns JSON payload shape.
   */
  public function testEndpointReturnsJsonShape(): void {
    $this->drupalGet('/ps-search/location-autocomplete', ['query' => ['q' => 'Pa', 'limit' => 8]]);

    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseHeaderEquals('Content-Type', 'application/json');

    $payload = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    $this->assertIsArray($payload);
    $this->assertArrayHasKey('items', $payload);
    $this->assertIsArray($payload['items']);

    foreach ($payload['items'] as $item) {
      $this->assertIsArray($item);
      $this->assertArrayHasKey('value', $item);
      $this->assertArrayHasKey('label', $item);
      $this->assertIsString($item['value']);
      $this->assertIsString($item['label']);
    }
  }

  /**
   * Verifies too-short query returns an empty list.
   */
  public function testShortQueryReturnsNoSuggestions(): void {
    $this->drupalGet('/ps-search/location-autocomplete', ['query' => ['q' => 'P']]);

    $this->assertSession()->statusCodeEquals(200);
    $payload = json_decode($this->getSession()->getPage()->getContent(), TRUE);
    $this->assertSame(['items' => []], $payload);
  }

}
