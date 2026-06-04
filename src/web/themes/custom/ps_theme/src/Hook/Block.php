<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;

/**
 * Block preprocess for Stellar header/footer branding.
 */
final class Block {

  /**
   * Header account menu — compact slot in actions bar.
   */
  #[Hook('preprocess_block')]
  public function preprocessBlock(array &$variables): void {
    $block_id = $variables['elements']['#id'] ?? '';

    if ($block_id === 'ps_theme_main_menu') {
      $instance = (string) (\Drupal::request()->attributes->get('ps_mega_menu_instance') ?? '');
      if ($instance !== '') {
        $variables['attributes']['class'][] = 'ps-site-header__nav-main--' . $instance;
        $variables['attributes']['data-ps-menu-instance'] = $instance;
      }
    }

    if ($block_id === 'ps_theme_header_account') {
      $variables['attributes']['class'][] = 'ps-header-actions__account-block';
    }

  }

  /**
   * Header branding — flatten block wrapper for navbar grid.
   */
  #[Hook('preprocess_block__system_branding_block')]
  public function preprocessHeaderBranding(array &$variables): void {
    if (($variables['elements']['#id'] ?? '') !== 'ps_theme_branding') {
      return;
    }

    $variables['attributes']['class'][] = 'ps-site-header__brand-slot';
  }

  /**
   * Footer branding — swap logo to footer wordmark asset.
   */
  #[Hook('preprocess_block__ps_theme_footer_branding')]
  public function preprocessFooterBranding(array &$variables): void {
    $theme_path = \Drupal::service('extension.list.theme')->getPath('ps_theme');
    $variables['site_logo'] = Url::fromUri('base:' . $theme_path . '/assets/images/logo/footer-logo.svg')->toString();
    $variables['site_name'] = NULL;
    $variables['site_slogan'] = NULL;
  }

}
