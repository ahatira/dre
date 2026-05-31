<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\ps_offer\Entity\OfferReferencePatternInterface;

final class OfferReferenceGenerator implements OfferReferenceGeneratorInterface {

  public function __construct(
    private readonly TimeInterface $time,
    private readonly OfferReferenceAliasResolverInterface $aliasResolver,
  ) {}

  public function generate(OfferReferencePatternInterface $pattern, array $context, int $sequence = 1, ?int $timestamp = NULL): string {
    $parts = [];
    $timestamp ??= $this->time->getCurrentTime();
    $context['_pattern'] = $pattern;

    foreach ($pattern->getSegments() as $segment) {
      $parts[] = $this->normalizeSegmentValue(
        $this->resolveSegmentValue($segment, $context, $sequence, $timestamp),
        (int) ($segment['length'] ?? 0),
        (string) ($segment['label'] ?? $segment['type'] ?? 'segment'),
      );
    }

    return implode('', $parts);
  }

  private function resolveSegmentValue(array $segment, array $context, int $sequence, int $timestamp): string {
    return match ((string) ($segment['type'] ?? '')) {
      'literal' => (string) ($segment['fallback_value'] ?? ''),
      'field_map' => $this->resolveFieldMapValue($segment, $context),
      'year_2_digits' => date('y', $timestamp),
      'counter' => (string) $sequence,
      default => throw new \InvalidArgumentException(sprintf('Unsupported offer reference segment type "%s".', (string) ($segment['type'] ?? ''))),
    };
  }

  private function resolveFieldMapValue(array $segment, array $context): string {
    $source_field = (string) ($segment['source_field'] ?? '');
    $source_value = trim((string) ($context[$source_field] ?? ''));
    $source_candidates = $this->buildSourceCandidates($source_value);
    $mapping = is_array($segment['mapping'] ?? NULL) ? $segment['mapping'] : [];
    $resolution_mode = (string) ($segment['resolution_mode'] ?? 'manual_then_alias');

    if ($source_value === '') {
      return (string) ($segment['fallback_value'] ?? '');
    }

    $pattern = $context['_pattern'];
    if (!$pattern instanceof OfferReferencePatternInterface) {
      throw new \InvalidArgumentException('Offer reference pattern context is missing.');
    }

    $manual_value = NULL;
    foreach ($source_candidates as $candidate) {
      if (array_key_exists($candidate, $mapping)) {
        $manual_value = (string) $mapping[$candidate];
        break;
      }
    }
    $alias_value = NULL;
    $fallback_value = (string) ($segment['fallback_value'] ?? '');

    if (in_array($resolution_mode, ['manual_then_alias', 'alias_then_manual', 'alias_only'], TRUE)) {
      foreach ($source_candidates as $candidate) {
        $alias_value = $this->aliasResolver->resolve($pattern, $segment, $source_field, $candidate);
        if ($alias_value !== NULL) {
          break;
        }
      }
    }

    return match ($resolution_mode) {
      'canonical' => $fallback_value !== '' ? $fallback_value : $source_value,
      'alias_only' => $alias_value ?? $fallback_value,
      'alias_then_manual' => $alias_value ?? $manual_value ?? ($fallback_value !== '' ? $fallback_value : $source_value),
      default => $manual_value ?? $alias_value ?? ($fallback_value !== '' ? $fallback_value : $source_value),
    };
  }

  /**
   * @return string[]
   */
  private function buildSourceCandidates(string $sourceValue): array {
    if ($sourceValue === '') {
      return [];
    }

    $candidates = [$sourceValue];
    $upper = mb_strtoupper($sourceValue);
    if ($upper !== $sourceValue) {
      $candidates[] = $upper;
    }

    if (preg_match('/\(([A-Za-z0-9_\-]+)\)\s*$/', $sourceValue, $matches) === 1) {
      $token = trim($matches[1]);
      if ($token !== '') {
        $candidates[] = $token;
        $token_upper = mb_strtoupper($token);
        if ($token_upper !== $token) {
          $candidates[] = $token_upper;
        }
      }
    }

    return array_values(array_unique($candidates));
  }

  private function normalizeSegmentValue(string $value, int $length, string $label): string {
    if ($length <= 0) {
      throw new \InvalidArgumentException(sprintf('Segment "%s" must declare a positive length.', $label));
    }

    if (mb_strlen($value) > $length) {
      throw new \InvalidArgumentException(sprintf('Segment "%s" exceeds its configured length of %d.', $label, $length));
    }

    if (ctype_digit($value)) {
      return str_pad($value, $length, '0', STR_PAD_LEFT);
    }

    return str_pad($value, $length, ' ', STR_PAD_RIGHT);
  }

}