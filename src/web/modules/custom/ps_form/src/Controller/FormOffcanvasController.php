<?php

declare(strict_types=1);

namespace Drupal\ps_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_form\Service\ContactDisplayModeManager;
use Drupal\ps_form\Service\ContactNeedRouter;
use Drupal\ps_form\Service\ContactWebformShellBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Loads contact-family webforms in page, modal, or offcanvas mode.
 */
final class FormOffcanvasController extends ControllerBase {

  public function __construct(
    private readonly ContactWebformShellBuilder $shellBuilder,
    private readonly ContactNeedRouter $contactNeedRouter,
    private readonly ContactDisplayModeManager $displayModeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_form.contact_webform_shell_builder'),
      $container->get('ps_form.contact_need_router'),
      $container->get('ps_form.contact_display_mode'),
    );
  }

  /**
   * Builds contact-family webform content for the configured display mode.
   */
  public function offcanvas(string $webform, Request $request): array {
    if (!$this->moduleHandler()->moduleExists('webform')) {
      return [
        '#markup' => $this->t('Install the Webform module to display this form.'),
      ];
    }

    $mode = $this->displayModeManager->getMode();
    $isAjax = $request->isXmlHttpRequest();

    if ($isAjax && $mode !== ContactDisplayModeManager::MODE_PAGE) {
      return $this->shellBuilder->build($webform, $mode);
    }

    return $this->shellBuilder->build($webform, ContactDisplayModeManager::MODE_PAGE);
  }

  /**
   * Page title callback.
   */
  public function title(string $webform): TranslatableMarkup|string {
    return $this->contactNeedRouter->getPageTitle($webform);
  }

}
