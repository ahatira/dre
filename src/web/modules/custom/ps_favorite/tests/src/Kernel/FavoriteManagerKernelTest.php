<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_favorite\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\user\Entity\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Kernel tests for favorite manager storage and limits.
 */
#[Group('ps_favorite')]
#[RunTestsInSeparateProcesses]
final class FavoriteManagerKernelTest extends KernelTestBase {

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
  }

  public function testAnonymousMergeOnLogin(): void {
    $manager = $this->container->get('ps_favorite.manager');
    $accountProxy = $this->container->get('current_user');
    $cookieState = $this->container->get('ps_favorite.cookie_state');
    $repository = $this->container->get('ps_favorite.repository');

    $node = Node::create([
      'type' => 'offer',
      'title' => 'Offer 1',
    ]);
    $node->save();

    $this->assertTrue($manager->addFavorite($node));
    $this->assertSame(1, $manager->getFavoritesCount('node'));

    $user = User::create([
      'name' => 'favorite_kernel_user',
      'mail' => 'favorite_kernel_user@example.com',
      'status' => 1,
    ]);
    $user->save();

    $accountProxy->setAccount($user);
    $manager->mergeAnonymousFavorites('node');

    $this->assertTrue($repository->has((int) $user->id(), 'node', (int) $node->id()));
    $this->assertContains('node', $cookieState->getClearedEntityTypes());
  }

  public function testMaxFavoritesLimitByBundle(): void {
    $manager = $this->container->get('ps_favorite.manager');
    $this->container->get('config.factory')->getEditable('ps_favorite.settings')
      ->set('max_favorites_map', 'node.offer:1')
      ->save();

    $nodeA = Node::create([
      'type' => 'offer',
      'title' => 'Offer A',
    ]);
    $nodeA->save();

    $nodeB = Node::create([
      'type' => 'offer',
      'title' => 'Offer B',
    ]);
    $nodeB->save();

    $this->assertTrue($manager->addFavorite($nodeA));
    $this->assertFalse($manager->addFavorite($nodeB));
    $this->assertSame(1, $manager->getFavoritesCount('node'));
  }

}
