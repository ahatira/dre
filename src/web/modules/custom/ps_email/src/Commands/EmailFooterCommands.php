<?php

declare(strict_types=1);

namespace Drupal\ps_email\Commands;

use Drupal\ps_email\Service\EmailFooterConfigSync;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for ps_email footer CMI sync.
 */
final class EmailFooterCommands extends DrushCommands {

  public function __construct(
    private readonly EmailFooterConfigSync $footerConfigSync,
  ) {
    parent::__construct();
  }

  /**
   * Imports ps_email.footer from config/env/sites/{country}/ CMI.
   */
  #[CLI\Command(name: 'ps:email:sync-footer', aliases: ['ps-esf'])]
  #[CLI\Option(name: 'country', description: 'Country code (default: active site).')]
  #[CLI\Usage(name: 'drush @ps.com ps:email:sync-footer', description: 'Apply COM footer CMI to active database.')]
  public function syncFooter(array $options = ['country' => NULL]): void {
    $country = $options['country'] !== NULL && $options['country'] !== ''
      ? strtolower((string) $options['country'])
      : NULL;

    $this->footerConfigSync->syncFromCmi($country);
    $resolved = $country ?? 'active site country';
    $this->io()->success(sprintf('Imported ps_email.footer from CMI (%s).', $resolved));
  }

}
