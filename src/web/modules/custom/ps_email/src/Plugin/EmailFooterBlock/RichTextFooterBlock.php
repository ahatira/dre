<?php

declare(strict_types=1);

namespace Drupal\ps_email\Plugin\EmailFooterBlock;

/**
 * Renders configurable rich text (legal notice or custom markup).
 */
final class RichTextFooterBlock extends EmailFooterBlockBase {

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    return (string) $this->t('Rich text');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $settings, ?string $langcode = NULL): string {
    $markup = trim((string) ($settings['markup'] ?? ''));
    if ($markup === '') {
      $markupKey = trim((string) ($settings['markup_key'] ?? ''));
      if ($markupKey === 'legal_markup') {
        $markup = $this->emailShellSettings->getLegalMarkup($langcode);
      }
      elseif ($markupKey !== '') {
        $markup = $this->emailShellSettings->getText($markupKey, $langcode);
      }
    }

    return $markup;
  }

}
