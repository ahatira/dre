<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_email\Unit;

use Drupal\ps_email\Service\EmailFooterMarkupBuilder;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_email\Service\EmailFooterMarkupBuilder
 * @group ps_email
 */
final class EmailFooterMarkupBuilderTest extends UnitTestCase {

  /**
   * @covers ::buildGreenAccentRule
   */
  public function testBuildGreenAccentRule(): void {
    $builder = new EmailFooterMarkupBuilder();
    $html = $builder->buildGreenAccentRule('#00915a');

    $this->assertStringContainsString('height:5px', $html);
    $this->assertStringContainsString('bgcolor="#00915a"', $html);
    $this->assertStringContainsString('background-color:#00915a', $html);
  }

  /**
   * @covers ::wrapLegalFooterBlock
   */
  public function testWrapLegalFooterBlockIncludesTypography(): void {
    $builder = new EmailFooterMarkupBuilder();
    $html = $builder->wrapLegalFooterBlock(
      '<p>GDPR text</p>',
      'RCS Nanterre 692 012 180',
      'This message was sent by Property Search.',
      '#f9f9fb',
      '#777e83',
      '#00915a',
      "'BNP Sans','Open Sans',Arial,sans-serif",
    );

    $this->assertStringContainsString('font-family:', $html);
    $this->assertStringContainsString('font-size:11px', $html);
    $this->assertStringContainsString('GDPR text', $html);
    $this->assertStringContainsString('692 012 180', $html);
    $this->assertStringContainsString('Property Search', $html);
  }

}
