<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Unit\Plugin\ImportGovernance;

use Drupal\ps_dictionary\Plugin\ImportGovernance\DictionaryImportGovernancePolicy;
use Drupal\ps_dictionary\Service\DictionaryImportGovernance;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_dictionary\Plugin\ImportGovernance\DictionaryImportGovernancePolicy
 */
#[CoversClass(DictionaryImportGovernancePolicy::class)]
#[Group('ps_dictionary')]
final class DictionaryImportGovernancePolicyTest extends UnitTestCase {

  /**
   * @covers ::getEntityTypeIds
   */
  public function testGetEntityTypeIds(): void {
    $policy = new DictionaryImportGovernancePolicy(
      [],
      'dictionary',
      [
        'admin_label' => 'Dictionary',
        'description' => 'Dictionary import governance.',
        'settings_route' => 'ps_dictionary.governance_domain_settings',
        'weight' => 30,
      ],
      $this->createMock(DictionaryImportGovernance::class),
    );

    self::assertSame(['ps_dictionary_entry'], $policy->getEntityTypeIds());
  }

}
