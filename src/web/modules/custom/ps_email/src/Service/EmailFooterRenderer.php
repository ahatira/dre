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
   * Builds footer markup for HTML (ps_theme) and MJML (ps_theme_email).
   *
   * @return array<string, mixed>
   *   Footer variables for email-wrap preprocessing.
   */
  public function buildFooterVariables(?string $langcode = NULL): array {
    $footerDark = (string) ($this->configFactory->get(self::TOKENS_CONFIG)->get('footer_dark_color') ?: '#1f2a36');
    $legalBg = (string) ($this->configFactory->get(self::TOKENS_CONFIG)->get('background_color') ?: '#f0f0f0');
    $legalMuted = (string) ($this->configFactory->get(self::TOKENS_CONFIG)->get('muted_color') ?: '#777e83');

    $zones = $this->footerContentSettings->getProcessedZones($langcode);
    $footerInner = $this->markupBuilder->wrapFooterColumns($zones['left'], $zones['right'], $footerDark);
    $legalInner = $zones['legal'];

    $footerLeft = $zones['left'] !== ''
      ? Markup::create($this->markupBuilder->applyInlineTextColor($zones['left'], '#ffffff'))
      : NULL;
    $footerRight = $zones['right'] !== ''
      ? Markup::create($this->markupBuilder->applyInlineTextColor($zones['right'], '#ffffff'))
      : NULL;
    $legalStyled = $legalInner !== ''
      ? Markup::create($this->markupBuilder->applyInlineTextColor($legalInner, $legalMuted))
      : NULL;

    return [
      'email_footer' => $footerInner !== '' ? Markup::create($footerInner) : NULL,
      'email_footer_left' => $footerLeft,
      'email_footer_right' => $footerRight,
      'email_legal' => $legalInner !== ''
        ? Markup::create($this->markupBuilder->wrapLegalZone($legalInner, $legalBg, $legalMuted))
        : NULL,
      'email_legal_inner' => $legalStyled,
      'ps_email_rich_footer' => $footerInner !== '' || $legalInner !== '',
    ];
  }

}
