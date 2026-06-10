<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Views data alterations for Search API integration.
 */
final class SearchViewsDataHooks {

  /**
   * Implements hook_views_data_alter().
   */
  #[Hook('views_data_alter')]
  public function viewsDataAlter(array &$data): void {
    if (!isset($data['search_api_index_offers']['field_address_locality']['filter'])) {
      return;
    }

    $data['search_api_index_offers']['field_address_locality']['filter']['id'] = 'ps_search_location_tokens';
  }

}
