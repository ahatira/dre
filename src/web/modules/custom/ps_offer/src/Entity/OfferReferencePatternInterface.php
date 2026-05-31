<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface OfferReferencePatternInterface extends ConfigEntityInterface {

  public function getWeight(): int;

  public function getTargetBundles(): array;

  public function allowsManualOverride(): bool;

  public function requiresUniqueness(): bool;

  public function validatesManualValueAgainstPattern(): bool;

  public function generatesOnCreate(): bool;

  public function regeneratesOnSourceChange(): bool;

  public function getCounterScopeMode(): string;

  public function getSegments(): array;

}