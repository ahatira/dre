<?php

declare(strict_types=1);

namespace Drupal\ps_content\Service;

use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Url;

/**
 * Default theme image paths for the expert journey steps.
 */
final class ExpertJourneyDefaultAssets {

  /**
   * @var list<string>
   */
  private const STEP_IMAGE_PATHS = [
    'assets/images/expert-journey/step-1-accompagnement.webp',
    'assets/images/expert-journey/step-2-visite.webp',
    'assets/images/expert-journey/step-3-contractualisation.webp',
    'assets/images/expert-journey/step-4-amenagement.webp',
    'assets/images/expert-journey/step-5-installation.webp',
  ];

  /**
   * @var list<string>
   */
  private const STEP_IMAGE_ALTS = [
    'BNP PRE real estate project',
    'Property visit BNP Paribas Real Estate',
    'Contracting BNP Paribas Real Estate',
    'Fitting out business premises',
    'Ongoing support BNP Paribas Real Estate',
  ];

  /**
   * @var list<string>
   */
  private const STEP_IMAGE_CREDITS = [
    'nenetus - Adobe Stock',
    'tsyhun - Shutterstock',
    'Andrey_Popov - Shutterstock',
    'Who is Danny - Adobe Stock',
    'marvent - Shutterstock',
  ];

  public function __construct(
    private readonly ExtensionPathResolver $extensionPathResolver,
  ) {}

  public function imageUrl(int $index): string {
    $relative = self::STEP_IMAGE_PATHS[$index] ?? self::STEP_IMAGE_PATHS[0];
    $themePath = $this->extensionPathResolver->getPath('theme', 'ps_theme');

    return Url::fromUri('base:' . $themePath . '/' . $relative)->toString();
  }

  public function imageAlt(int $index): string {
    return self::STEP_IMAGE_ALTS[$index] ?? self::STEP_IMAGE_ALTS[0];
  }

  public function imageCredit(int $index): string {
    return self::STEP_IMAGE_CREDITS[$index] ?? self::STEP_IMAGE_CREDITS[0];
  }

}
