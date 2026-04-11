<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Kernel;

use Drupal\ps_dictionary\Plugin\views\filter\DictionaryFilter;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\ps_dictionary\Entity\DictionaryEntry;
use Drupal\ps_dictionary\Entity\DictionaryType;

/**
 * Tests Views integration with ps_dictionary fields.
 *
 * @group ps_dictionary
 */
class DictionaryViewsIntegrationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'node',
    'text',
    'options',
    'views',
    'ps',
    'ps_dictionary',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installConfig(['field', 'node', 'ps_dictionary']);
    $this->installSchema('system', ['sequences']);

    // Create a node type.
    NodeType::create([
      'type' => 'test_content',
      'name' => 'Test Content',
    ])->save();

    // Create dictionary type.
    DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
      'description' => 'Test dictionary',
      'locked' => FALSE,
    ])->save();

    // Create dictionary entries.
    DictionaryEntry::create([
      'id' => 'test_type_val1',
      'dictionary_type' => 'test_type',
      'code' => 'VAL1',
      'label' => 'Value 1',
      'weight' => 0,
      'status' => TRUE,
      'deprecated' => FALSE,
    ])->save();

    DictionaryEntry::create([
      'id' => 'test_type_val2',
      'dictionary_type' => 'test_type',
      'code' => 'VAL2',
      'label' => 'Value 2',
      'weight' => 1,
      'status' => TRUE,
      'deprecated' => FALSE,
    ])->save();

    // Create dictionary field.
    FieldStorageConfig::create([
      'field_name' => 'field_test_dict',
      'entity_type' => 'node',
      'type' => 'ps_dictionary',
      'settings' => ['dictionary_type' => 'test_type'],
    ])->save();

    FieldConfig::create([
      'field_name' => 'field_test_dict',
      'entity_type' => 'node',
      'bundle' => 'test_content',
      'label' => 'Test Dictionary',
    ])->save();
  }

  /**
   * Tests that the ps_dictionary_filter plugin exists and is registered.
   */
  public function testFilterPluginExists(): void {
    $plugin_manager = $this->container->get('plugin.manager.views.filter');
    $this->assertTrue($plugin_manager->hasDefinition('ps_dictionary_filter'));

    // Get plugin definition.
    $definition = $plugin_manager->getDefinition('ps_dictionary_filter');
    $this->assertNotNull($definition);
    $this->assertEquals('ps_dictionary_filter', $definition['id']);
  }

  /**
   * Tests that the filter plugin can be instantiated.
   */
  public function testFilterPluginInstantiation(): void {
    $plugin_manager = $this->container->get('plugin.manager.views.filter');

    // Create plugin with minimal configuration.
    $configuration = [
      'dictionary_type' => 'test_type',
    ];

    $plugin_definition = [
      'id' => 'ps_dictionary_filter',
      'dictionary_type' => 'test_type',
    ];

    $plugin = $plugin_manager->createInstance('ps_dictionary_filter', $configuration);
    $this->assertInstanceOf(DictionaryFilter::class, $plugin);
  }

}
