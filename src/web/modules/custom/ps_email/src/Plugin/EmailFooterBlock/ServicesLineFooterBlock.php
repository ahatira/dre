<?php

declare(strict_types=1);

namespace Drupal\ps_email\Plugin\EmailFooterBlock;

/**
 * Renders the services line in the footer.
 */
final class ServicesLineFooterBlock extends EmailFooterBlockBase {

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    return (string) $this->t('Services line');
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $settings, ?string $langcode = NULL): string {
    $services = $this->shellScalar('services', $langcode, 'services');
    return $services !== '' ? $this->wrapFooterCell($services, '0') : '';
  }

}
