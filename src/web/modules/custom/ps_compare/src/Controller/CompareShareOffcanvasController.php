<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_compare\Service\CompareManagerInterface;
use Drupal\ps_compare\Service\ComparePathResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Loads the compare share webform inside a Bootstrap offcanvas.
 */
final class CompareShareOffcanvasController extends ControllerBase {

  public function __construct(
    private readonly CompareManagerInterface $compareManager,
    private readonly ComparePathResolver $comparePathResolver,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps_compare.manager'),
      $container->get('ps_compare.path_resolver'),
    );
  }

  /**
   * Builds offcanvas content with the compare share webform.
   */
  public function offcanvas(Request $request): array {
    if (!$this->compareManager->canOpenComparisonPage()) {
      throw new AccessDeniedHttpException();
    }

    if (!$this->moduleHandler()->moduleExists('webform')) {
      return [
        '#markup' => $this->t('Install the Webform module to share a comparison by email.'),
      ];
    }

    $webform = $this->entityTypeManager()->getStorage('webform')->load('compare_share');
    if ($webform === NULL) {
      return [
        '#markup' => $this->t('The comparison share form is not available yet.'),
      ];
    }

    return [
      '#theme' => 'ps_compare_share_offcanvas',
      '#webform' => $this->entityTypeManager()->getViewBuilder('webform')->view($webform, 'default'),
      '#attached' => [
        'library' => ['ps_compare/compare-share-offcanvas', 'ps_theme/form'],
      ],
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['user', 'url.query_args'],
      ],
    ];
  }

  /**
   * Offcanvas title callback.
   */
  public function title(): TranslatableMarkup {
    return $this->t('Receive my comparison');
  }

}
