<?php

namespace Drupal\ps_media\Plugin\media\Source;

use Drupal\entity_browser_generic_embed\FileInputExtensionMatchTrait;
use Drupal\entity_browser_generic_embed\InputMatchInterface;
use Drupal\media\Plugin\media\Source\VideoFile as DrupalCoreMediaVideoFile;

/**
 * Input-matching version of the PS Media Video File media source.
 */
class PsMediaVideoFile extends DrupalCoreMediaVideoFile implements InputMatchInterface {

  use FileInputExtensionMatchTrait;

}
