<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Entity;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_diagnostic\Form\PsDiagnosticDeleteForm;
use Drupal\ps_diagnostic\Form\PsDiagnosticForm;
use Drupal\ps_diagnostic\PsDiagnosticListBuilder;

/**
 * Defines the diagnostic config entity.
 *
 * Stores configuration for regulatory diagnostics (DPE, GES) including
 * energy classes with colors, ranges, and display settings.
 */
#[ConfigEntityType(
  id: 'diagnostic',
  label: new TranslatableMarkup('Diagnostic'),
  label_collection: new TranslatableMarkup('Diagnostics'),
  label_singular: new TranslatableMarkup('diagnostic'),
  label_plural: new TranslatableMarkup('diagnostics'),
  label_count: [
    'singular' => '@count diagnostic',
    'plural' => '@count diagnostics',
  ],
  handlers: [
    'list_builder' => PsDiagnosticListBuilder::class,
    'form' => [
      'add' => PsDiagnosticForm::class,
      'edit' => PsDiagnosticForm::class,
      'delete' => PsDiagnosticDeleteForm::class,
    ],
  ],
  config_prefix: 'diagnostic',
  admin_permission: 'administer ps_diagnostic',
  entity_keys: [
    'id' => 'id',
    'label' => 'label',
  ],
  config_export: [
    'id',
    'label',
    'unit',
    'icon',
    'classes',
  ],
  links: [
    'collection' => '/admin/ps/structure/diagnostic',
    'add-form' => '/admin/ps/structure/diagnostic/add',
    'edit-form' => '/admin/ps/structure/diagnostic/{diagnostic}/edit',
    'delete-form' => '/admin/ps/structure/diagnostic/{diagnostic}/delete',
  ],
)]
class PsDiagnostic extends ConfigEntityBase implements PsDiagnosticInterface {

  /**
   * The diagnostic ID.
   */
  protected string $id;

  /**
   * The diagnostic label.
   *
   * @var \Drupal\Core\StringTranslation\TranslatableMarkup|string
   */
  protected TranslatableMarkup|string $label = '';

  /**
   * The unit of measurement (translatable).
   *
   * @var \Drupal\Core\StringTranslation\TranslatableMarkup|string
   */
  protected TranslatableMarkup|string $unit = '';

  /**
   * Optional icon identifier (emoji, CSS class, or image path).
   *
   * @var string
   */
  protected string $icon = '';

  /**
   * Energy classes configuration.
   *
   * @var array<string, array{label: string, color: string, range_max: int|null}>
   */
  protected array $classes = [];

  /**
   * {@inheritdoc}
   */
  public function label(): TranslatableMarkup|string {
    // Return translatable markup for UI display.
    if ($this->label instanceof TranslatableMarkup) {
      return $this->label;
    }
    if ($this->label === '') {
      return new TranslatableMarkup('Unnamed diagnostic');
    }
    return new TranslatableMarkup('@label', ['@label' => $this->label]);
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel(): string {
    return (string) $this->label();
  }

  /**
   * {@inheritdoc}
   */
  public function getUnit(): string {
    if ($this->unit instanceof TranslatableMarkup) {
      return (string) $this->unit;
    }
    return (string) new TranslatableMarkup('@unit', ['@unit' => $this->unit]);
  }

  /**
   * {@inheritdoc}
   */
  public function setUnit(string $unit): static {
    $this->unit = $unit;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIcon(): string {
    return $this->icon ?? '';
  }

  /**
   * {@inheritdoc}
   */
  public function setIcon(string $icon): static {
    $this->icon = $icon;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getClasses(): array {
    return $this->classes;
  }

  /**
   * {@inheritdoc}
   */
  public function setClasses(array $classes): static {
    $this->classes = $classes;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);

    // Validate: only last class can have empty range_max.
    // This protects against programmatic saves (imports, drush, etc.).
    // Form validation handles UI submissions.
    if (!empty($this->classes)) {
      $empty_range_positions = [];
      $position = 0;

      foreach ($this->classes as $code => $config) {
        $position++;
        if (!isset($config['range_max']) || $config['range_max'] === NULL || $config['range_max'] === '') {
          $empty_range_positions[] = $position;
        }
      }

      $total_classes = count($this->classes);

      if (count($empty_range_positions) > 1) {
        throw new EntityStorageException('Only the last class can have an empty range max value.');
      }
      elseif (count($empty_range_positions) === 1 && $empty_range_positions[0] !== $total_classes) {
        throw new EntityStorageException(sprintf('Only the last class can have an empty range max value. Currently row %d has empty range but is not last.', $empty_range_positions[0]));
      }
      elseif (count($empty_range_positions) === 0 && $total_classes > 0) {
        throw new EntityStorageException('The last class must have an empty range max value.');
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getClass(string $classCode): ?array {
    return $this->classes[$classCode] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateClass(float $value): ?string {
    if ($value < 0) {
      return NULL;
    }

    $sortedClasses = $this->classes;
    ksort($sortedClasses);

    foreach ($sortedClasses as $code => $config) {
      if ($config['range_max'] === NULL) {
        return strtoupper($code);
      }
      if ($value <= $config['range_max']) {
        return strtoupper($code);
      }
    }

    // Return last class if value exceeds all ranges.
    return strtoupper((string) array_key_last($sortedClasses));
  }

}
