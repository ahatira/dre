<?php

declare(strict_types=1);

namespace Drupal\ps_email\Plugin\EmailFooterBlock;

/**
 * Renders the footer address line.
 */
final class AddressFooterBlock extends EmailFooterBlockBase {

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    return (string) $this->t('Address');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $settings, ?string $langcode = NULL): string {
    $address = $this->shellScalar('address', $langcode, 'address');
    return $address !== '' ? $this->wrapFooterCell($address) : '';
  }

}
