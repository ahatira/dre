<?php

declare(strict_types=1);

namespace Drupal\ps_email\Plugin\EmailFooterBlock;

/**
 * Renders social network links in the dark footer zone.
 */
final class SocialLinksFooterBlock extends EmailFooterBlockBase {

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    return (string) $this->t('Social links');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $settings, ?string $langcode = NULL): string {
    $links = $this->loadSocialLinks();
    if ($links === []) {
      return '';
    }

    $parts = [];
    foreach ($links as $link) {
      $parts[] = '<a href="' . htmlspecialchars($link['url'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" style="color:#ffffff;text-decoration:none;margin-right:12px;">' . htmlspecialchars($link['label'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
    }

    return $this->wrapFooterCell(implode('', $parts), '0 0 16px');
  }

}
