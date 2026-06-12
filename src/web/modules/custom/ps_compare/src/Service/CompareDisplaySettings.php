<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;

/**
 * Reads compare page display settings from configuration.
 */
final class CompareDisplaySettings {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * @return array<string, bool>
   */
  public function all(): array {
    $config = $this->config();
    return [
      'show_summary' => (bool) ($config->get('display_show_summary') ?? FALSE),
      'section_nav' => (bool) ($config->get('display_section_nav') ?? TRUE),
      'collapsible_sections' => (bool) ($config->get('display_collapsible_sections') ?? TRUE),
      'collapsible_feature_only' => (bool) ($config->get('display_collapsible_feature_only') ?? TRUE),
      'mobile_cards' => (bool) ($config->get('display_mobile_cards') ?? TRUE),
      'sticky_cta' => (bool) ($config->get('display_sticky_cta') ?? TRUE),
      'price_info' => (bool) ($config->get('display_price_info') ?? TRUE),
      'merge_energy' => (bool) ($config->get('display_merge_energy') ?? TRUE),
      'share_button' => (bool) ($config->get('display_share_button') ?? $config->get('display_email_share') ?? TRUE),
      'undo_removal' => (bool) ($config->get('display_undo_removal') ?? TRUE),
    ];
  }

  /**
   * Display flags adjusted for page, modal, or email rendering.
   *
   * @return array<string, bool>
   */
  public function forContext(string $context): array {
    $display = $this->all();

    if ($context === \Drupal\ps_compare\CompareRenderContext::EMAIL) {
      $display['section_nav'] = FALSE;
      $display['collapsible_sections'] = FALSE;
      $display['mobile_cards'] = FALSE;
      $display['sticky_cta'] = FALSE;
      $display['price_info'] = FALSE;
      $display['share_button'] = FALSE;
    }

    return $display;
  }

  public function shareButton(): bool {
    $config = $this->config();
    return (bool) ($config->get('display_share_button') ?? $config->get('display_email_share') ?? TRUE);
  }

  public function undoRemoval(): bool {
    return $this->all()['undo_removal'];
  }

  /**
   * @deprecated in ps_compare:1.x; use shareButton() instead.
   */
  public function emailShare(): bool {
    return $this->shareButton();
  }

  public function showSummary(): bool {
    return $this->all()['show_summary'];
  }

  public function sectionNav(): bool {
    return $this->all()['section_nav'];
  }

  public function collapsibleSections(): bool {
    return $this->all()['collapsible_sections'];
  }

  public function collapsibleFeatureOnly(): bool {
    return $this->all()['collapsible_feature_only'];
  }

  public function mobileCards(): bool {
    return $this->all()['mobile_cards'];
  }

  public function stickyCta(): bool {
    return $this->all()['sticky_cta'];
  }

  public function priceInfo(): bool {
    return $this->all()['price_info'];
  }

  public function mergeEnergy(): bool {
    return $this->all()['merge_energy'];
  }

  private function config(): ImmutableConfig {
    return $this->configFactory->get('ps_compare.settings');
  }

}
