<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Kernel;

use Drupal\Core\Cache\Cache;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Kernel coverage for offer section plugin discovery, config, cache, and headings.
 */
#[Group('ps_core')]
#[RunTestsInSeparateProcesses]
final class OfferSectionKernelTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected $strictConfigSchema = FALSE;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'ps_core',
    'ps_core_test_offer_section',
    'ps_surface',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig(['ps_core']);
  }

  /**
   * Ensures section plugins from enabled modules are discovered and sorted.
   */
  public function testPluginDiscoveryIncludesEnabledModules(): void {
    /** @var \Drupal\ps_core\Service\OfferSectionRegistry $registry */
    $registry = $this->container->get('ps_core.section_registry');
    $plugins = $registry->getPlugins();

    $this->assertArrayHasKey('test_plain', $plugins);
    $this->assertArrayHasKey('test_icon', $plugins);
    $this->assertArrayHasKey('surface_table', $plugins);
    $this->assertSame(
      ['test_plain', 'test_icon', 'surface_table'],
      array_keys($plugins),
    );
  }

  /**
   * Ensures stored config overrides plugin defaults.
   */
  public function testConfigOverridesPluginDefaults(): void {
    $this->config('ps_core.offer_section_settings')
      ->set('sections.surface_table', [
        'label' => 'Kernel surface title',
        'icon' => 'bnp_custom:kernel-floors',
      ])
      ->save();

    /** @var \Drupal\ps_core\Service\OfferSectionRegistry $registry */
    $registry = $this->container->get('ps_core.section_registry');

    $this->assertSame('Kernel surface title', $registry->getLabel('surface_table'));
    $this->assertSame('bnp_custom:kernel-floors', $registry->getIconId('surface_table'));
  }

  /**
   * Ensures unknown sections resolve to empty values.
   */
  public function testUnknownSectionReturnsEmptyValues(): void {
    /** @var \Drupal\ps_core\Service\OfferSectionRegistry $registry */
    $registry = $this->container->get('ps_core.section_registry');

    $this->assertSame('', $registry->getLabel('missing_section'));
    $this->assertSame('', $registry->getIconId('missing_section'));
    $this->assertNull($registry->getPlugin('missing_section'));
  }

  /**
   * Ensures plugin label defaults apply when config is absent.
   */
  public function testPluginLabelDefaultsAreUsedWithoutStoredConfig(): void {
    /** @var \Drupal\ps_core\Service\OfferSectionRegistry $registry */
    $registry = $this->container->get('ps_core.section_registry');

    $this->assertSame('Test icon default', $registry->getLabel('test_icon'));
  }

  /**
   * Ensures icons stay empty unless explicitly configured.
   */
  public function testOptionalIconReturnsEmptyWhenUnset(): void {
    /** @var \Drupal\ps_core\Service\OfferSectionRegistry $registry */
    $registry = $this->container->get('ps_core.section_registry');

    $this->assertSame('', $registry->getIconId('test_icon'));
    $this->assertSame('bnp_custom:surface', $registry->getIconId('surface_table'));
  }

  /**
   * Ensures cleared icons are not rendered in section headings.
   */
  public function testHeadingBuilderOmitsIconWhenNotConfigured(): void {
    $this->config('ps_core.offer_section_settings')
      ->set('sections.surface_table.icon', '')
      ->save();

    /** @var \Drupal\ps_core\Service\OfferSectionHeadingBuilder $builder */
    $builder = $this->container->get('ps_core.section_heading_builder');
    $content = $builder->buildTitleContent('surface_table');

    $this->assertArrayNotHasKey('icon', $content);
    $this->assertSame([], $builder->buildIcon('surface_table'));
  }

  /**
   * Ensures energy legacy settings remain a fallback when section config is empty.
   */
  public function testLegacyDiagnosticSettingsFallback(): void {
    $this->enableModules(['ps_diagnostic']);

    $this->config('ps_diagnostic.settings')
      ->set('section_label', 'Legacy diagnostics title')
      ->set('section_icon', 'bnp_custom:legacy-diag')
      ->save();

    $sections = (array) ($this->config('ps_core.offer_section_settings')->get('sections') ?? []);
    unset($sections['energy']);
    $this->config('ps_core.offer_section_settings')
      ->set('sections', $sections)
      ->save();

    /** @var \Drupal\ps_core\Service\OfferSectionRegistry $registry */
    $registry = $this->container->get('ps_core.section_registry');

    $this->assertSame('Legacy diagnostics title', $registry->getLabel('energy'));
    $this->assertSame('bnp_custom:legacy-diag', $registry->getIconId('energy'));
  }

  /**
   * Ensures heading builder exposes config cache tags for render arrays.
   */
  public function testHeadingBuilderUsesConfigCacheTags(): void {
    /** @var \Drupal\ps_core\Service\OfferSectionHeadingBuilder $builder */
    $builder = $this->container->get('ps_core.section_heading_builder');

    $this->assertSame(
      ['config:ps_core.offer_section_settings'],
      $builder->getCacheTags(),
    );
    $this->assertSame(
      ['languages:language_interface'],
      $builder->getCacheContexts(),
    );

    $content = $builder->buildTitleContent('test_plain');
    $this->assertSame(
      ['config:ps_core.offer_section_settings'],
      $content['#cache']['tags'],
    );
    $this->assertSame(
      ['languages:language_interface'],
      $content['#cache']['contexts'],
    );
  }

  /**
   * Ensures heading builder returns empty content when label cannot be resolved.
   */
  public function testHeadingBuilderReturnsEmptyContentForUnknownSection(): void {
    /** @var \Drupal\ps_core\Service\OfferSectionHeadingBuilder $builder */
    $builder = $this->container->get('ps_core.section_heading_builder');

    $this->assertSame([], $builder->buildTitleContent('missing_section'));
    $this->assertSame([], $builder->buildIcon('missing_section'));
  }

  /**
   * Ensures heading builder structures title markup and honors options.
   */
  public function testHeadingBuilderBuildsTitleMarkup(): void {
    $this->config('ps_core.offer_section_settings')
      ->set('sections.test_plain.label', 'Plain kernel title')
      ->save();

    /** @var \Drupal\ps_core\Service\OfferSectionHeadingBuilder $builder */
    $builder = $this->container->get('ps_core.section_heading_builder');
    $build = $builder->buildTitle('test_plain', [
      'tag' => 'h3',
      'title_classes' => ['extra-class'],
    ]);

    $this->assertSame('html_tag', $build['#type']);
    $this->assertSame('h3', $build['#tag']);
    $this->assertContains('ps-offer-section__title', $build['#attributes']['class']);
    $this->assertContains('extra-class', $build['#attributes']['class']);
    $this->assertSame('Plain kernel title', $build['content']['text']['#value']);
    $this->assertArrayNotHasKey('icon', $build['content']);
  }

  /**
   * Ensures config updates change resolved labels (cache invalidation contract).
   */
  public function testConfigSaveUpdatesResolvedLabel(): void {
    /** @var \Drupal\ps_core\Service\OfferSectionRegistry $registry */
    $registry = $this->container->get('ps_core.section_registry');
    $this->assertSame('Test plain default', $registry->getLabel('test_plain'));

    $this->config('ps_core.offer_section_settings')
      ->set('sections.test_plain.label', 'Updated plain title')
      ->save();

    Cache::invalidateTags(['config:ps_core.offer_section_settings']);

    $this->assertSame('Updated plain title', $registry->getLabel('test_plain'));
  }

}
