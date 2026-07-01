<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\ps_form\Service\ContactDisplayModeManager;
use Drupal\ps_offer\Service\OfferWebformModalBuilder;
use Drupal\webform\WebformInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Loads offer-context webforms using the site contact display mode.
 */
final class OfferWebformController extends ControllerBase {

  public function __construct(
    private readonly OfferWebformModalBuilder $shellBuilder,
    private readonly ContactDisplayModeManager $displayModeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_offer.webform_modal_builder'),
      $container->get('ps_form.contact_display_mode'),
    );
  }

  /**
   * Builds an offer webform for page or AJAX dialog rendering.
   */
  public function modal(NodeInterface $node, WebformInterface|string $webform, Request $request): array {
    if (!$this->moduleHandler()->moduleExists('webform')) {
      return [
        '#markup' => $this->t('Webform is not available.'),
      ];
    }

    $webformId = $webform instanceof WebformInterface ? $webform->id() : $webform;
    $mode = $this->displayModeManager->getMode();
    if ($request->isXmlHttpRequest() && $mode !== ContactDisplayModeManager::MODE_PAGE) {
      return $this->shellBuilder->build($node, $webformId, $mode);
    }

    return $this->shellBuilder->build($node, $webformId, ContactDisplayModeManager::MODE_PAGE);
  }

  /**
   * Page title callback.
   */
  public function title(NodeInterface $node, WebformInterface|string $webform): string {
    if ($node->bundle() !== 'offer') {
      throw new NotFoundHttpException();
    }

    if ($webform instanceof WebformInterface) {
      return (string) $webform->label();
    }

    $entity = $this->entityTypeManager()->getStorage('webform')->load($webform);
    if ($entity instanceof WebformInterface) {
      return (string) $entity->label();
    }

    return (string) $this->t('Contact the consultancy');
  }

}
