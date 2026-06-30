<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Render\Markup;

/**
 * Renders the email footer from ps_email.footer WYSIWYG config.
 */
final class EmailFooterRenderer {

  private const TOKENS_CONFIG = 'ps_email.email_tokens';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EmailFooterContentSettings $footerContentSettings,
    private readonly EmailFooterMarkupBuilder $markupBuilder,
  ) {}

  /**
   * Builds email_footer and email_legal markup for the email wrapper.
   *
   * @return array{email_footer: \Drupal\Component\Render\MarkupInterface|null, email_legal: \Drupal\Component\Render\MarkupInterface|null, ps_email_rich_footer: bool}
   *   Footer variables for email-wrap preprocessing.
   */
  public function buildFooterVariables(?string $langcode = NULL): array {
    $footerDark = (string) ($this->configFactory->get(self::TOKENS_CONFIG)->get('footer_dark_color') ?: '#1f2a36');
    $legalBg = (string) ($this->configFactory->get(self::TOKENS_CONFIG)->get('background_color') ?: '#f0f0f0');
    $legalMuted = (string) ($this->configFactory->get(self::TOKENS_CONFIG)->get('muted_color') ?: '#777e83');

    $zones = $this->footerContentSettings->getProcessedZones($langcode);
    $footerInner = $this->markupBuilder->wrapFooterColumns($zones['left'], $zones['right'], $footerDark);
    $legalInner = $zones['legal'];

    return [
      'email_footer' => $footerInner !== '' ? Markup::create($footerInner) : NULL,
      'email_legal' => $legalInner !== ''
        ? Markup::create($this->markupBuilder->wrapLegalZone($legalInner, $legalBg, $legalMuted))
        : NULL,
      'ps_email_rich_footer' => $footerInner !== '' || $legalInner !== '',
    ];
  }

}
