<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferWebformModalBuilder;
use Drupal\webform\WebformInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Loads offer-context webforms in an AJAX modal.
 */
final class OfferWebformController extends ControllerBase {

  public function __construct(
    private readonly OfferWebformModalBuilder $modalBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_offer.webform_modal_builder'),
    );
  }

  /**
   * Builds an offer webform for AJAX modal rendering.
   */
  public function modal(NodeInterface $node, WebformInterface|string $webform): array {
    if (!$this->moduleHandler()->moduleExists('webform')) {
      return [
        '#markup' => $this->t('Webform is not available.'),
      ];
    }

    $webformId = $webform instanceof WebformInterface ? $webform->id() : $webform;
    return $this->modalBuilder->build($node, $webformId);
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
