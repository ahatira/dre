<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

/**
 * Builds email-safe footer HTML table shells for WYSIWYG zones.
 */
final class EmailFooterMarkupBuilder {

  /**
   * Wraps legal inner HTML in the email table shell.
   */
  public function wrapLegalZone(string $inner, string $background, string $muted): string {
    if ($inner === '') {
      return '';
    }

    $inner = $this->applyInlineTextColor($inner, $muted);

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $background . ';"><tr><td style="padding:16px;"><div style="font-size:10px;line-height:1.5;color:' . $muted . ';text-align:justify;">' . $inner . '</div></td></tr></table>';
  }

  /**
   * Wraps contact and links columns in the dark footer table shell.
   */
  public function wrapFooterColumns(string $contactHtml, string $linksHtml, string $footerDark): string {
    if ($contactHtml === '' && $linksHtml === '') {
      return '';
    }

    $contactHtml = $this->applyInlineTextColor($contactHtml, '#ffffff');
    $linksHtml = $this->applyInlineTextColor($linksHtml, '#ffffff');

    $contactCell = $contactHtml !== ''
      ? '<td class="ps-email-footer-col-left" style="padding:24px 16px;color:#ffffff;vertical-align:top;width:50%;">' . $contactHtml . '</td>'
      : '';
    $linksCell = $linksHtml !== ''
      ? '<td class="ps-email-footer-col-right" style="padding:24px 16px;color:#ffffff;vertical-align:top;width:50%;border-left:1px solid #4a5560;">' . $linksHtml . '</td>'
      : '';

    if ($contactCell === '') {
      return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $footerDark . ';"><tr>' . $linksCell . '</tr></table>';
    }
    if ($linksCell === '') {
      return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $footerDark . ';"><tr>' . $contactCell . '</tr></table>';
    }

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $footerDark . ';"><tr>' . $contactCell . $linksCell . '</tr></table>';
  }

  /**
   * Applies inline colors on footer zone copy (p/a tags).
   *
   * MJML sets mj-text color to #333; td color alone fails in mj-raw footers.
   */
  public function applyInlineTextColor(string $html, string $color): string {
    if ($html === '') {
      return '';
    }

    $html = preg_replace('/<p>/i', '<p style="margin:0 0 8px;color:' . $color . ';">', $html) ?? $html;

    $html = preg_replace_callback('/<a(\s[^>]*)>/i', static function (array $matches) use ($color): string {
      $attributes = $matches[1];
      if (preg_match('/style="([^"]*)"/i', $attributes, $styleMatch)) {
        $style = rtrim($styleMatch[1], '; ') . ';color:' . $color . ';';
        return preg_replace('/style="[^"]*"/i', 'style="' . $style . '"', '<a' . $attributes . '>', 1) ?? '<a' . $attributes . '>';
      }

      return '<a style="color:' . $color . ';"' . $attributes . '>';
    }, $html) ?? $html;

    return $html;
  }

}
