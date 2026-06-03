<?php

declare(strict_types=1);

namespace Drupal\Tests\bnp_editor\Unit\Service;

use Drupal\bnp_editor\Service\EditorManager;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\editor\EditorInterface;
use Drupal\filter\FilterFormatInterface;
use Drupal\Tests\UnitTestCase;
use Psr\Log\LoggerInterface;

/**
 * Unit tests for EditorManager service.
 *
 * @coversDefaultClass \Drupal\bnp_editor\Service\EditorManager
 * @group bnp_editor
 */
final class EditorManagerTest extends UnitTestCase {

  /**
   * The entity type manager mock.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  private EntityTypeManagerInterface $entityTypeManager;

  /**
   * The logger mock.
   *
   * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  private LoggerInterface $logger;

  /**
   * The editor manager service under test.
   *
   * @var \Drupal\bnp_editor\Service\EditorManager
   */
  private EditorManager $editorManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->logger = $this->createMock(LoggerInterface::class);

    $this->editorManager = new EditorManager(
      $this->entityTypeManager,
      $this->logger,
    );
  }

  /**
   * Tests getEditorConfigurations() with valid CKEditor 5 editors.
   *
   * @covers ::getEditorConfigurations
   */
  public function testGetEditorConfigurationsWithValidEditors(): void {
    // Mock filter format.
    $format = $this->createMock(FilterFormatInterface::class);
    $format->method('id')->willReturn('full_html');

    // Mock editor entity.
    $editor = $this->createMock(EditorInterface::class);
    $editor->method('id')->willReturn('full_html');
    $editor->method('label')->willReturn('Full HTML');
    $editor->method('getEditor')->willReturn('ckeditor5');
    $editor->method('getFilterFormat')->willReturn($format);
    $editor->method('getSettings')->willReturn([
      'toolbar' => ['items' => ['bold', 'italic']],
    ]);

    // Mock editor storage.
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadMultiple')
      ->willReturn(['full_html' => $editor]);

    // Mock entity type manager.
    $this->entityTypeManager->method('getStorage')
      ->with('editor')
      ->willReturn($storage);

    // Execute test.
    $configurations = $this->editorManager->getEditorConfigurations();

    // Assertions.
    $this->assertIsArray($configurations);
    $this->assertArrayHasKey('full_html', $configurations);
    $this->assertEquals('full_html', $configurations['full_html']['id']);
    $this->assertEquals('Full HTML', $configurations['full_html']['label']);
    $this->assertEquals('full_html', $configurations['full_html']['format']);
    $this->assertIsArray($configurations['full_html']['settings']);
  }

  /**
   * Tests getEditorConfigurations() filters out non-CKEditor5 editors.
   *
   * @covers ::getEditorConfigurations
   */
  public function testGetEditorConfigurationsFiltersNonCKEditor5(): void {
    // Mock CKEditor 5 editor.
    $format_cke5 = $this->createMock(FilterFormatInterface::class);
    $format_cke5->method('id')->willReturn('full_html');

    $editor_cke5 = $this->createMock(EditorInterface::class);
    $editor_cke5->method('id')->willReturn('full_html');
    $editor_cke5->method('label')->willReturn('Full HTML');
    $editor_cke5->method('getEditor')->willReturn('ckeditor5');
    $editor_cke5->method('getFilterFormat')->willReturn($format_cke5);
    $editor_cke5->method('getSettings')->willReturn([]);

    // Mock CKEditor 4 editor (should be filtered out).
    $format_cke4 = $this->createMock(FilterFormatInterface::class);
    $format_cke4->method('id')->willReturn('legacy_editor');

    $editor_cke4 = $this->createMock(EditorInterface::class);
    $editor_cke4->method('id')->willReturn('legacy_editor');
    $editor_cke4->method('label')->willReturn('Legacy Editor');
    $editor_cke4->method('getEditor')->willReturn('ckeditor');
    $editor_cke4->method('getFilterFormat')->willReturn($format_cke4);
    $editor_cke4->method('getSettings')->willReturn([]);

    // Mock editor storage.
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadMultiple')->willReturn([
      'full_html' => $editor_cke5,
      'legacy_editor' => $editor_cke4,
    ]);

    // Mock entity type manager.
    $this->entityTypeManager->method('getStorage')
      ->with('editor')
      ->willReturn($storage);

    // Execute test.
    $configurations = $this->editorManager->getEditorConfigurations();

    // Assertions.
    $this->assertIsArray($configurations);
    $this->assertArrayHasKey('full_html', $configurations);
    $this->assertArrayNotHasKey('legacy_editor', $configurations);
    $this->assertCount(1, $configurations);
  }

  /**
   * Tests getEditorConfigurations() handles exceptions gracefully.
   *
   * @covers ::getEditorConfigurations
   */
  public function testGetEditorConfigurationsHandlesException(): void {
    // Mock editor storage that throws exception.
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadMultiple')
      ->willThrowException(new \Exception('Storage error'));

    // Mock entity type manager.
    $this->entityTypeManager->method('getStorage')
      ->with('editor')
      ->willReturn($storage);

    // Expect logger to be called.
    $this->logger->expects($this->once())
      ->method('error')
      ->with(
        'Error loading editor configurations: @message',
        $this->callback(function ($context) {
          return isset($context['@message']) && $context['@message'] === 'Storage error';
        })
      );

    // Execute test.
    $configurations = $this->editorManager->getEditorConfigurations();

    // Assertions.
    $this->assertIsArray($configurations);
    $this->assertEmpty($configurations);
  }

  /**
   * Tests validateEditorConfig() with valid CKEditor 5 editor.
   *
   * @covers ::validateEditorConfig
   */
  public function testValidateEditorConfigWithValidEditor(): void {
    // Mock editor entity.
    $editor = $this->createMock(EditorInterface::class);
    $editor->method('getEditor')->willReturn('ckeditor5');

    // Mock editor storage.
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')
      ->with('full_html')
      ->willReturn($editor);

    // Mock entity type manager.
    $this->entityTypeManager->method('getStorage')
      ->with('editor')
      ->willReturn($storage);

    // Execute test.
    $result = $this->editorManager->validateEditorConfig('full_html');

    // Assertions.
    $this->assertTrue($result);
  }

  /**
   * Tests validateEditorConfig() with non-CKEditor5 editor.
   *
   * @covers ::validateEditorConfig
   */
  public function testValidateEditorConfigWithNonCKEditor5(): void {
    // Mock editor entity (CKEditor 4).
    $editor = $this->createMock(EditorInterface::class);
    $editor->method('getEditor')->willReturn('ckeditor');

    // Mock editor storage.
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')
      ->with('legacy_editor')
      ->willReturn($editor);

    // Mock entity type manager.
    $this->entityTypeManager->method('getStorage')
      ->with('editor')
      ->willReturn($storage);

    // Execute test.
    $result = $this->editorManager->validateEditorConfig('legacy_editor');

    // Assertions.
    $this->assertFalse($result);
  }

  /**
   * Tests validateEditorConfig() with non-existent editor.
   *
   * @covers ::validateEditorConfig
   */
  public function testValidateEditorConfigWithNonExistentEditor(): void {
    // Mock editor storage.
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')
      ->with('nonexistent')
      ->willReturn(NULL);

    // Mock entity type manager.
    $this->entityTypeManager->method('getStorage')
      ->with('editor')
      ->willReturn($storage);

    // Execute test.
    $result = $this->editorManager->validateEditorConfig('nonexistent');

    // Assertions.
    $this->assertFalse($result);
  }

  /**
   * Tests validateEditorConfig() handles exceptions gracefully.
   *
   * @covers ::validateEditorConfig
   */
  public function testValidateEditorConfigHandlesException(): void {
    // Mock editor storage that throws exception.
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')
      ->willThrowException(new \Exception('Load error'));

    // Mock entity type manager.
    $this->entityTypeManager->method('getStorage')
      ->with('editor')
      ->willReturn($storage);

    // Expect logger to be called.
    $this->logger->expects($this->once())
      ->method('error')
      ->with(
        'Error validating editor configuration: @message',
        $this->callback(function ($context) {
          return isset($context['@message']) && $context['@message'] === 'Load error';
        })
      );

    // Execute test.
    $result = $this->editorManager->validateEditorConfig('error_editor');

    // Assertions.
    $this->assertFalse($result);
  }

}
