<?php

declare(strict_types=1);

namespace Drupal\ps_delegate_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\webform\WebformInterface;

/**
 * Controller for delegate search modal.
 */
final class DelegateSearchController extends ControllerBase {

  /**
   * Builds the delegate search webform for modal rendering.
   */
  public function modal(): array {
    if (!$this->moduleHandler()->moduleExists('webform')) {
      return [
        '#markup' => $this->t('Webform module is not available.'),
      ];
    }

    $webform = $this->entityTypeManager()->getStorage('webform')->load('delegate_search');
    if (!$webform instanceof WebformInterface) {
      return [
        '#markup' => $this->t('Delegate search form is not available.'),
      ];
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-delegate-search-modal']],
      'form' => $webform->getSubmissionForm(),
      '#attached' => [
        'library' => ['ps_delegate_search/delegate_search_modal'],
      ],
    ];
  }

  /**
   * Page title callback.
   */
  public function title(): TranslatableMarkup {
    return $this->t('Confier ma recherche');
  }

}
