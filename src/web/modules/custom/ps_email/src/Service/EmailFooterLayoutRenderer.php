<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Render\Markup;

/**
 * Renders the configurable email footer layout into shell variables.
 */
final class EmailFooterLayoutRenderer {

  private const FOOTER_LAYOUT_CONFIG = 'ps_email.footer_layout';

  private const TOKENS_CONFIG = 'ps_email.email_tokens';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EmailFooterBlockRegistry $emailFooterBlockRegistry,
  ) {}

  /**
   * Builds email_footer and email_legal markup for the email shell.
   *
   * @return array{email_footer: \Drupal\Component\Render\MarkupInterface|null, email_legal: \Drupal\Component\Render\MarkupInterface|null, ps_email_rich_footer: bool}
   *   Layout variables for email-wrap preprocessing.
   */
  public function buildLayoutVariables(?string $langcode = NULL): array {
    $layout = $this->configFactory->get(self::FOOTER_LAYOUT_CONFIG);
    $footerDark = (string) ($this->configFactory->get(self::TOKENS_CONFIG)->get('footer_dark_color') ?: '#1f2a36');
    $legalBg = (string) ($this->configFactory->get(self::TOKENS_CONFIG)->get('background_color') ?: '#f0f0f0');
    $legalMuted = (string) ($this->configFactory->get(self::TOKENS_CONFIG)->get('muted_color') ?: '#777e83');

    $footerComponents = $this->sortComponents($layout->get('footer_components') ?? []);
    $legalComponents = $this->sortComponents($layout->get('legal_components') ?? []);

    $contactHtml = $this->renderRegion($footerComponents, $langcode, 'contact');
    $linksHtml = $this->renderRegion($footerComponents, $langcode, 'links');
    $footerInner = $this->wrapFooterColumns($contactHtml, $linksHtml, $footerDark);
    $legalInner = $this->renderRegion($legalComponents, $langcode);

    return [
      'email_footer' => $footerInner !== '' ? Markup::create($footerInner) : NULL,
      'email_legal' => $legalInner !== '' ? Markup::create($this->wrapLegalZone($legalInner, $legalBg, $legalMuted)) : NULL,
      'ps_email_rich_footer' => $footerInner !== '' || $legalInner !== '',
    ];
  }

  /**
   * @param array<int, array<string, mixed>> $components
   *
   * @return array<int, array<string, mixed>>
   */
  private function sortComponents(array $components): array {
    usort($components, static fn (array $a, array $b): int => ((int) ($a['weight'] ?? 0)) <=> ((int) ($b['weight'] ?? 0)));
    return $components;
  }

  /**
   * @param array<int, array<string, mixed>> $components
   */
  private function renderRegion(array $components, ?string $langcode, ?string $region = NULL): string {
    $parts = [];
    foreach ($components as $component) {
      if ($region !== NULL && (string) ($component['region'] ?? 'contact') !== $region) {
        continue;
      }
      $html = $this->emailFooterBlockRegistry->buildComponent($component, $langcode);
      if ($html !== '') {
        $parts[] = $html;
      }
    }

    return implode('', $parts);
  }

  private function wrapFooterColumns(string $contactHtml, string $linksHtml, string $footerDark): string {
    if ($contactHtml === '' && $linksHtml === '') {
      return '';
    }

    $contactCell = $contactHtml !== ''
      ? '<td style="padding:24px 16px;color:#ffffff;vertical-align:top;width:50%;">' . $contactHtml . '</td>'
      : '';
    $linksCell = $linksHtml !== ''
      ? '<td style="padding:24px 16px;color:#ffffff;vertical-align:top;width:50%;border-left:1px solid #4a5560;">' . $linksHtml . '</td>'
      : '';

    if ($contactCell === '') {
      return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $footerDark . ';"><tr>' . $linksCell . '</tr></table>';
    }
    if ($linksCell === '') {
      return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $footerDark . ';"><tr>' . $contactCell . '</tr></table>';
    }

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $footerDark . ';"><tr>' . $contactCell . $linksCell . '</tr></table>';
  }

  private function wrapLegalZone(string $inner, string $background, string $muted): string {
    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $background . ';"><tr><td style="padding:16px;"><div style="font-size:10px;line-height:1.5;color:' . $muted . ';text-align:justify;">' . $inner . '</div></td></tr></table>';
  }

}
