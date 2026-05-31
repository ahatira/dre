<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\ValueObject;

/**
 * Immutable representation of a technical element extracted from CRM XML.
 */
final class FeatureTechnicalElement {

  /**
   * Constructs a FeatureTechnicalElement object.
   */
  public function __construct(
    private readonly string $groupCode,
    private readonly string $featureCode,
    private readonly string $label,
    private readonly ?string $value,
    private readonly ?string $unit,
    private readonly ?string $complement,
    private readonly int $sourceIndex,
    private readonly array $errors = [],
    private readonly array $warnings = [],
  ) {}

  /**
   * Gets the source group code.
   */
  public function getGroupCode(): string {
    return $this->groupCode;
  }

  /**
   * Gets the source feature code.
   */
  public function getFeatureCode(): string {
    return $this->featureCode;
  }

  /**
   * Gets the source label.
   */
  public function getLabel(): string {
    return $this->label;
  }

  /**
   * Gets the source value.
   */
  public function getValue(): ?string {
    return $this->value;
  }

  /**
   * Gets the source unit.
   */
  public function getUnit(): ?string {
    return $this->unit;
  }

  /**
   * Gets the source complement.
   */
  public function getComplement(): ?string {
    return $this->complement;
  }

  /**
   * Gets the zero-based source index.
   */
  public function getSourceIndex(): int {
    return $this->sourceIndex;
  }

  /**
   * Gets validation errors.
   */
  public function getErrors(): array {
    return $this->errors;
  }

  /**
   * Gets validation warnings.
   */
  public function getWarnings(): array {
    return $this->warnings;
  }

  /**
   * Returns a copy with validation messages.
   */
  public function withMessages(array $errors = [], array $warnings = []): self {
    return new self(
      $this->groupCode,
      $this->featureCode,
      $this->label,
      $this->value,
      $this->unit,
      $this->complement,
      $this->sourceIndex,
      $errors,
      $warnings,
    );
  }

  /**
   * Checks whether the element is valid.
   */
  public function isValid(): bool {
    return $this->errors === [];
  }

  /**
   * Returns the normalized import record.
   */
  public function toRecord(): array {
    $payload = [];

    if ($this->value !== NULL) {
      $payload['value'] = $this->value;
    }

    if ($this->unit !== NULL) {
      $payload['unit'] = $this->unit;
    }

    if ($this->complement !== NULL) {
      $payload['complement'] = $this->complement;
    }

    return [
      'group_code' => $this->groupCode,
      'feature_code' => $this->featureCode,
      'label' => $this->label,
      'payload' => $payload,
      'source_index' => $this->sourceIndex,
      'errors' => $this->errors,
      'warnings' => $this->warnings,
      'valid' => $this->isValid(),
    ];
  }

}