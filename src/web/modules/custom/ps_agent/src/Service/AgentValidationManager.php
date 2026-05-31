<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Service;

/**
 * Validates contact fields for Agent entities.
 */
final class AgentValidationManager {

  /**
   * Validates email and phone values.
   *
   * @return array<string, string>
   *   Validation errors keyed by form element name.
   */
  public function validateContactValues(string $email, string $phone): array {
    $errors = [];

    if (trim($email) === '') {
      $errors['email'] = 'Email is required.';
    }

    if (trim($phone) === '') {
      $errors['phone'] = 'Phone is required.';
      return $errors;
    }

    $normalized_phone = preg_replace('/[\s\-\(\)\.]/', '', $phone) ?? '';
    if (!preg_match('/^\+?[0-9]{7,20}$/', $normalized_phone)) {
      $errors['phone'] = 'Phone format is invalid. Use digits and optional leading +.';
    }

    return $errors;
  }

}
