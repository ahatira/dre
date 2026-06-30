<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;
use Drupal\ps_compare\Repository\CompareRepositoryInterface;
use Drupal\ps_compare\Service\CompareManagerInterface;
use Drupal\ps_email\Service\EmailDesignTokens;
use Drupal\user\UserInterface;

/**
 *
 */
final class CompareHooks {

  public function __construct(
    private readonly CompareManagerInterface $compareManager,
    private readonly CompareRepositoryInterface $compareRepository,
    private readonly EmailDesignTokens $emailDesignTokens,
  ) {}

  /**
 *
 */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'ps_compare_button' => [
        'variables' => [
          'entity_type_id' => NULL,
          'entity_id' => NULL,
          'toggle_url' => NULL,
          'is_compared' => FALSE,
          'context' => 'inline',
          'label_add' => NULL,
          'label_remove' => NULL,
        ],
        'template' => 'ps-compare-button',
      ],
      'ps_compare_empty_state' => [
        'variables' => [
          'title' => NULL,
          'message' => NULL,
          'search_url' => NULL,
        ],
        'template' => 'ps-compare-empty-state',
      ],
      'ps_compare_page_stub' => [
        'variables' => [
          'title' => NULL,
          'intro' => NULL,
          'items' => [],
        ],
        'template' => 'ps-compare-page-stub',
      ],
      'ps_compare_widget' => [
        'variables' => [
          'count' => 0,
          'max_items' => 4,
          'min_items' => 2,
          'can_compare' => FALSE,
          'compare_url' => NULL,
          'items' => [],
        ],
        'template' => 'ps-compare-widget',
      ],
      'ps_compare_panel_item' => [
        'variables' => [
          'entity_type_id' => NULL,
          'entity_id' => NULL,
          'title' => NULL,
          'surface' => NULL,
          'surface_primary' => NULL,
          'surface_suffix' => NULL,
          'price_amount' => NULL,
          'price_qualifiers' => NULL,
          'thumbnail' => NULL,
          'url' => NULL,
          'toggle_url' => NULL,
        ],
        'template' => 'ps-compare-panel-item',
      ],
      'ps_compare_panel_list' => [
        'variables' => [
          'items' => [],
          'count' => 0,
          'max_items' => 4,
          'min_items' => 2,
          'remaining_slots' => 0,
        ],
        'template' => 'ps-compare-panel-list',
      ],
      'ps_compare_table_column_header' => [
        'variables' => [
          'title' => '',
          'url' => '',
          'address' => '',
          'surface' => '',
          'location' => '',
          'reference' => '',
          'price_amount' => '',
          'price_qualifiers' => '',
          'gallery' => NULL,
          'favorite' => NULL,
          'remove' => NULL,
          'show_actions' => TRUE,
        ],
        'template' => 'ps-compare-table-column-header',
      ],
      'ps_compare_table' => [
        'variables' => [
          'title' => NULL,
          'context' => 'page',
          'columns' => [],
          'sections' => [],
          'photos_label' => NULL,
          'summary' => NULL,
          'display' => [],
          'toolbar' => NULL,
          'compare_url' => NULL,
        ],
        'template' => 'ps-compare-table',
      ],
      'ps_compare_table_email' => [
        'variables' => [
          'title' => NULL,
          'context' => 'email',
          'columns' => [],
          'sections' => [],
          'photos_label' => NULL,
          'summary' => NULL,
          'display' => [],
          'toolbar' => NULL,
          'compare_url' => NULL,
        ],
        'template' => 'ps-compare-table-email',
      ],
      'ps_compare_gallery_carousel' => [
        'variables' => [
          'images' => [],
          'entity_id' => NULL,
          'alt' => '',
        ],
        'template' => 'ps-compare-gallery-carousel',
      ],
      'ps_compare_toolbar' => [
        'variables' => [
          'context' => 'page',
          'share_enabled' => TRUE,
          'compare_url' => NULL,
        ],
        'template' => 'ps-compare-toolbar',
      ],
      'ps_compare_email' => [
        'variables' => [
          'title' => NULL,
          'intro_message' => NULL,
          'compare_url' => NULL,
          'mobile_cards' => NULL,
          'table' => NULL,
        ],
        'template' => 'ps-compare-email',
      ],
      'ps_compare_email_mobile_cards' => [
        'variables' => [
          'columns' => [],
        ],
        'template' => 'ps-compare-email-mobile-cards',
      ],
      'ps_compare_share_modal' => [
        'template' => 'ps-compare-share-modal',
      ],
      'ps_compare_share_offcanvas' => [
        'variables' => [
          'webform' => NULL,
        ],
        'template' => 'ps-compare-share-offcanvas',
      ],
      'ps_compare_share_success' => [
        'variables' => [
          'message' => '',
        ],
        'template' => 'ps-compare-share-success',
      ],
      'ps_compare_modal' => [
        'variables' => [
          'share_enabled' => TRUE,
        ],
        'template' => 'ps-compare-modal',
      ],
    ];
  }

  /**
 *
 */
  #[Hook('entity_predelete')]
  public function entityPredelete(EntityInterface $entity): void {
    $this->compareRepository->removeByEntity($entity->getEntityTypeId(), (int) $entity->id());
  }

  /**
 *
 */
  #[Hook('user_login')]
  public function userLogin(UserInterface $account): void {
    $this->compareManager->mergeAnonymousCompare();
  }

  /**
   * Injects design tokens into the compare email body fragment.
   */
  #[Hook('preprocess_ps_compare_email')]
  public function preprocessPsCompareEmail(array &$variables): void {
    $variables += $this->emailDesignTokens->getPreprocessVariables();
  }

  /**
   * Implements hook_mail().
   */
  #[Hook('mail')]
  public function mail(string $key, array &$message, array $params): void {
    if ($key !== 'comparison') {
      return;
    }

    $message['subject'] = (string) ($params['subject'] ?? '');
    $message['body'][] = Markup::create((string) ($params['body'] ?? ''));
    $message['headers']['Content-Type'] = 'text/html; charset=UTF-8';
  }

}
