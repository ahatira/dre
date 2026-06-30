<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_email\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Render\Markup;
use Drupal\ps_email\Plugin\EmailFooterBlock\EmailFooterBlockInterface;
use Drupal\ps_email\Service\EmailFooterBlockRegistry;
use Drupal\ps_email\Service\EmailFooterLayoutRenderer;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_email\Service\EmailFooterLayoutRenderer
 * @group ps_email
 */
final class EmailFooterLayoutRendererTest extends UnitTestCase {

  /**
   * @covers ::buildLayoutVariables
   */
  public function testBuildLayoutVariablesReturnsFooterAndLegalMarkup(): void {
    $layoutConfig = $this->createMock(ImmutableConfig::class);
    $layoutConfig->method('get')->willReturnCallback(static function (string $key) {
      return match ($key) {
        'footer_components' => [
          [
            'plugin' => 'address',
            'weight' => 0,
            'region' => 'contact',
            'settings' => [],
          ],
        ],
        'legal_components' => [
          [
            'plugin' => 'rich_text',
            'weight' => 0,
            'settings' => ['markup' => '<p>Legal notice</p>'],
          ],
        ],
        default => NULL,
      };
    });

    $tokensConfig = $this->createMock(ImmutableConfig::class);
    $tokensConfig->method('get')->willReturnCallback(static fn (string $key) => match ($key) {
      'footer_dark_color' => '#1f2a36',
      'background_color' => '#f0f0f0',
      'muted_color' => '#777e83',
      default => NULL,
    });

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->willReturnCallback(static function (string $name) use ($layoutConfig, $tokensConfig) {
      return match ($name) {
        'ps_email.footer_layout' => $layoutConfig,
        'ps_email.email_tokens' => $tokensConfig,
        default => $this->createMock(ImmutableConfig::class),
      };
    });

    $addressBlock = $this->createMock(EmailFooterBlockInterface::class);
    $addressBlock->method('build')->willReturn('<p>Address line</p>');
    $richTextBlock = $this->createMock(EmailFooterBlockInterface::class);
    $richTextBlock->method('build')->willReturn('<p>Legal notice</p>');

    $registry = new EmailFooterBlockRegistry([
      'address' => $addressBlock,
      'rich_text' => $richTextBlock,
    ]);

    $renderer = new EmailFooterLayoutRenderer($configFactory, $registry);
    $variables = $renderer->buildLayoutVariables('en');

    self::assertTrue($variables['ps_email_rich_footer']);
    self::assertInstanceOf(Markup::class, $variables['email_footer']);
    self::assertStringContainsString('Address line', (string) $variables['email_footer']);
    self::assertInstanceOf(Markup::class, $variables['email_legal']);
    self::assertStringContainsString('Legal notice', (string) $variables['email_legal']);
  }

}
