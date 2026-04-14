<?php

namespace Drupal\ps_media\Plugin\media\Source;

use Drupal\entity_browser_generic_embed\FileInputExtensionMatchTrait;
use Drupal\entity_browser_generic_embed\InputMatchInterface;
use Drupal\media\Plugin\media\Source\File as DrupalCoreMediaFile;

/**
 * Input-matching version of the PS Media File media source.
 */
class PsMediaFile extends DrupalCoreMediaFile implements InputMatchInterface {

  use FileInputExtensionMatchTrait;

}
