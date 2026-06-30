<?php

declare(strict_types=1);

namespace Drupal\ps_email\Plugin\EmailFooterBlock;

/**
 * Renders the offers browse link in the footer.
 */
final class OffersLinkFooterBlock extends EmailFooterBlockBase {

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    return (string) $this->t('Offers link');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $settings, ?string $langcode = NULL): string {
    $offersUrl = $this->shellScalar('offers_url', $langcode);
    $offersLabel = $this->shellScalar('offers_label', $langcode);
    $offersUrl = $this->offersUrl($offersUrl);

    if ($offersLabel === '') {
      $host = parse_url($offersUrl, PHP_URL_HOST) ?: $offersUrl;
      $offersLabel = (string) $this->t('Browse all our listings on @url', ['@url' => $host]);
    }

    $inner = '<a href="' . htmlspecialchars($offersUrl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" style="color:#ffffff;text-decoration:underline;">' . htmlspecialchars($offersLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
    return $this->wrapFooterCell($inner, '0 0 12px');
  }

}
