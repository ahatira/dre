<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\TranslatableInterface;

interface SurfaceDivisionInterface extends ContentEntityInterface, RevisionableInterface, RevisionLogInterface, TranslatableInterface {


  /**
   * Returns the business reference for the division.
   */
  public function getDivisionReference(): string;

}
