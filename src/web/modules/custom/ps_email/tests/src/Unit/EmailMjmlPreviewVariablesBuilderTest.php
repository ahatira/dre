<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_email\Unit;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_email\Service\EmailBrandingBuilder;
use Drupal\ps_email\Service\EmailDesignTokens;
use Drupal\ps_email\Service\EmailMjmlPreviewVariablesBuilder;
use Drupal\ps_email\Service\EmailRichFooterBuilder;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_email\Service\EmailMjmlPreviewVariablesBuilder
 * @group ps_email
 */
final class EmailMjmlPreviewVariablesBuilderTest extends UnitTestCase {

  /**
   * @covers ::buildForTemplate
   * @covers ::applySharedShellVariables
   */
  public function testBuildForShellPreviewIncludesBrandingAndFooterFlags(): void {
    $builder = $this->createBuilder(
      slogan: 'Real Estate for a Changing World',
      logoUrl: 'http://example.com/themes/custom/ps_theme/assets/images/logo/header-logo.svg',
      legalMarkup: '<p>Data Protection Notice</p>',
    );

    $variables = $builder->buildForTemplate('email-shell-preview');

    $this->assertSame('Real Estate for a Changing World', $variables['site_slogan']);
    $this->assertSame('http://example.com/themes/custom/ps_theme/assets/images/logo/header-logo.svg', $variables['email_logo_url']);
    $this->assertTrue($variables['ps_email_rich_footer']);
    $this->assertTrue($variables['email_hide_default_signoff']);
    $this->assertSame('<p>Data Protection Notice</p>', $variables['ps_contact_footer_legal']);
    $this->assertSame('Your request has been sent', $variables['subject']);
  }

  /**
   * @covers ::buildForTemplate
   */
  public function testAlterHookCanExtendVariables(): void {
    $moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $moduleHandler->expects($this->once())
      ->method('invokeAll')
      ->willReturnCallback(static function (string $hook, array $args): array {
        \PHPUnit\Framework\Assert::assertSame('ps_email_mjml_preview_variables_alter', $hook);
        \PHPUnit\Framework\Assert::assertSame('email-contact-preview', $args[1]);
        \PHPUnit\Framework\Assert::assertSame('en', $args[2]);
        $args[0]['custom_flag'] = TRUE;
        return [];
      });

    $builder = new EmailMjmlPreviewVariablesBuilder(
      $this->createConfigFactory(),
      $moduleHandler,
      $this->createLanguageManager(),
      $this->createBrandingBuilder(),
      new EmailDesignTokens($this->createConfigFactory()),
      $this->createRichFooterBuilder(),
    );

    $variables = $builder->buildForTemplate('email-contact-preview', 'en');
    $this->assertTrue($variables['custom_flag']);
  }

  /**
   * Creates a builder with mocked branding/footer dependencies.
   */
  private function createBuilder(
    string $slogan = '',
    ?string $logoUrl = NULL,
    string $legalMarkup = '',
  ): EmailMjmlPreviewVariablesBuilder {
    return new EmailMjmlPreviewVariablesBuilder(
      $this->createConfigFactory(),
      $this->createMock(ModuleHandlerInterface::class),
      $this->createLanguageManager(),
      $this->createBrandingBuilder($slogan, $logoUrl),
      new EmailDesignTokens($this->createConfigFactory()),
      $this->createRichFooterBuilder($legalMarkup),
    );
  }

  /**
   * Creates a config factory mock for system.site and email tokens.
   */
  private function createConfigFactory(): ConfigFactoryInterface {
    $siteConfig = new Config('system.site', $this->createMock(StorageInterface::class));
    $siteConfig->set('name', 'BNP Paribas Real Estate');

    $tokenConfig = new Config('ps_email.email_tokens', $this->createMock(StorageInterface::class));
    $tokenConfig->setData([
      'primary_color' => '#00915a',
      'text_color' => '#333333',
      'muted_color' => '#777e83',
      'background_color' => '#f0f0f0',
      'surface_color' => '#ffffff',
      'footer_dark_color' => '#1f2a36',
      'font_family' => "'BNP Sans',Arial,sans-serif",
      'font_size_base' => '14px',
      'line_height_base' => '1.6',
      'spacing_unit' => 8,
      'max_width' => 600,
      'logo_width' => 162,
      'logo_height' => 31,
    ]);

    $shellConfig = new Config('ps_email.shell', $this->createMock(StorageInterface::class));
    $shellConfig->setData([
      'legal_markup' => '',
      'reuse_site_footer' => FALSE,
    ]);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->willReturnMap([
      ['system.site', $siteConfig],
      ['ps_email.email_tokens', $tokenConfig],
      ['ps_email.shell', $shellConfig],
    ]);

    return $configFactory;
  }

  /**
   * Creates a language manager mock.
   */
  private function createLanguageManager(): LanguageManagerInterface {
    $language = new Language(['id' => 'en']);
    $languageManager = $this->createMock(LanguageManagerInterface::class);
    $languageManager->method('getCurrentLanguage')->willReturn($language);
    return $languageManager;
  }

  /**
   * Creates an email branding builder mock.
   */
  private function createBrandingBuilder(
    string $slogan = '',
    ?string $logoUrl = NULL,
  ): EmailBrandingBuilder {
    $brandingBuilder = $this->createMock(EmailBrandingBuilder::class);
    $brandingBuilder->method('getSiteSlogan')->willReturn($slogan);
    $brandingBuilder->method('getHeaderLogoUrl')->willReturn($logoUrl);
    return $brandingBuilder;
  }

  /**
   * Creates an email rich footer builder mock.
   */
  private function createRichFooterBuilder(string $legalMarkup = ''): EmailRichFooterBuilder {
    $richFooterBuilder = $this->createMock(EmailRichFooterBuilder::class);
    $richFooterBuilder->method('buildFooterVariables')->willReturn([
      'ps_contact_footer_address' => '',
      'ps_contact_footer_phone' => '',
      'ps_contact_footer_phone_link' => '',
      'ps_contact_footer_offers_url' => 'http://example.com',
      'ps_contact_footer_offers_label' => 'Browse listings',
      'ps_contact_footer_services' => '',
      'ps_contact_footer_social_links' => [],
      'ps_contact_footer_legal' => $legalMarkup,
      'ps_contact_footer_site_name' => 'BNP Paribas Real Estate',
    ]);
    return $richFooterBuilder;
  }

}
