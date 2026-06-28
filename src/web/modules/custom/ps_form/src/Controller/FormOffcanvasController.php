<?php

declare(strict_types=1);

namespace Drupal\ps_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_form\Service\ContactNeedRouter;
use Drupal\ps_form\Service\WebformOffcanvasBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Loads contact-family webforms in a right offcanvas panel.
 */
final class FormOffcanvasController extends ControllerBase {

  public function __construct(
    private readonly WebformOffcanvasBuilder $offcanvasBuilder,
    private readonly ContactNeedRouter $contactNeedRouter,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_form.webform_offcanvas_builder'),
      $container->get('ps_form.contact_need_router'),
    );
  }

  /**
   * Builds offcanvas content for a contact-family webform.
   */
  public function offcanvas(string $webform): array {
    if (!$this->moduleHandler()->moduleExists('webform')) {
      return [
        '#markup' => $this->t('Install the Webform module to display this form.'),
      ];
    }

    return $this->offcanvasBuilder->build($webform);
  }

  /**
   * Page title callback.
   */
  public function title(string $webform): TranslatableMarkup|string {
    return $this->contactNeedRouter->getPageTitle($webform);
  }

}
