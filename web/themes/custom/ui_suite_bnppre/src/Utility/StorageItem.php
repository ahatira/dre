<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Utility;

use Drupal\Core\KeyValueStore\MemoryStorage;

/**
 * Theme Storage Item.
 *
 * This is essentially the same object as Storage. The only exception is
 * delegating any data changes to the primary Storage object this
 * StorageItem object lives in.
 *
 * This storage object can be used in `foreach` loops.
 *
 * @see \Drupal\ui_suite_bnppre\Utility\Storage
 */
class StorageItem extends MemoryStorage implements \Iterator {

  /**
   * Flag determining whether the object has been initialized.
   *
   * @var bool
   */
  protected bool $initialized = FALSE;

  /**
   * The \Drupal\ui_suite_bnppre\Storage instance this item belongs to.
   *
   * @var \Drupal\ui_suite_bnppre\Utility\Storage
   */
  protected Storage $storage;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $data, Storage $storage) {
    $this->storage = $storage;
    $this->setMultiple($data);
    $this->initialized = TRUE;
  }

  /**
   * Notifies the main Storage object that data has changed.
   */
  public function changed(): void {
    if ($this->initialized) {
      $this->storage->changed();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function current(): mixed {
    return \current($this->data);
  }

  /**
   * {@inheritdoc}
   */
  public function delete($key): void {
    parent::delete($key);
    $this->changed();
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMultiple(array $keys): void {
    parent::deleteMultiple($keys);
    $this->changed();
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll(): void {
    parent::deleteAll();
    $this->changed();
  }

  /**
   * Determines if the cache is empty.
   *
   * @return bool
   *   TRUE or FALSE
   */
  public function isEmpty(): bool {
    return empty($this->data);
  }

  /**
   * {@inheritdoc}
   */
  public function key(): mixed {
    return \key($this->data);
  }

  /**
   * {@inheritdoc}
   */
  public function next(): void {
    \next($this->data);
  }

  /**
   * {@inheritdoc}
   */
  public function rename($key, $new_key): void {
    parent::rename($key, $new_key);
    $this->changed();
  }

  /**
   * {@inheritdoc}
   */
  public function rewind(): void {
    \reset($this->data);
  }

  /**
   * {@inheritdoc}
   */
  public function set($key, $value): void {
    parent::set($key, $value);
    $this->changed();
  }

  /**
   * {@inheritdoc}
   */
  public function setIfNotExists($key, $value): bool {
    if (!isset($this->data[$key])) {
      $this->data[$key] = $value;
      $this->changed();
      return TRUE;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function setMultiple(array $data): void {
    parent::setMultiple($data);
    $this->changed();
  }

  /**
   * {@inheritdoc}
   */
  public function valid(): bool {
    return \key($this->data) !== NULL;
  }

}
