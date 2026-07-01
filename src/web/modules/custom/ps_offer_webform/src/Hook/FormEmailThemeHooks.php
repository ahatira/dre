<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Hook;

use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Email-related theme hooks for ps_offer_webform.
 */
final class FormEmailThemeHooks {

  public function __construct(
    private readonly ModuleExtensionList $moduleExtensionList,
  ) {}

  /**
   * Registers offer contact email fragment templates.
   */
  #[Hook('theme')]
  public function theme(): array {
    $path = $this->moduleExtensionList->getPath('ps_offer_webform') . '/templates';

    return [
      'ps_offer_contact_email_block' => [
        'variables' => [
          'card' => NULL,
          'offer_url' => NULL,
          'cta_label' => NULL,
        ],
        'template' => 'ps-offer-contact-email-block',
        'path' => $path,
      ],
      'ps_offer_snapshot_email_recap' => [
        'variables' => [
          'rows' => [],
        ],
        'template' => 'ps-offer-snapshot-email-recap',
        'path' => $path,
      ],
      'ps_schedule_visit_availabilities_email_block' => [
        'variables' => [
          'title' => NULL,
          'dates' => [],
        ],
        'template' => 'ps-schedule-visit-availabilities-email-block',
        'path' => $path,
      ],
    ];
  }

}
