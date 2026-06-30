<?php

declare(strict_types=1);

namespace Drupal\ps_email\Plugin\EmailFooterBlock;

/**
 * Renders the footer phone line.
 */
final class PhoneFooterBlock extends EmailFooterBlockBase {

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    return (string) $this->t('Phone');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $settings, ?string $langcode = NULL): string {
    $phone = $this->shellScalar('phone', $langcode, 'phone');
    if ($phone === '') {
      return '';
    }

    $phoneLink = $this->shellScalar('phone_link', $langcode, 'phone_link');
    if ($phoneLink === '') {
      $phoneLink = 'tel:' . preg_replace('/\s+/', '', $phone);
    }

    $inner = '<a href="' . htmlspecialchars($phoneLink, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" style="color:#ffffff;text-decoration:none;">' . htmlspecialchars($phone, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a>';
    return $this->wrapFooterCell($inner, '0');
  }

}
