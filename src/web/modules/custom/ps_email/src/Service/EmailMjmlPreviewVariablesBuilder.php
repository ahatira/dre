<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Builds Drupal variables for ps_theme_email MJML Devel preview templates.
 *
 * Mirrors preprocess_email_* hooks so mjml_render_devel previews match sends.
 */
final class EmailMjmlPreviewVariablesBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly LanguageManagerInterface $languageManager,
    private readonly EmailBrandingBuilder $emailBrandingBuilder,
    private readonly EmailDesignTokens $emailDesignTokens,
    private readonly EmailFooterRenderer $emailFooterRenderer,
    private readonly OfferEmailCardHtmlRenderer $offerEmailCardHtmlRenderer,
  ) {}

  /**
   * Builds preview variables for a ps_theme_email MJML template base name.
   *
   * @param string $templateName
   *   Template id without extension (e.g. email-shell-preview).
   * @param string|null $langcode
   *   Optional language for translated config and strings.
   *
   * @return array<string, mixed>
   *   Twig variables merged into mjml_render_devel mock data.
   */
  public function buildForTemplate(string $templateName, ?string $langcode = NULL): array {
    $langcode ??= $this->languageManager->getCurrentLanguage()->getId();
    $variables = [];

    $this->applySharedShellVariables($variables, $langcode);

    match ($templateName) {
      'email-shell-preview' => $variables += $this->buildShellPreviewDefaults(),
      'email-partials-preview' => $variables += [
        ...$this->buildShellPreviewDefaults(),
        ...$this->buildPartialsPreviewSamples(),
      ],
      'email-compare-preview' => $variables += $this->buildComparePreviewDefaults(),
      'email-search-alert-preview' => $variables += $this->buildSearchAlertPreviewDefaults(),
      'email-offer-cards-preview' => $variables += $this->buildOfferCardsPreviewDefaults(),
      'email-import-alert-preview' => $variables += $this->buildImportAlertPreviewDefaults(),
      default => NULL,
    };

    $this->moduleHandler->invokeAll('ps_email_mjml_preview_variables_alter', [
      &$variables,
      $templateName,
      $langcode,
    ]);

    return $variables;
  }

  /**
   * Applies branding, tokens and rich footer (same as live HTML emails).
   *
   * @param array<string, mixed> $variables
   *   Template variables.
   * @param string|null $langcode
   *   Optional language code.
   */
  public function applySharedShellVariables(array &$variables, ?string $langcode = NULL): void {
    $langcode ??= $this->languageManager->getCurrentLanguage()->getId();
    $variables += $this->emailDesignTokens->getPreprocessVariables();
    $variables += $this->buildBrandingVariables($langcode);
    $this->applyRichFooter($variables, $langcode);
  }

  /**
   * Applies the global rich footer shell to HTML preview templates.
   *
   * @param array<string, mixed> $variables
   *   Template variables.
   * @param string|null $langcode
   *   Optional language code.
   */
  public function applyRichFooter(array &$variables, ?string $langcode = NULL): void {
    if (($variables['is_html'] ?? TRUE) !== TRUE) {
      return;
    }

    $variables += $this->emailFooterRenderer->buildFooterVariables($langcode);
    if ($variables['ps_email_rich_footer'] ?? FALSE) {
      $variables['email_hide_default_signoff'] = TRUE;
    }
  }

  /**
   * Default variables for email-shell-preview and email-partials-preview.
   *
   * @return array<string, mixed>
   *   Shell preview defaults.
   */
  public function buildShellPreviewDefaults(): array {
    return [
      'subject' => (string) $this->t('Your request has been sent'),
      'body' => Markup::create('<p>' . (string) $this->t('Sample email body for MJML preview.') . '</p>'),
      'is_html' => TRUE,
      'email_display_title' => (string) $this->t('Your request has been sent'),
      'ps_contact_confirmation' => FALSE,
    ];
  }

  /**
   * Sample partials data for email-partials-preview.
   *
   * @return array<string, mixed>
   *   Partial showcase variables.
   */
  public function buildPartialsPreviewSamples(): array {
    $siteUrl = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();

    return [
      'preview_subject' => (string) $this->t('Email partials preview'),
      'preview_alert_title' => (string) $this->t('Success'),
      'preview_alert_message' => Markup::create('<p>' . (string) $this->t('Your request has been registered successfully.') . '</p>'),
      'preview_card_title' => (string) $this->t('Property summary'),
      'preview_card_body' => Markup::create(
        '<p><strong>' . (string) $this->t('Office space — Paris 8') . '</strong></p>'
        . '<p>' . (string) $this->t('Surface: 450 m² · Rent: on request') . '</p>'
      ),
      'preview_card_footer_url' => $siteUrl,
      'preview_card_footer_label' => (string) $this->t('View on Property Search'),
      'preview_button_url' => $siteUrl . '/compare/share/example-token',
      'preview_button_label' => (string) $this->t('View comparison'),
      'preview_info_title' => (string) $this->t('Note'),
      'preview_info_message' => Markup::create('<p>' . (string) $this->t('Table partial uses the same columns/sections model as ps_compare.') . '</p>'),
      'preview_table_columns' => [
        ['header' => Markup::create('<strong>Offer A</strong>')],
        ['header' => Markup::create('<strong>Offer B</strong>')],
      ],
      'preview_table_sections' => [
        [
          'label' => (string) $this->t('Key figures'),
          'rows' => [
            [
              'label' => (string) $this->t('Surface'),
              'cells' => ['450 m²', '320 m²'],
            ],
            [
              'label' => (string) $this->t('City'),
              'cells' => ['Paris', 'Lyon'],
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * Default variables for email-compare-preview.
   *
   * @return array<string, mixed>
   *   Compare preview defaults.
   */
  public function buildComparePreviewDefaults(): array {
    return [
      'subject' => (string) $this->t('Property comparison'),
      'intro_message' => (string) $this->t('Here is the property comparison you requested.'),
      'is_html' => TRUE,
    ];
  }

  /**
   * Default variables for email-search-alert-preview.
   *
   * @return array<string, mixed>
   *   Search alert preview defaults.
   */
  public function buildSearchAlertPreviewDefaults(): array {
    $sampleProps = $this->sampleOfferCardProps();
    return [
      'body' => Markup::create(
        '<p style="margin:0 0 16px;font-size:14px;line-height:1.6;font-weight:700;">'
        . (string) $this->t('New matching properties:')
        . '</p>'
        . $this->offerEmailCardHtmlRenderer->renderCompact($sampleProps)
        . $this->offerEmailCardHtmlRenderer->renderCompact(array_merge($sampleProps, [
          'title' => (string) $this->t('Retail — Lyon Part-Dieu'),
          'reference' => 'REF-002',
        ])),
      ),
      'subject' => (string) $this->t('3 new properties for your alert'),
      'is_html' => TRUE,
    ];
  }

  /**
   * Default variables for email-offer-cards-preview.
   *
   * @return array<string, mixed>
   */
  public function buildOfferCardsPreviewDefaults(): array {
    $sampleProps = $this->sampleOfferCardProps();
    return [
      'subject' => (string) $this->t('Offer email cards preview'),
      'body' => Markup::create(
        $this->offerEmailCardHtmlRenderer->renderVertical($sampleProps)
        . $this->offerEmailCardHtmlRenderer->renderCompact($sampleProps),
      ),
      'is_html' => TRUE,
    ];
  }

  /**
   * @return array<string, mixed>
   */
  private function sampleOfferCardProps(): array {
    $siteUrl = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();

    return [
      'title' => (string) $this->t('Office — Paris 8'),
      'reference' => 'REF-001',
      'property_type' => (string) $this->t('Office'),
      'surface' => '450 m²',
      'location' => '75008 PARIS',
      'price_amount' => '',
      'price_qualifiers' => '',
      'price_on_request_label' => (string) $this->t('Price on request'),
      'exclusive' => TRUE,
      'url' => $siteUrl,
      'cta_label' => (string) $this->t('View the property'),
      'image' => NULL,
      'image_alt' => (string) $this->t('Office — Paris 8'),
    ];
  }

  /**
   * Default variables for email-import-alert-preview.
   *
   * @return array<string, mixed>
   *   Import alert preview defaults.
   */
  public function buildImportAlertPreviewDefaults(): array {
    return [
      'subject' => (string) $this->t('CRM import failed: sample-file.xml'),
      'is_html' => TRUE,
    ];
  }

  /**
   * Builds shared site branding variables for email templates.
   *
   * @return array<string, mixed>
   *   Branding variables.
   */
  public function buildBrandingVariables(?string $langcode = NULL): array {
    $langcode ??= $this->languageManager->getCurrentLanguage()->getId();
    $siteConfig = $this->configFactory->get('system.site');
    $siteName = (string) ($siteConfig->get('name') ?? 'Property Search');

    return [
      'site_name' => $siteName,
      'site_slogan' => $this->emailBrandingBuilder->getSiteSlogan($langcode),
      'site_url' => Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(),
      'email_team_name' => (string) $this->t('The @team team', ['@team' => $siteName]),
      'email_logo_url' => $this->emailBrandingBuilder->getHeaderLogoUrl(),
    ];
  }

}
