<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface OfferReferenceAliasSetInterface extends ConfigEntityInterface {

  public function getWeight(): int;

  public function getAppliesToPatternIds(): array;

  public function getEntries(): array;

}