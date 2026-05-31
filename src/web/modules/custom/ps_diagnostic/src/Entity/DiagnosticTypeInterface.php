<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface DiagnosticTypeInterface extends ConfigEntityInterface {

  public function isEnabled(): bool;

  public function getUnit(): string;

  public function getIcon(): string;

  /**
   * @return array<int, array{label:string,color:string,range_max:int}>
   */
  public function getClasses(): array;

}
