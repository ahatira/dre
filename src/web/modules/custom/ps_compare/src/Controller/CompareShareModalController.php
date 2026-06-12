<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\ps_compare\Form\CompareShareForm;
use Drupal\ps_compare\Service\CompareManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
   * Builds the share form (GET fragment, POST/AJAX submit).
   *
   * GET responses render only the form markup for modal injection. AJAX submits
   * use ?ajax_form=1; FormBuilder throws FormAjaxException before this returns.
   */
  public function modal(): Response {
    if (!$this->compareManager->canOpenComparisonPage()) {
      throw new AccessDeniedHttpException();
    }

    $build = $this->formBuilder()->getForm(CompareShareForm::class);

    return new Response((string) $this->renderer->renderRoot($build));
  }

}
