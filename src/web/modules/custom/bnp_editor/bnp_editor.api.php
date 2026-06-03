<?php

/**
 * @file
 * Hooks provided by the BNP Editor module.
 *
 * This file documents the hooks that BNP Editor module invokes to allow
 * other modules to extend or alter editor functionality.
 */

declare(strict_types=1);

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter BNP Editor configuration before it is saved.
 *
 * This hook allows modules to modify the editor configuration settings
 * before they are stored in the configuration system.
 *
 * @param array $config
 *   The editor configuration array. Keys include:
 *   - enable_custom_plugins: boolean
 *   - enable_media_embed: boolean
 *   - allowed_protocols: string
 *
 * @see \Drupal\bnp_editor\Form\BnpEditorSettingsForm
 */
function hook_bnp_editor_config_alter(array &$config): void {
  // Example: Force enable media embed for all configurations.
  $config['enable_media_embed'] = TRUE;

  // Example: Add additional allowed protocols.
  $existing_protocols = explode("\n", $config['allowed_protocols'] ?? '');
  $existing_protocols[] = 'ftp';
  $config['allowed_protocols'] = implode("\n", array_unique($existing_protocols));
}

/**
 * Alter the list of available editor configurations.
 *
 * This hook allows modules to modify or extend the list of editor
 * configurations returned by the EditorManager service.
 *
 * @param array $configurations
 *   Array of editor configurations, keyed by editor ID. Each configuration
 *   contains:
 *   - id: string - Editor ID
 *   - label: string - Editor label
 *   - format: string - Text format ID
 *   - settings: array - Editor settings
 *
 * @see \Drupal\bnp_editor\Service\EditorManager::getEditorConfigurations()
 */
function hook_bnp_editor_configurations_alter(array &$configurations): void {
  // Example: Add custom metadata to BNP Rich Text configuration.
  if (isset($configurations['full_html'])) {
    $configurations['full_html']['custom_metadata'] = [
      'department' => 'Marketing',
      'approved_by' => 'admin',
    ];
  }

  // Example: Remove a specific configuration.
  unset($configurations['unwanted_editor']);
}

/**
 * Perform actions after BNP Editor settings are saved.
 *
 * This hook is invoked after the BNP Editor configuration form is
 * successfully submitted and settings are saved.
 *
 * @param array $config
 *   The saved configuration values.
 *
 * @see \Drupal\bnp_editor\Form\BnpEditorSettingsForm::submitForm()
 */
function hook_bnp_editor_config_save(array $config): void {
  // Example: Log configuration changes.
  \Drupal::logger('custom_module')->info('BNP Editor settings updated: @config', [
    '@config' => print_r($config, TRUE),
  ]);

  // Example: Trigger a cache clear for related systems.
  \Drupal::service('cache_tags.invalidator')->invalidateTags(['custom_editor_cache']);

  // Example: Send notification to administrators.
  if (!$config['enable_custom_plugins']) {
    \Drupal::messenger()->addWarning('Custom plugins have been disabled.');
  }
}

/**
 * Alter the allowed HTML tags for BNP text formats.
 *
 * This hook allows modules to modify the list of allowed HTML tags
 * before they are applied to text formats.
 *
 * @param array $allowed_tags
 *   Array of allowed HTML tags with attributes.
 * @param string $format_id
 *   The text format ID being altered.
 *
 * @see filter.format.full_html.yml
 */
function hook_bnp_editor_allowed_tags_alter(array &$allowed_tags, string $format_id): void {
  // Example: Add custom tag for BNP Rich Text format.
  if ($format_id === 'full_html') {
    $allowed_tags[] = '<custom-element data-*>';
  }

  // Example: Remove potentially dangerous tags.
  $allowed_tags = array_filter($allowed_tags, function ($tag) {
    return !str_contains($tag, '<script');
  });
}

/**
 * Alter CKEditor 5 toolbar configuration for BNP editors.
 *
 * This hook allows modules to modify the toolbar items available
 * in BNP editor configurations.
 *
 * @param array $toolbar_items
 *   Array of toolbar item IDs.
 * @param string $editor_id
 *   The editor configuration ID.
 *
 * @see editor.editor.full_html.yml
 */
function hook_bnp_editor_toolbar_alter(array &$toolbar_items, string $editor_id): void {
  // Example: Add custom toolbar item.
  if ($editor_id === 'full_html') {
    $toolbar_items[] = 'customButton';
  }

  // Example: Remove source editing for specific editors.
  $key = array_search('sourceEditing', $toolbar_items);
  if ($key !== FALSE) {
    unset($toolbar_items[$key]);
  }
}

/**
 * Provide custom CKEditor 5 plugin definitions.
 *
 * This hook allows modules to register additional CKEditor 5 plugins
 * that should be available for BNP editors.
 *
 * @return array
 *   Array of plugin definitions, keyed by plugin ID. Each definition should
 *   contain:
 *   - id: string - Unique plugin ID
 *   - label: string - Human-readable label
 *   - class: string - Fully qualified class name
 *   - library: string - Asset library name
 *   - enabled: bool - Whether plugin is enabled by default
 */
function hook_bnp_editor_plugins(): array {
  return [
    'custom_plugin' => [
      'id' => 'custom_plugin',
      'label' => t('Custom Plugin'),
      'class' => '\Drupal\custom_module\Plugin\CKEditor5Plugin\CustomPlugin',
      'library' => 'custom_module/ckeditor_plugin',
      'enabled' => TRUE,
    ],
  ];
}

/**
 * Alter existing CKEditor 5 plugin definitions.
 *
 * This hook allows modules to modify plugin definitions provided by
 * BNP Editor or other modules.
 *
 * @param array $plugins
 *   Array of plugin definitions, keyed by plugin ID.
 *
 * @see hook_bnp_editor_plugins()
 */
function hook_bnp_editor_plugins_alter(array &$plugins): void {
  // Example: Disable a specific plugin.
  if (isset($plugins['bnp_editor_example'])) {
    $plugins['bnp_editor_example']['enabled'] = FALSE;
  }

  // Example: Override plugin library.
  if (isset($plugins['some_plugin'])) {
    $plugins['some_plugin']['library'] = 'custom_module/override_library';
  }
}

/**
 * Validate BNP Editor configuration before saving.
 *
 * This hook allows modules to add custom validation logic to the
 * BNP Editor settings form.
 *
 * @param array $values
 *   The form values to validate.
 * @param array &$errors
 *   Array to add validation errors to. Keys are field names, values are
 *   error messages.
 *
 * @see \Drupal\bnp_editor\Form\BnpEditorSettingsForm::validateForm()
 */
function hook_bnp_editor_config_validate(array $values, array &$errors): void {
  // Example: Ensure at least one protocol is allowed.
  $protocols = array_filter(explode("\n", $values['allowed_protocols'] ?? ''));
  if (empty($protocols)) {
    $errors['allowed_protocols'] = t('At least one protocol must be allowed.');
  }

  // Example: Validate protocol format.
  foreach ($protocols as $protocol) {
    if (!preg_match('/^[a-z]+$/', trim($protocol))) {
      $errors['allowed_protocols'] = t('Invalid protocol format: @protocol', [
        '@protocol' => $protocol,
      ]);
    }
  }
}

/**
 * React to BNP Editor module installation.
 *
 * This hook is invoked when the BNP Editor module is installed.
 */
function hook_bnp_editor_install(): void {
  // Example: Create default content with rich text.
  $node = \Drupal::entityTypeManager()->getStorage('node')->create([
    'type' => 'article',
    'title' => 'Welcome to BNP Editor',
    'body' => [
      'value' => '<p>This article uses the <strong>Full HTML</strong> text format.</p>',
      'format' => 'full_html',
    ],
  ]);
  $node->save();

  // Example: grant format permissions (prefer BnpEditorRoleInstaller on install).
  $role = \Drupal::entityTypeManager()->getStorage('user_role')->load('content_editor');
  if ($role) {
    $role->grantPermission('use text format basic_html');
    $role->save();
  }
}

/**
 * React to BNP Editor module uninstallation.
 *
 * This hook is invoked when the BNP Editor module is uninstalled.
 */
function hook_bnp_editor_uninstall(): void {
  // Example: Clean up custom tables or data.
  \Drupal::database()->delete('custom_editor_metadata')
    ->condition('module', 'bnp_editor')
    ->execute();

  // Example: Log uninstallation.
  \Drupal::logger('custom_module')->info('BNP Editor has been uninstalled.');
}

/**
 * @} End of "addtogroup hooks".
 */
