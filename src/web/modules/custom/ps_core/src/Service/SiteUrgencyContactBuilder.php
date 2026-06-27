<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Builds the site-wide urgency contact block (phone + hours).
 */
final class SiteUrgencyContactBuilder {

  use StringTranslationTrait;

  private const CONFIG = 'ps_core.settings';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Whether the urgency contact block should be displayed.
   */
  public function isEnabled(): bool {
    $config = $this->getConfig();
    if (!(bool) $config->get('urgency_help_enabled')) {
      return FALSE;
    }

    return trim((string) $config->get('urgency_help_phone')) !== '';
  }

  /**
   * Builds a render array for the urgency contact block.
   *
   * @return array<string, mixed>
   *   Empty when disabled or misconfigured.
   */
  public function buildRenderArray(): array {
    if (!$this->isEnabled()) {
      return [];
    }

    $config = $this->getConfig();
    $phoneDisplay = trim((string) $config->get('urgency_help_phone'));
    $phoneLink = trim((string) $config->get('urgency_help_phone_link'));
    if ($phoneLink === '') {
      $phoneLink = $this->normalizePhoneLink($phoneDisplay);
    }

    $lead = trim((string) $config->get('urgency_help_lead'));
    if ($lead === '') {
      $lead = (string) $this->t('In a hurry? Call us at');
    }

    return [
      '#type' => 'component',
      '#component' => 'ps_theme:webform-urgency-help',
      '#props' => [
        'lead' => $lead,
        'phone_display' => $phoneDisplay,
        'phone_href' => $phoneLink,
        'hours' => trim((string) $config->get('urgency_help_hours')),
      ],
      '#cache' => [
        'tags' => $config->getCacheTags(),
      ],
    ];
  }

  /**
   * Normalizes a display phone number into a tel: href.
   */
  public function normalizePhoneLink(string $phone): string {
    $digits = preg_replace('/[^\d+]/', '', $phone) ?? '';
    if ($digits === '') {
      return '';
    }

    if (!str_starts_with($digits, '+') && str_starts_with($digits, '0')) {
      $digits = '+33' . substr($digits, 1);
    }

    return 'tel:' . $digits;
  }

  /**
   * Loads urgency contact settings.
   */
  private function getConfig(): ImmutableConfig {
    return $this->configFactory->get(self::CONFIG);
  }

}
