<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_email\Unit;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\ps_email\Service\EmailDesignTokens;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_email\Service\EmailDesignTokens
 * @group ps_email
 */
final class EmailDesignTokensTest extends UnitTestCase {

  /**
   * @covers ::getPreprocessVariables
   * @covers ::getPrimaryColor
   */
  public function testGetPreprocessVariables(): void {
    $config = new Config('ps_email.email_tokens', $this->createMock(StorageInterface::class));
    $config->setData([
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

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_email.email_tokens')->willReturn($config);

    $tokens = new EmailDesignTokens($configFactory);
    $variables = $tokens->getPreprocessVariables();

    $this->assertSame('#00915a', $variables['email_primary_color']);
    $this->assertSame(600, $variables['email_max_width']);
    $this->assertSame('#00915a', $tokens->getPrimaryColor());
  }

}
