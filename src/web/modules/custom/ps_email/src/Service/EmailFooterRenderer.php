<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\TranslationInterface;

/**
 * Renders the email legal footer from ps_email.footer config.
 */
final class EmailFooterRenderer {

  private const TOKENS_CONFIG = 'ps_email.email_tokens';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EmailFooterContentSettings $footerContentSettings,
    private readonly EmailFooterMarkupBuilder $markupBuilder,
    private readonly TranslationInterface $translation,
  ) {}

  /**
   * Builds footer shell variables for email-wrap preprocessing.
   *
   * @return array<string, mixed>
   *   Footer variables for email-wrap preprocessing.
   */
  public function buildFooterVariables(?string $langcode = NULL): array {
    $tokens = $this->configFactory->get(self::TOKENS_CONFIG);
    $primary = (string) ($tokens->get('primary_color') ?: '#00915a');
    $legalBg = (string) ($tokens->get('background_color') ?: '#f9f9fb');
    $legalMuted = (string) ($tokens->get('muted_color') ?: '#777e83');
    $fontFamily = (string) ($tokens->get('font_family') ?: "'BNP Sans','Open Sans',Arial,sans-serif");

    $gdprHtml = $this->footerContentSettings->getProcessedLegalHtml($langcode);
    $corporateLine = $this->footerContentSettings->getCorporateLine($langcode);

    $siteName = (string) ($this->configFactory->get('system.site')->get('name') ?: 'Property Search');
    $systemLine = (string) $this->translation->translate('This message was sent by @site.', [
      '@site' => $siteName,
    ], ['langcode' => $langcode ?? NULL]);

    $accentRule = $this->markupBuilder->buildGreenAccentRule($primary);
    $legalBlock = $this->markupBuilder->wrapLegalFooterBlock(
      $gdprHtml,
      $corporateLine,
      $systemLine,
      $legalBg,
      $legalMuted,
      $primary,
      $fontFamily,
    );

    return [
      'email_accent_rule' => Markup::create($accentRule),
      'email_legal' => $legalBlock !== '' ? Markup::create($legalBlock) : NULL,
      'ps_email_rich_footer' => $legalBlock !== '',
    ];
  }

}
