<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Unit\Plugin\views\filter;

use Drupal\ps_dictionary\Plugin\views\filter\DictionaryFilter;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\Tests\UnitTestCase;

/**
 * Tests for DictionaryFilter plugin.
 *
 * @coversDefaultClass \Drupal\ps_dictionary\Plugin\views\filter\DictionaryFilter
 * @group ps_dictionary
 */
class DictionaryFilterTest extends UnitTestCase {

  /**
   * The dictionary manager mock.
   *
   * @var \Drupal\ps_dictionary\Service\DictionaryManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected DictionaryManagerInterface $dictionaryManager;

  /**
   * The filter plugin instance.
   *
   * @var \Drupal\ps_dictionary\Plugin\views\filter\DictionaryFilter
   */
  protected DictionaryFilter $filter;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create mock dictionary manager.
    $this->dictionaryManager = $this->createMock(DictionaryManagerInterface::class);

    // Create filter instance.
    $configuration = [];
    $plugin_id = 'ps_dictionary_filter';
    $plugin_definition = [
      'dictionary_type' => 'property_type',
    ];

    $this->filter = new DictionaryFilter(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $this->dictionaryManager
    );
  }

  /**
   * Tests that filter can be instantiated and has correct properties.
   *
   * @covers ::__construct
   */
  public function testFilterConstruction(): void {
    $this->assertInstanceOf(DictionaryFilter::class, $this->filter);
  }

  /**
   * Tests cache tags include dictionary type.
   *
   * @covers ::getCacheTags
   */
  public function testGetCacheTags(): void {
    $tags = $this->filter->getCacheTags();
    $this->assertContains('ps_dictionary:property_type', $tags);
  }

  /**
   * Tests that dictionary_type option exists via reflection.
   *
   * We test via reflection since defineOptions is protected.
   */
  public function testDictionaryTypeOptionExists(): void {
    $reflection = new \ReflectionClass($this->filter);
    $method = $reflection->getMethod('defineOptions');
    $method->setAccessible(TRUE);

    $options = $method->invoke($this->filter);
    $this->assertArrayHasKey('dictionary_type', $options);
    $this->assertEquals('', $options['dictionary_type']['default']);
  }

}
