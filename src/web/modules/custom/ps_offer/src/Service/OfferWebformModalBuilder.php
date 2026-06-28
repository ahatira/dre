<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\webform\WebformInterface;
use Drupal\webform\WebformMessageManagerInterface;

/**
 * Builds modal render arrays for offer-context webforms.
 */
final class OfferWebformModalBuilder {

  use StringTranslationTrait;

  public const OFFER_WEBFORMS = [
    'offer_contact',
    'schedule_visit',
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly WebformMessageManagerInterface $messageManager,
  ) {}

  /**
   * Builds an offer webform modal panel.
   *
   * @return array<string, mixed>
   *   Render array for AJAX modal loading.
   */
  public function build(NodeInterface $node, string $webformId): array {
    if ($node->bundle() !== 'offer') {
      return $this->unavailable($this->t('Offer not found.'));
    }

    if (!in_array($webformId, self::OFFER_WEBFORMS, TRUE)) {
      return $this->unavailable($this->t('Unknown offer form.'));
    }

    $webform = $this->loadWebform($webformId);
    if ($webform === NULL) {
      return $this->unavailable($this->t('The requested form is not available yet.'));
    }

    $this->messageManager->setSourceEntity($node);

    return [
      '#theme' => 'ps_offer_webform_modal',
      '#webform' => $webform->getSubmissionForm([
        'entity_type' => 'node',
        'entity_id' => $node->id(),
      ]),
      '#webform_id' => $webformId,
      '#panel_id' => $webformId . '-panel',
      '#attached' => [
        'library' => [
          'ps_theme/form',
        ],
      ],
      '#cache' => [
        'max-age' => 0,
        'tags' => array_merge($node->getCacheTags(), $webform->getCacheTags()),
        'contexts' => ['route', 'url.query_args', 'languages:language_interface', 'user'],
      ],
    ];
  }

  /**
   * Loads a webform entity by machine name.
   */
  private function loadWebform(string $webformId): ?WebformInterface {
    $entity = $this->entityTypeManager->getStorage('webform')->load($webformId);
    return $entity instanceof WebformInterface ? $entity : NULL;
  }

  /**
   * Builds a placeholder when the webform cannot be loaded.
   *
   * @return array<string, mixed>
   *   Placeholder render array.
   */
  private function unavailable(string|TranslatableMarkup $message): array {
    return [
      '#theme' => 'ps_homepage_contact_offcanvas_placeholder',
      '#message' => $message,
    ];
  }

}
