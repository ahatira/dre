<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_favorite\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\ps_favorite\Controller\FavoriteToggleController;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\Request;

/**
 * Kernel tests for favorite toggle CSRF validation.
 */
#[Group('ps_favorite')]
#[RunTestsInSeparateProcesses]
final class FavoriteToggleControllerKernelTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'field',
    'text',
    'filter',
    'ps_core',
    'ps_favorite',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installSchema('node', ['node_access']);
    $this->installSchema('ps_favorite', ['ps_favorite_item']);
    $this->installConfig(['node', 'ps_favorite']);

    NodeType::create([
      'type' => 'offer',
      'name' => 'Offer',
    ])->save();

    Role::create([
      'id' => 'favorite_tester',
      'label' => 'Favorite tester',
    ])->grantPermission('access content')->save();

    $user = User::create([
      'name' => 'favorite_tester',
      'mail' => 'favorite_tester@example.com',
      'status' => 1,
    ]);
    $user->addRole('favorite_tester');
    $user->save();
    $this->container->get('account_switcher')->switchTo($user);

    $this->container->get('entity_type.manager')
      ->getStorage('ps_favorite_target')
      ->create([
        'id' => 'node.offer',
        'label' => 'Offer',
        'entity_type_id' => 'node',
        'bundle' => 'offer',
        'max_favorites' => 0,
        'view_mode' => 'card_favorite',
        'status' => TRUE,
      ])
      ->save();
  }

  public function testRouteSpecificTokenIsRequired(): void {
    $node = Node::create([
      'type' => 'offer',
      'title' => 'Favorite CSRF Offer',
      'status' => 1,
    ]);
    $node->save();

    $controller = new FavoriteToggleController(
      $this->container->get('ps_favorite.manager'),
      $this->container->get('entity_type.manager'),
      $this->container->get('csrf_token'),
    );

    $routeToken = $this->container->get('csrf_token')->get('ps_favorite.toggle');
    $sessionToken = $this->container->get('csrf_token')->get('session');

    $acceptedResponse = $controller->toggle(
      Request::create('/favorites/toggle/node/' . $node->id(), 'POST', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_X_CSRF_TOKEN' => $routeToken,
      ]),
      'node',
      (int) $node->id(),
    );

    $this->assertSame(200, $acceptedResponse->getStatusCode());
    $acceptedPayload = json_decode((string) $acceptedResponse->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);
    $this->assertTrue($acceptedPayload['isFavorite']);
    $this->assertSame(1, $acceptedPayload['count']);

    $rejectedResponse = $controller->toggle(
      Request::create('/favorites/toggle/node/' . $node->id(), 'POST', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_X_CSRF_TOKEN' => $sessionToken,
      ]),
      'node',
      (int) $node->id(),
    );

    $this->assertSame(403, $rejectedResponse->getStatusCode());
    $rejectedPayload = json_decode((string) $rejectedResponse->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);
    $this->assertSame('Invalid CSRF token.', $rejectedPayload['message']);
  }

}