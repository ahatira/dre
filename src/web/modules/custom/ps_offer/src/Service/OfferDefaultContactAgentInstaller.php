<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_agent\Entity\AgentInterface;

/**
 * Creates and wires the default site contact agent on ps_offer install.
 */
final class OfferDefaultContactAgentInstaller {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Ensures a default ps_agent exists and is referenced in ps_offer.settings.
   *
   * @return int
   *   The default agent entity ID, or 0 when creation failed.
   */
  public function ensureDefaultAgent(): int {
    $settings = $this->configFactory->getEditable('ps_offer.settings');
    $existing_id = (int) ($settings->get('default_contact_agent') ?? 0);
    if ($existing_id > 0) {
      $existing = $this->entityTypeManager->getStorage('ps_agent')->load($existing_id);
      if ($existing instanceof AgentInterface) {
        return $existing_id;
      }
    }

    $site = $this->configFactory->get('system.site');
    $site_name = trim((string) ($site->get('name') ?? ''));
    $site_mail = trim((string) ($site->get('mail') ?? ''));
    if ($site_mail === '') {
      $site_mail = 'contact@example.com';
    }

    $agent = $this->entityTypeManager->getStorage('ps_agent')->create([
      'type' => 'default',
      'first_name' => $site_name !== '' ? $site_name : 'Agency',
      'last_name' => (string) $this->t('Contact'),
      'email' => $site_mail,
      'status' => TRUE,
      'internal_external' => 'INTERNAL',
    ]);
    $agent->save();

    $agent_id = (int) $agent->id();
    $settings->set('default_contact_agent', $agent_id)->save(TRUE);

    return $agent_id;
  }

}
