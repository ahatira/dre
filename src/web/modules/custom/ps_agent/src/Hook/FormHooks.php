<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hooks for enhancing Agent forms.
 */
final class FormHooks {

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    // Target Agent entity forms.
    if (str_starts_with($form_id, 'ps_agent_') && !str_starts_with($form_id, 'ps_agent_type_')) {
      $this->enhanceAgentForm($form, $form_state);
    }
  }

  /**
   * Implements hook_inline_entity_form_table_fields_alter().
   */
  #[Hook('inline_entity_form_table_fields_alter')]
  public function inlineEntityFormTableFieldsAlter(array &$fields, array $context): void {
    // Only alter fields for ps_agent entities.
    if ($context['entity_type'] !== 'ps_agent') {
      return;
    }

    // Add avatar column at the start.
    $fields['avatar'] = [
      'type' => 'callback',
      'label' => t('Avatar'),
      'weight' => -10,
      'callback' => [$this, 'renderAgentAvatar'],
    ];

    // Change label from "Libellé" to "Full Name".
    if (isset($fields['label'])) {
      $fields['label']['label'] = t('Full Name');
      $fields['label']['weight'] = 0;
    }
  }

  /**
   * Callback to render agent avatar in IEF table.
   */
  public function renderAgentAvatar($entity, $variables): array {
    /** @var \Drupal\ps_agent\Entity\AgentInterface $entity */
    if (!$entity->get('avatar')->isEmpty()) {
      /** @var \Drupal\file\FileInterface $file */
      $file = $entity->get('avatar')->entity;
      if ($file && $file->access('view')) {
        $image_style = \Drupal::service('entity_type.manager')->getStorage('image_style')->load('thumbnail');
        if ($image_style) {
          return [
            '#theme' => 'image_style',
            '#style_name' => 'thumbnail',
            '#uri' => $file->getFileUri(),
            '#alt' => $entity->label(),
            '#attributes' => ['class' => ['agent-avatar-ief']],
          ];
        }
        return [
          '#theme' => 'image',
          '#uri' => $file->getFileUri(),
          '#alt' => $entity->label(),
          '#width' => 50,
          '#height' => 50,
          '#attributes' => ['class' => ['agent-avatar-ief']],
        ];
      }
    }

    // Fallback: show default avatar SVG based on civility.
    $civility = $entity->get('civility')->value ?? 'default';
    $civility_map = [
      'mr' => 'mr',
      'ms' => 'ms',
      'mrs' => 'mrs',
    ];
    $avatar_variant = $civility_map[strtolower($civility)] ?? 'default';
    $module_path = \Drupal::service('extension.list.module')->getPath('ps_agent');
    $svg_path = $module_path . '/images/avatar-fallback-' . $avatar_variant . '.svg';

    return [
      '#markup' => '<img src="/' . $svg_path . '" alt="' . $entity->label() . '" class="agent-avatar-ief agent-avatar-fallback" width="50" height="50">',
    ];
  }

  /**
   * Enhance Agent entity forms with better organization and descriptions.
   */
  private function enhanceAgentForm(array &$form, FormStateInterface $form_state): void {
    // Add custom CSS class for styling.
    $form['#attributes']['class'][] = 'ps-agent-form';

    // Organize fields with consistent weights.
    $weight = 0;
    $field_weights = [
      'first_name' => $weight++,
      'civility' => $weight++,
      'last_name' => $weight++,
      'email' => $weight++,
      'phone' => $weight++,
      'job_title' => $weight++,
      'internal_external' => $weight++,
      'avatar' => $weight++,
      'has_avatar' => $weight++,
      'status' => $weight + 80,
      'uid' => $weight + 85,
      'internal_lock' => $weight + 90,
    ];

    foreach ($field_weights as $field_name => $weight) {
      if (isset($form[$field_name])) {
        $form[$field_name]['#weight'] = $weight;
      }
    }

    // Enhanced description for internal_lock field.
    if (isset($form['internal_lock'])) {
      $form['internal_lock']['#description'] = t('⚠️ <strong>Enable this lock to protect manual edits from automated CRM imports.</strong> When locked, this agent will not be overwritten during CRM synchronization. Use this when you\'ve made important manual changes that should be preserved.');
      
      // Visual wrapper for protection field.
      $form['internal_lock']['#prefix'] = '<div class="field-protection-wrapper">';
      $form['internal_lock']['#suffix'] = '</div>';
    }

    // Enhanced placeholders and descriptions.
    if (isset($form['first_name'])) {
      $form['first_name']['widget'][0]['value']['#placeholder'] = t('e.g., John');
    }

    if (isset($form['last_name'])) {
      $form['last_name']['widget'][0]['value']['#placeholder'] = t('e.g., Doe');
    }

    if (isset($form['email'])) {
      $form['email']['widget'][0]['value']['#placeholder'] = t('e.g., john.doe@example.com');
    }

    if (isset($form['phone'])) {
      $form['phone']['widget'][0]['value']['#placeholder'] = t('e.g., +33 1 23 45 67 89');
    }

    if (isset($form['job_title'])) {
      $form['job_title']['widget'][0]['value']['#placeholder'] = t('e.g., Sales Manager');
    }

    // Enhanced description for internal_external field.
    if (isset($form['internal_external'])) {
      $form['internal_external']['#description'] = t('<strong>Internal:</strong> Employee of the company. <strong>External:</strong> Third-party agent or partner.');
    }
  }

}
