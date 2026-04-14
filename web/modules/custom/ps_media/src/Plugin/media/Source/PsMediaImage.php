<?php

namespace Drupal\ps_media\Plugin\media\Source;

use Drupal\entity_browser_generic_embed\FileInputExtensionMatchTrait;
use Drupal\entity_browser_generic_embed\InputMatchInterface;
use Drupal\media\Plugin\media\Source\Image as DrupalCoreMediaImage;

/**
 * Input-matching version of the PS Media Image media source.
 */
class PsMediaImage extends DrupalCoreMediaImage implements InputMatchInterface {

  use FileInputExtensionMatchTrait;

}
