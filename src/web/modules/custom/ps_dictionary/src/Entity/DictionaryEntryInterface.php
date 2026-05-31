<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface DictionaryEntryInterface extends ConfigEntityInterface {

  public function getType(): string;

  public function getCode(): string;

  public function getWeight(): int;

}
