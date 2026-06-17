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
   * Footer social — map social_media_links block to footer-social SDC.
   */
  #[Hook('preprocess_block__ps_theme_footer_social')]
  public function preprocessFooterSocial(array &$variables): void {
    $configuration = $variables['configuration'] ?? [];
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $override = \Drupal::languageManager()->getLanguageConfigOverride($langcode, 'block.block.ps_theme_footer_social');
    if ($label = $override->get('settings.label')) {
      $configuration['label'] = $label;
    }
    $platformOverrides = $override->get('settings.platforms');
    if (is_array($platformOverrides)) {
      $configuration['platforms'] = array_replace_recursive($configuration['platforms'] ?? [], $platformOverrides);
    }

    $platforms = $configuration['platforms'] ?? [];
    if ($platforms === []) {
      return;
    }

    $title = trim((string) ($configuration['label'] ?? ''));
    if ($title === '') {
      $title = (string) t('Follow us');
    }

    $icon_map = [
      'linkedin' => 'linkedin',
      'email' => 'mail-outline',
    ];
    $url_builders = [
      'linkedin' => static fn(string $value): string => 'https://www.linkedin.com/' . ltrim($value, '/'),
      'email' => static fn(string $value): string => 'mailto:' . $value,
    ];

    $platform_ids = array_keys($platforms);
    usort($platform_ids, static function (string $a, string $b) use ($platforms): int {
      return ((int) ($platforms[$a]['weight'] ?? 0)) <=> ((int) ($platforms[$b]['weight'] ?? 0));
    });

    $links = [];
    foreach ($platform_ids as $platform_id) {
      $platform = $platforms[$platform_id];
      $value = trim((string) ($platform['value'] ?? ''));
      if ($value === '' || !isset($icon_map[$platform_id], $url_builders[$platform_id])) {
        continue;
      }

      $attributes = '';
      if ($platform_id === 'linkedin') {
        $attributes = 'target="_blank" rel="noopener noreferrer"';
      }

      $linkTitle = trim((string) ($platform['description'] ?? ''));
      if ($linkTitle === '') {
        $linkTitle = $platform_id === 'email' ? (string) t('Email alerts') : $platform_id;
      }

      $links[] = [
        'title' => $linkTitle,
        'url' => $url_builders[$platform_id]($value),
        'icon' => $icon_map[$platform_id],
        'attributes' => $attributes,
      ];
    }

    if ($links === []) {
      return;
    }

    $variables['ps_footer_social'] = [
      'title' => $title,
      'links' => $links,
    ];
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
