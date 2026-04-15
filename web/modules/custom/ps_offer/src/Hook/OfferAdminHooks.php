<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Hook;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferReferenceBuilder;

/**
 * UI and access hooks for offer nodes.
 */
final class OfferAdminHooks {

  /**
   * Constructs OfferAdminHooks.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected OfferReferenceBuilder $referenceBuilder,
  ) {}

  /**
   * Implements hook_help().
   */
  #[Hook('help')]
  public function help(string $route_name, RouteMatchInterface $route_match): array|string|null {
    unset($route_match);

    return match ($route_name) {
      'help.page.ps_offer' => [
        '#type' => 'markup',
        '#markup' => t('Manage property offers, pricing, diagnostics, media, and agents.'),
      ],
      'ps_offer.settings' => t('Configure offer behavior and display settings.'),
      'ps_offer.admin_offers' => t('View and manage all property offers.'),
      default => NULL,
    };
  }

  /**
   * Implements hook_node_access().
   */
  #[Hook('node_access')]
  public function nodeAccess(NodeInterface $node, string $op, AccountInterface $account): ?AccessResult {
    if ($node->getType() !== 'offer') {
      return AccessResult::neutral();
    }

    // Basic permission mapping for offer nodes.
    return match ($op) {
      'update' => AccessResult::allowedIfHasPermission($account, 'edit any offer content')->cachePerPermissions(),
      'delete' => AccessResult::allowedIfHasPermission($account, 'delete any offer content')->cachePerPermissions(),
      'view' => AccessResult::neutral()->addCacheableDependency($node)->cachePerPermissions(),
      default => AccessResult::neutral(),
    };
  }

  /**
   * Implements hook_entity_base_field_info_alter().
   *
   * Node titles are stored in shared base tables, so the storage definition
   * must be increased globally to allow offer titles longer than 450 chars.
   */
  #[Hook('entity_base_field_info_alter')]
  public function entityBaseFieldInfoAlter(array &$fields, EntityTypeInterface $entity_type): void {
    if ($entity_type->id() !== 'node' || !isset($fields['title'])) {
      return;
    }

    $fields['title']->setSetting('max_length', 512);
  }

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    unset($form_id);

    $form_object = $form_state->getFormObject();
    if (!$form_object || !method_exists($form_object, 'getEntity')) {
      return;
    }

    $entity = $form_object->getEntity();
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return;
    }

    // Keep the CRM identifier internal; editors should never see this field.
    if (isset($form['external_id'])) {
      $form['external_id']['#access'] = FALSE;
    }

    if (isset($form['field_reference']['widget'][0]['value'])) {
      $current_reference = strtoupper(trim((string) ($entity->get('field_reference')->value ?? '')));
      $is_reference_locked = !$entity->isNew() && $current_reference !== '';

      $form['field_reference']['widget'][0]['value']['#description'] = t('Leave empty to generate the 12-character offer reference automatically.');
      $form['field_reference']['widget'][0]['value']['#maxlength'] = 12;
      $form['field_reference']['widget'][0]['value']['#size'] = 16;

      if ($is_reference_locked) {
        // Existing references must remain immutable after first save.
        $form['field_reference']['widget'][0]['value']['#attributes']['readonly'] = 'readonly';
        $form['field_reference']['widget'][0]['value']['#description'] = t('This reference is already generated and is read-only.');
      }
      else {
        // Pass full segment config to JS for immediate client-side preview.
        // The counter segment (type "auto") will show "?????" as placeholder.
        $form['#attached']['library'][] = 'ps_offer/reference_live';
        $form['#attached']['drupalSettings']['psOfferReference'] = [
          'nid' => (int) ($entity->id() ?? 0),
          'segments' => $this->referenceBuilder->getJsSegmentsConfig(),
        ];
      }
    }

    if (isset($form['title']['widget'][0]['value'])) {
      $form['title']['widget'][0]['value']['#description'] = t('Commercial title supporting up to 512 characters.');
      $form['title']['widget'][0]['value']['#maxlength'] = 512;
    }

    if (isset($form['field_divisions'], $form['field_is_divisible'])) {
      $form['field_divisions']['#states'] = [
        'visible' => [
          ':input[name="field_is_divisible[value]"]' => ['checked' => TRUE],
        ],
      ];
    }
  }

}
