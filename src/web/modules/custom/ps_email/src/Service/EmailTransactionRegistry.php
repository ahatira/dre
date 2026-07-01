<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_email\ValueObject\EmailTransactionDefinition;

/**
 * Registry of Property Search transactional email types.
 */
final class EmailTransactionRegistry {

  use StringTranslationTrait;

  public function __construct(
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {}

  /**
   * Returns registered email types for enabled modules, sorted by weight.
   *
   * @return list<\Drupal\ps_email\ValueObject\EmailTransactionDefinition>
   *   Email type definitions.
   */
  public function getDefinitions(): array {
    $definitions = [
      new EmailTransactionDefinition(
        id: 'contact_confirmation',
        module: 'ps_form',
        label: $this->t('Contact hub confirmation'),
        description: $this->t('Visitor confirmation after contact hub webform submission. Copy and subject in Webform → Emails / Handlers; hero image in Webform settings.'),
        mailerPolicyId: 'webform',
        mjmlPreviewTemplate: 'email-contact-preview',
        e2eScript: 'web/modules/custom/ps_form/tests/b2b_contact_email_handlers.sh',
        weight: 0,
      ),
      new EmailTransactionDefinition(
        id: 'compare_share',
        module: 'ps_compare',
        label: $this->t('Property comparison share'),
        description: $this->t('Email sent when a visitor shares a property comparison.'),
        mailerPolicyId: 'ps_compare',
        mjmlPreviewTemplate: 'email-compare-preview',
        e2eScript: 'web/modules/custom/ps_compare/tests/b2b_compare_email.sh',
        weight: 10,
      ),
      new EmailTransactionDefinition(
        id: 'search_alert_digest',
        module: 'ps_search',
        label: $this->t('Search alert digest'),
        description: $this->t('Periodic digest of new offers matching saved search alerts.'),
        mailerPolicyId: 'ps_search',
        configRoute: 'ps_search.alert_settings_form',
        mjmlPreviewTemplate: 'email-search-alert-preview',
        e2eScript: 'web/modules/custom/ps_search/tests/e2e_search_alert_digest.sh',
        weight: 20,
      ),
      new EmailTransactionDefinition(
        id: 'offer_email_cards',
        module: 'ps_email',
        label: $this->t('Offer email cards'),
        description: $this->t('Search-style offer card and primary CTA for offer contact confirmation emails.'),
        mailerPolicyId: 'webform',
        mjmlPreviewTemplate: 'email-offer-cards-preview',
        weight: 15,
      ),
      new EmailTransactionDefinition(
        id: 'import_pipeline_alert',
        module: 'ps_migrate',
        label: $this->t('CRM import pipeline alerts'),
        description: $this->t('Internal ops alerts on import failure or high skip rate.'),
        mailerPolicyId: 'ps_migrate',
        configRoute: 'ps_migrate.import_pipeline_settings',
        mjmlPreviewTemplate: 'email-import-alert-preview',
        e2eScript: 'web/modules/custom/ps_migrate/tests/e2e_import_alert.sh',
        weight: 30,
      ),
      new EmailTransactionDefinition(
        id: 'user_account',
        module: 'user',
        label: $this->t('User account emails'),
        description: $this->t('Password reset, registration and account status notifications (Drupal core).'),
        mailerPolicyId: 'user',
        configRoute: 'ps_email.user_account',
        weight: 40,
      ),
    ];

    $definitions = array_values(array_filter(
      $definitions,
      fn (EmailTransactionDefinition $definition): bool => $this->moduleHandler->moduleExists($definition->module),
    ));

    usort(
      $definitions,
      static fn (EmailTransactionDefinition $a, EmailTransactionDefinition $b): int => $a->weight <=> $b->weight,
    );

    return $definitions;
  }

}
