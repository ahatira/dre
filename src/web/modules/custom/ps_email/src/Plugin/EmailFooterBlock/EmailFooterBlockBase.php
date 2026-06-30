<?php

declare(strict_types=1);

namespace Drupal\ps_email\Plugin\EmailFooterBlock;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\ps_email\Service\EmailShellSettings;

/**
 * Base helpers for email footer block plugins.
 */
abstract class EmailFooterBlockBase implements EmailFooterBlockInterface {

  use StringTranslationTrait;

  public function __construct(
    protected readonly EmailShellSettings $emailShellSettings,
    protected readonly ConfigFactoryInterface $configFactory,
    protected readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Loads social links from the theme footer block.
   *
   * @return list<array{label: string, url: string}>
   */
  protected function loadSocialLinks(): array {
    $links = [];
    $block = $this->entityTypeManager->getStorage('block')->load('ps_theme_footer_social');
    if ($block === NULL) {
      return $links;
    }

    $settings = $block->get('settings') ?? [];
    $platforms = is_array($settings['platforms'] ?? NULL) ? $settings['platforms'] : [];
    foreach ($platforms as $platform => $data) {
      if (!is_array($data)) {
        continue;
      }
      $value = trim((string) ($data['value'] ?? ''));
      if ($value === '') {
        continue;
      }
      $label = trim((string) ($data['description'] ?? ucfirst((string) $platform)));
      $url = str_contains($value, '://') ? $value : match ($platform) {
        'linkedin' => 'https://www.linkedin.com/' . ltrim($value, '/'),
        'youtube' => 'https://www.youtube.com/' . ltrim($value, '/'),
        'twitter', 'x' => 'https://twitter.com/' . ltrim($value, '/'),
        'email' => 'mailto:' . $value,
        default => $value,
      };
      $links[] = ['label' => $label, 'url' => $url];
    }

    return $links;
  }

  /**
   * Resolves shell scalar with optional site footer reuse.
   */
  protected function shellScalar(string $key, ?string $langcode, string $socialKey = ''): string {
    $value = $this->emailShellSettings->getFooterScalar($key, $langcode);
    if ($value !== '' || !$this->emailShellSettings->reuseSiteFooter() || $socialKey === '') {
      return $value;
    }

    $block = $this->entityTypeManager->getStorage('block')->load('ps_theme_footer_social');
    if ($block === NULL) {
      return '';
    }

    $settings = $block->get('settings') ?? [];
    return trim((string) ($settings[$socialKey] ?? ''));
  }

  /**
   * Wraps inner HTML in a table row cell for the dark footer zone.
   */
  protected function wrapFooterCell(string $inner, string $padding = '0 0 8px'): string {
    if ($inner === '') {
      return '';
    }

    return '<p style="margin:0;padding:' . $padding . ';font-size:12px;line-height:1.5;color:#ffffff;">' . $inner . '</p>';
  }

  /**
   * Builds default offers URL when empty.
   */
  protected function offersUrl(string $configured): string {
    if ($configured !== '') {
      return $configured;
    }

    return Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();
  }

}
