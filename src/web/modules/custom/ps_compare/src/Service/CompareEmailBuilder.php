<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\ps_compare\CompareRenderContext;

/**
 * Builds the comparison email using the same table as the compare page.
 */
final class CompareEmailBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly CompareManagerInterface $compareManager,
    private readonly ComparePageBuilder $pageBuilder,
    private readonly CompareShareResolver $shareResolver,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * Builds the email render array for the current compare list.
   *
   * @return array<string, mixed>
   */
  public function buildRenderArray(?string $introMessage = NULL): array {
    $offers = array_values(array_filter(
      $this->compareManager->getCompareList('node'),
      static fn (EntityInterface $entity): bool => $entity instanceof NodeInterface && $entity->bundle() === 'offer',
    ));

    $shareUrl = $this->shareResolver->buildUrlFromOffers($offers, TRUE);
    $tableBuild = $this->pageBuilder->buildPage(CompareRenderContext::EMAIL, $offers, [
      'share_url' => $shareUrl,
    ]);
    $tableHtml = (string) $this->renderer->renderPlain($tableBuild);

    return [
      '#theme' => 'ps_compare_email',
      '#title' => $this->t('Property comparison'),
      '#intro_message' => $introMessage !== NULL && trim($introMessage) !== '' ? trim($introMessage) : NULL,
      '#compare_url' => $shareUrl,
      '#table' => $tableHtml !== '' ? Markup::create($tableHtml) : NULL,
    ];
  }

}
