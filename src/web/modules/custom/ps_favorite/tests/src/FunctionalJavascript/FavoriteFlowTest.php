<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_favorite\FunctionalJavascript;

use Drupal\Core\Url;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * End-to-end tests for favorite flow in front and account pages.
 */
#[Group('ps_favorite')]
#[RunTestsInSeparateProcesses]
final class FavoriteFlowTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'user',
    'block',
    'ps_core',
    'ps_favorite',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  protected function setUp(): void {
    parent::setUp();

    if (!NodeType::load('offer')) {
      NodeType::create([
        'type' => 'offer',
        'name' => 'Offer',
      ])->save();
    }

    $this->drupalPlaceBlock('ps_favorite_header_block', [
      'region' => 'header',
      'id' => 'ps_favorite_header_block_test',
    ]);
  }

  public function testToggleAndMergeAcrossLogin(): void {
    $node = Node::create([
      'type' => 'offer',
      'title' => 'Favorite E2E Offer',
      'status' => 1,
    ]);
    $node->save();

    $this->drupalGet($node->toUrl()->toString());
    $assert = $this->assertSession();
    $assert->waitForElementVisible('css', '[data-ps-favorite-toggle]');

    $button = $this->getSession()->getPage()->find('css', '[data-ps-favorite-toggle]');
    $this->assertNotNull($button);
    $button->click();

    $assert->waitForText('Added to favorites.');
    $assert->elementTextContains('css', '[data-ps-favorite-toggle] .js-ps-favorite-text', 'Saved');

    $this->drupalGet('/favorites/count');
    $assert->responseContains('"count":1');

    $user = $this->drupalCreateUser(['access content']);
    $this->drupalLogin($user);

    $this->drupalGet('/favorites');
    $assert->pageTextContains('Favorite E2E Offer');
  }

  public function testToggleUsesRouteSpecificCsrfToken(): void {
    $node = Node::create([
      'type' => 'offer',
      'title' => 'Favorite CSRF Offer',
      'status' => 1,
    ]);
    $node->save();

    $this->drupalGet($node->toUrl()->toString());
    $assert = $this->assertSession();
    $assert->waitForElementVisible('css', '[data-ps-favorite-toggle]');

    $settings = $this->getDrupalSettings();
    $this->assertArrayHasKey('psFavorite', $settings);
    $this->assertNotEmpty($settings['psFavorite']['csrfToken']);

    $toggleUrl = Url::fromRoute('ps_favorite.toggle', [
      'entity_type_id' => 'node',
      'entity_id' => $node->id(),
    ], ['absolute' => TRUE])->toString();

    $client = \Drupal::httpClient();

    $acceptedResponse = $client->post($toggleUrl, [
      'headers' => [
        'Accept' => 'application/json',
        'X-CSRF-Token' => $settings['psFavorite']['csrfToken'],
      ],
      'http_errors' => FALSE,
    ]);
    $this->assertSame(200, $acceptedResponse->getStatusCode());
    $acceptedPayload = json_decode((string) $acceptedResponse->getBody(), TRUE, 512, JSON_THROW_ON_ERROR);
    $this->assertTrue($acceptedPayload['isFavorite']);
    $this->assertSame(1, $acceptedPayload['count']);

    $rejectedResponse = $client->post($toggleUrl, [
      'headers' => [
        'Accept' => 'application/json',
        'X-CSRF-Token' => \Drupal::service('csrf_token')->get('session'),
      ],
      'http_errors' => FALSE,
    ]);
    $this->assertSame(403, $rejectedResponse->getStatusCode());
    $rejectedPayload = json_decode((string) $rejectedResponse->getBody(), TRUE, 512, JSON_THROW_ON_ERROR);
    $this->assertSame('Invalid CSRF token.', $rejectedPayload['message']);
  }

}
