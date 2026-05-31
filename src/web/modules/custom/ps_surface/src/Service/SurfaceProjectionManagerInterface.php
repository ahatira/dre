<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Service;

interface SurfaceProjectionManagerInterface {

  /**
   * Recomputes surface projection metrics for an offer.
   */
  public function rebuildForOffer(int $offer_id): void;

}
