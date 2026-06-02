<?php

declare(strict_types=1);

namespace Drupal\bnp_editor\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service for managing CKEditor configurations and plugins.
 */
final class EditorManager {

  /**
   * Constructs an EditorManager object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger channel.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Gets available CKEditor configurations.
   *
   * @return array<string, mixed>
   *   Array of editor configurations.
   */
  public function getEditorConfigurations(): array {
    $configs = [];
    
    try {
      $editor_storage = $this->entityTypeManager->getStorage('editor');
      $editors = $editor_storage->loadMultiple();
      
      foreach ($editors as $editor_id => $editor) {
        if ($editor->getEditor() === 'ckeditor5') {
          $configs[$editor_id] = [
            'id' => $editor->id(),
            'label' => $editor->label(),
            'format' => $editor->getFilterFormat()->id(),
            'settings' => $editor->getSettings(),
          ];
        }
      }
    }
    catch (\Exception $e) {
      $this->logger->error('Error loading editor configurations: @message', [
        '@message' => $e->getMessage(),
      ]);
    }
    
    return $configs;
  }

  /**
   * Validates editor configuration.
   *
   * @param string $editor_id
   *   The editor ID to validate.
   *
   * @return bool
   *   TRUE if valid, FALSE otherwise.
   */
  public function validateEditorConfig(string $editor_id): bool {
    try {
      $editor_storage = $this->entityTypeManager->getStorage('editor');
      $editor = $editor_storage->load($editor_id);
      
      return $editor !== NULL && $editor->getEditor() === 'ckeditor5';
    }
    catch (\Exception $e) {
      $this->logger->error('Error validating editor configuration: @message', [
        '@message' => $e->getMessage(),
      ]);
      return FALSE;
    }
  }

}
