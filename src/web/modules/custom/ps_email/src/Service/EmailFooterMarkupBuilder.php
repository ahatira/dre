<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

/**
 * Builds email-safe footer HTML for the legal-only shell (variant H).
 */
final class EmailFooterMarkupBuilder {

  /**
   * Builds the 5px primary-color accent rule (Outlook-safe td).
   */
  public function buildGreenAccentRule(string $primaryColor): string {
    $color = htmlspecialchars($primaryColor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">'
      . '<tr>'
      . '<td bgcolor="' . $color . '" height="5" style="height:5px;line-height:5px;font-size:0;mso-line-height-rule:exactly;padding:0;background-color:' . $color . ';border:0;">'
      . '<!--[if mso]><span style="display:block;height:5px;line-height:5px;font-size:0;mso-line-height-rule:exactly;">&nbsp;</span><![endif]-->'
      . '</td>'
      . '</tr>'
      . '</table>';
  }

  /**
   * Wraps GDPR, corporate line and system line in the legal footer shell.
   */
  public function wrapLegalFooterBlock(
    string $gdprHtml,
    string $corporateLine,
    string $systemLine,
    string $background,
    string $muted,
    string $primary,
    string $fontFamily,
  ): string {
    if ($gdprHtml === '' && $corporateLine === '' && $systemLine === '') {
      return '';
    }

    $fontFamily = htmlspecialchars($fontFamily, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $background = htmlspecialchars($background, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $muted = htmlspecialchars($muted, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $primary = htmlspecialchars($primary, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $gdprHtml = $this->applyInlineTextColor($gdprHtml, $muted, $primary, '11px');
    $corporateLine = htmlspecialchars($corporateLine, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $systemLine = htmlspecialchars($systemLine, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $blocks = [];

    if ($gdprHtml !== '') {
      $blocks[] = '<div style="margin:0 0 12px;font-family:' . $fontFamily . ';font-size:11px;line-height:1.5;color:' . $muted . ';">'
        . $gdprHtml
        . '</div>';
    }

    if ($corporateLine !== '') {
      $blocks[] = '<p style="margin:0 0 8px;font-family:' . $fontFamily . ';font-size:10px;line-height:1.5;color:' . $muted . ';">'
        . $corporateLine
        . '</p>';
    }

    if ($systemLine !== '') {
      $blocks[] = '<p style="margin:0;font-family:' . $fontFamily . ';font-size:10px;line-height:1.5;color:#b4babe;">'
        . $systemLine
        . '</p>';
    }

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:' . $background . ';font-size:11px;line-height:1.5;">'
      . '<tr><td style="padding:20px 24px 16px;background:' . $background . ';font-family:' . $fontFamily . ';font-size:11px;line-height:1.5;color:' . $muted . ';text-align:center;">'
      . implode('', $blocks)
      . '</td></tr>'
      . '</table>';
  }

  /**
   * Applies inline colors on legal copy (p/a tags).
   */
  public function applyInlineTextColor(string $html, string $color, string $linkColor, string $fontSize = '14px'): string {
    if ($html === '') {
      return '';
    }

    $paragraphStyle = 'margin:0 0 8px;color:' . $color . ';font-size:' . $fontSize . ';line-height:1.5;';
    $html = preg_replace('/<p>/i', '<p style="' . $paragraphStyle . '">', $html) ?? $html;

    $html = preg_replace_callback('/<a(\s[^>]*)>/i', static function (array $matches) use ($linkColor): string {
      $attributes = $matches[1];
      if (preg_match('/style="([^"]*)"/i', $attributes, $styleMatch)) {
        $style = rtrim($styleMatch[1], '; ') . ';color:' . $linkColor . ';text-decoration:underline;';
        return preg_replace('/style="[^"]*"/i', 'style="' . $style . '"', '<a' . $attributes . '>', 1) ?? '<a' . $attributes . '>';
      }

      return '<a style="color:' . $linkColor . ';text-decoration:underline;"' . $attributes . '>';
    }, $html) ?? $html;

    return $html;
  }

}
