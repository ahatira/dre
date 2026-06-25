<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_agent\Entity\AgentInterface;

/**
 * Resolves the consultant and notification email for an offer.
 *
 * Chain: primary agent → first secondary agent → default site agent (config).
 * Email fallback after agents: default agent email → system.site:mail.
 */
class OfferContactResolver {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Resolves the ps_agent entity to display for an offer.
   */
  public function resolveAgent(NodeInterface $offer): ?AgentInterface {
    if ($offer->bundle() !== 'offer') {
      return NULL;
    }

    $primary = $this->loadPublishedAgentFromReference($offer, 'field_primary_agent');
    if ($primary !== NULL) {
      return $primary;
    }

    $secondary = $this->loadFirstPublishedSecondaryAgent($offer);
    if ($secondary !== NULL) {
      return $secondary;
    }

    return $this->loadDefaultConfiguredAgent();
  }

  /**
   * Resolves the notification email for offer webforms.
   */
  public function resolveContactEmail(NodeInterface $offer): string {
    $agent = $this->resolveAgent($offer);
    if ($agent !== NULL) {
      $email = trim($agent->getEmail());
      if ($email !== '') {
        return $email;
      }
    }

    return trim((string) ($this->configFactory->get('system.site')->get('mail') ?? ''));
  }

  /**
   * Checks whether an offer resolves to a consultant entity.
   */
  public function hasResolvedAgent(NodeInterface $offer): bool {
    return $this->resolveAgent($offer) !== NULL;
  }

  /**
   * Loads the configured default contact agent entity.
   */
  public function loadDefaultConfiguredAgent(): ?AgentInterface {
    $agent_id = (int) ($this->configFactory->get('ps_offer.settings')->get('default_contact_agent') ?? 0);
    if ($agent_id <= 0) {
      return NULL;
    }

    $agent = $this->entityTypeManager->getStorage('ps_agent')->load($agent_id);
    if (!$agent instanceof AgentInterface || !$this->isPublishedAgent($agent)) {
      return NULL;
    }

    return $agent;
  }

  /**
   * Loads the first published agent from a single-value reference field.
   */
  private function loadPublishedAgentFromReference(NodeInterface $offer, string $field_name): ?AgentInterface {
    if (!$offer->hasField($field_name)) {
      return NULL;
    }

    $field = $offer->get($field_name);
    if (!$field instanceof EntityReferenceFieldItemListInterface || $field->isEmpty()) {
      return NULL;
    }

    foreach ($field->referencedEntities() as $entity) {
      if ($entity instanceof AgentInterface && $this->isPublishedAgent($entity)) {
        return $entity;
      }
    }

    return NULL;
  }

  /**
   * Loads the first published secondary agent on the offer.
   */
  private function loadFirstPublishedSecondaryAgent(NodeInterface $offer): ?AgentInterface {
    if (!$offer->hasField('field_secondary_agents')) {
      return NULL;
    }

    $field = $offer->get('field_secondary_agents');
    if (!$field instanceof EntityReferenceFieldItemListInterface || $field->isEmpty()) {
      return NULL;
    }

    foreach ($field->referencedEntities() as $entity) {
      if ($entity instanceof AgentInterface && $this->isPublishedAgent($entity)) {
        return $entity;
      }
    }

    return NULL;
  }

  /**
   * Checks whether an agent entity is published.
   */
  private function isPublishedAgent(AgentInterface $agent): bool {
    if (!$agent->hasField('status') || $agent->get('status')->isEmpty()) {
      return TRUE;
    }

    $item = $agent->get('status')->first();
    return (bool) ($item->value ?? TRUE);
  }

}
