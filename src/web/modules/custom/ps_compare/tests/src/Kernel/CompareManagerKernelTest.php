<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_compare\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\user\Entity\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Kernel tests for compare manager storage and limits.
 */
#[Group('ps_compare')]
#[RunTestsInSeparateProcesses]
final class CompareManagerKernelTest extends KernelTestBase {

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
    'ps_offer',
    'ps_compare',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installSchema('node', ['node_access']);
    $this->installSchema('ps_compare', ['ps_compare_item']);
    $this->installConfig(['node', 'ps_compare']);

    NodeType::create([
      'type' => 'offer',
      'name' => 'Offer',
    ])->save();
  }

  /**
   *
   */
  public function testAnonymousMergeOnLogin(): void {
    $manager = $this->container->get('ps_compare.manager');
    $accountProxy = $this->container->get('current_user');
    $cookieState = $this->container->get('ps_compare.cookie_state');
    $repository = $this->container->get('ps_compare.repository');

    $node = Node::create([
      'type' => 'offer',
      'title' => 'Offer 1',
    ]);
    $node->save();

    $this->assertTrue($manager->addCompare($node));
    $this->assertSame(1, $manager->getCompareCount('node'));

    $user = User::create([
      'name' => 'compare_kernel_user',
      'mail' => 'compare_kernel_user@example.com',
      'status' => 1,
    ]);
    $user->save();

    $accountProxy->setAccount($user);
    $manager->mergeAnonymousCompare('node');

    $this->assertTrue($repository->has((int) $user->id(), 'node', (int) $node->id()));
    $this->assertContains('node', $cookieState->getClearedEntityTypes());
  }

  /**
   *
   */
  public function testMaxCompareLimit(): void {
    $manager = $this->container->get('ps_compare.manager');

    $nodes = [];
    for ($i = 0; $i < 5; $i++) {
      $node = Node::create([
        'type' => 'offer',
        'title' => 'Offer ' . $i,
      ]);
      $node->save();
      $nodes[] = $node;
    }

    $this->assertTrue($manager->addCompare($nodes[0]));
    $this->assertTrue($manager->addCompare($nodes[1]));
    $this->assertTrue($manager->addCompare($nodes[2]));
    $this->assertTrue($manager->addCompare($nodes[3]));
    $this->assertFalse($manager->addCompare($nodes[4]));
    $this->assertSame(4, $manager->getCompareCount('node'));
  }

  /**
   *
   */
  public function testFifoOrder(): void {
    $manager = $this->container->get('ps_compare.manager');

    $nodeA = Node::create(['type' => 'offer', 'title' => 'A']);
    $nodeA->save();
    $nodeB = Node::create(['type' => 'offer', 'title' => 'B']);
    $nodeB->save();
    $nodeC = Node::create(['type' => 'offer', 'title' => 'C']);
    $nodeC->save();

    $manager->addCompare($nodeA);
    $manager->addCompare($nodeB);
    $manager->removeCompare($nodeA);
    $manager->addCompare($nodeC);

    $this->assertSame([(int) $nodeB->id(), (int) $nodeC->id()], $manager->getCompareIds('node'));
  }

  /**
   *
   */
  public function testCanOpenComparisonPageRequiresMinItems(): void {
    $manager = $this->container->get('ps_compare.manager');

    $nodeA = Node::create(['type' => 'offer', 'title' => 'A']);
    $nodeA->save();

    $this->assertFalse($manager->canOpenComparisonPage());
    $manager->addCompare($nodeA);
    $this->assertFalse($manager->canOpenComparisonPage());

    $nodeB = Node::create(['type' => 'offer', 'title' => 'B']);
    $nodeB->save();
    $manager->addCompare($nodeB);
    $this->assertTrue($manager->canOpenComparisonPage());
  }

}
