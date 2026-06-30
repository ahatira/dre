<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

/**
 * Identifies hub contact webforms that use the styled confirmation email shell.
 */
final class ContactWebformEmailSettings {

  /**
   * Hub webform ids with styled confirmation emails.
   *
   * @var list<string>
   */
  public const HUB_WEBFORM_IDS = [
    'find_property',
    'entrust_search',
    'get_advice',
    'entrust_property',
    'invest_sell',
    'other_request',
  ];

  /**
   * Offer webforms with email handlers.
   *
   * @var list<string>
   */
  public const OFFER_WEBFORM_IDS = [
    'offer_contact',
    'schedule_visit',
  ];

  /**
   * Returns hub webform ids.
   *
   * @return list<string>
   *   Webform machine names.
   */
  public function getWebformIds(): array {
    return self::HUB_WEBFORM_IDS;
  }

  /**
   * Returns whether a webform uses the styled confirmation email shell.
   */
  public function isHubConfirmationWebform(string $webformId): bool {
    return in_array($webformId, self::HUB_WEBFORM_IDS, TRUE);
  }

  /**
   * Returns whether a webform is an offer contact/visit form with email handlers.
   */
  public function isOfferWebform(string $webformId): bool {
    return in_array($webformId, self::OFFER_WEBFORM_IDS, TRUE);
  }

  /**
   * Returns whether a webform sends styled contact/offer transactional emails.
   */
  public function isContactWebform(string $webformId): bool {
    return $this->isHubConfirmationWebform($webformId) || $this->isOfferWebform($webformId);
  }

}
