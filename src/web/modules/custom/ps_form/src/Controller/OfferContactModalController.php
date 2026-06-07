<?php

declare(strict_types=1);

namespace Drupal\ps_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\webform\WebformInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\webform\WebformMessageManagerInterface;

/**
 * Loads the offer contact webform for AJAX modal display.
 */
final class OfferContactModalController extends ControllerBase {

  private const WEBFORM_ID = 'offer_contact';

  public function __construct(
    private readonly WebformMessageManagerInterface $messageManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('webform.message_manager'),
    );
  }

  /**
   * Builds the contact webform for modal rendering.
   */
  public function modal(NodeInterface $node): array {
    if ($node->bundle() !== 'offer') {
      throw new NotFoundHttpException();
    }

    if (!$this->moduleHandler()->moduleExists('webform')) {
      return [
        '#markup' => $this->t('Contact form is not available.'),
      ];
    }

    $webform = $this->entityTypeManager()->getStorage('webform')->load(self::WEBFORM_ID);
    if (!$webform instanceof WebformInterface) {
      return [
        '#markup' => $this->t('Contact form is not available.'),
      ];
    }

    $this->messageManager->setSourceEntity($node);

    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-contact-modal__content']],
      'form' => $webform->getSubmissionForm([
        'entity_type' => 'node',
        'entity_id' => $node->id(),
      ]),
      '#attached' => [
        'library' => [
          'ps_theme/form',
        ],
      ],
      '#cache' => [
        'tags' => array_merge($node->getCacheTags(), $webform->getCacheTags()),
        'contexts' => ['route', 'url.query_args:source_entity_type', 'url.query_args:source_entity_id'],
      ],
    ];

    return $build;
  }

  /**
   * Page title callback.
   */
  public function title(NodeInterface $node): string {
    if (!$this->moduleHandler()->moduleExists('webform')) {
      return (string) $this->t('Contact the consultancy');
    }

    $webform = $this->entityTypeManager()->getStorage('webform')->load(self::WEBFORM_ID);
    if ($webform instanceof WebformInterface) {
      return $webform->label();
    }

    return (string) $this->t('Contact the consultancy');
  }

}
