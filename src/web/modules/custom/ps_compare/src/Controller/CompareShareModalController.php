<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\ps_compare\Form\CompareShareForm;
use Drupal\ps_compare\Service\CompareManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns the share-by-email form for the comparison share modal.
 */
final class CompareShareModalController extends ControllerBase {

  public function __construct(
    private readonly CompareManagerInterface $compareManager,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps_compare.manager'),
      $container->get('renderer'),
    );
  }

  /**
   * Builds the share form (GET display, POST/AJAX submit).
   */
  public function modal(): Response {
    if (!$this->compareManager->canOpenComparisonPage()) {
      return new Response('', Response::HTTP_FORBIDDEN);
    }

    $build = $this->formBuilder()->getForm(CompareShareForm::class);
    return new Response((string) $this->renderer->renderRoot($build));
  }

}
