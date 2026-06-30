<?php

declare(strict_types=1);

namespace Drupal\ps_email\ValueObject;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Describes one Property Search transactional email type.
 *
 * Used by the email admin hub registry.
 */
final readonly class EmailTransactionDefinition {

  /**
   * @param string $id
   *   Stable machine id (e.g. contact_confirmation).
   * @param string $module
   *   Owning Drupal module.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $label
   *   Admin label.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $description
   *   Short admin description.
   * @param string $mailerPolicyId
   *   Symfony Mailer policy id (mailer_policy.mailer_policy.*).
   * @param string|null $configRoute
   *   Route name for related settings, if any.
   * @param string|null $mjmlPreviewTemplate
   *   ps_theme_email MJML devel template id (without extension).
   * @param string|null $e2eScript
   *   Relative path from src/ to the Mailpit E2E shell script.
   * @param int $weight
   *   Sort weight on the overview page.
   */
  public function __construct(
    public string $id,
    public string $module,
    public TranslatableMarkup $label,
    public TranslatableMarkup $description,
    public string $mailerPolicyId,
    public ?string $configRoute = NULL,
    public ?string $mjmlPreviewTemplate = NULL,
    public ?string $e2eScript = NULL,
    public int $weight = 0,
  ) {}

}
