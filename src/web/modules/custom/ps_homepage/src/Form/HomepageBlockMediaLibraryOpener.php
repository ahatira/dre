<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Session\AccountInterface;
use Drupal\media_library\MediaLibraryOpenerInterface;
use Drupal\media_library\MediaLibraryState;

/**
 * Media library opener for homepage block configuration forms.
 */
final class HomepageBlockMediaLibraryOpener implements MediaLibraryOpenerInterface {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(MediaLibraryState $state, AccountInterface $account): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, [
      'administer ps_media',
      'create media',
      'update media',
      'configure editable page node layout overrides',
      'configure any layout',
    ], 'OR');
  }

  /**
   * {@inheritdoc}
   */
  public function getSelectionResponse(MediaLibraryState $state, array $selected_ids): AjaxResponse {
    $parameters = $state->getOpenerParameters();
    if (empty($parameters['field_widget_id'])) {
      throw new \InvalidArgumentException('field_widget_id parameter is missing.');
    }

    $widget_id = $parameters['field_widget_id'];
    $ids = implode(',', $selected_ids);

    $response = new AjaxResponse();
    return $response
      ->addCommand(new InvokeCommand("[data-media-library-widget-value=\"$widget_id\"]", 'val', [$ids]))
      ->addCommand(new InvokeCommand("[data-media-library-widget-update=\"$widget_id\"]", 'trigger', ['mousedown']));
  }

}
