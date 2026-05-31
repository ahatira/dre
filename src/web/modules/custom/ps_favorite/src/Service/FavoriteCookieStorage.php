<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Service;

use Drupal\Component\Serialization\Json;
use Drupal\Core\PrivateKey;
use Drupal\Core\Site\Settings;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;

final class FavoriteCookieStorage {

  public const COOKIE_NAME = 'ps_favorites';

  public function __construct(
    private readonly RequestStack $requestStack,
    private readonly PrivateKey $privateKey,
    private readonly Settings $settings,
  ) {}

  /**
   * @return int[]
   *   Favorite entity IDs from the signed cookie.
   */
  public function getEntityIds(string $entityTypeId): array {
    return $this->getAllItems()[$entityTypeId] ?? [];
  }

  /**
   * @return array<string, int[]>
   *   All cookie items keyed by entity type.
   */
  public function getAllItems(): array {
    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return [];
    }

    $raw = (string) $request->cookies->get(self::COOKIE_NAME, '');
    if ($raw === '') {
      return [];
    }

    $decoded = Json::decode(base64_decode($raw, TRUE) ?: '');
    if (!is_array($decoded) || !$this->isValidPayload($decoded)) {
      return [];
    }

    if (!$this->verifySignature($decoded)) {
      return [];
    }

    return $this->normalizeItems($decoded['items']);
  }

  /**
   * @param array<string, int[]> $items
   *   Items to store in the signed cookie.
   */
  public function buildCookieValue(array $items): string {
    $items = $this->normalizeItems($items);
    $payload = [
      'items' => $items,
      'sig' => '',
    ];
    $payload['sig'] = $this->sign($payload['items']);

    return base64_encode(Json::encode($payload));
  }

  /**
   * @param array<string, int[]> $items
   *   Items to store.
   */
  public function createCookie(array $items): Cookie {
    $request = $this->requestStack->getCurrentRequest();
    $secure = $request?->isSecure() ?? FALSE;

    return Cookie::create(
      self::COOKIE_NAME,
      $this->buildCookieValue($items),
      strtotime('+30 days'),
      '/',
      NULL,
      $secure,
      TRUE,
      FALSE,
      Cookie::SAMESITE_LAX,
    );
  }

  public function createExpiredCookie(): Cookie {
    $request = $this->requestStack->getCurrentRequest();
    $secure = $request?->isSecure() ?? FALSE;

    return Cookie::create(
      self::COOKIE_NAME,
      '',
      1,
      '/',
      NULL,
      $secure,
      TRUE,
      FALSE,
      Cookie::SAMESITE_LAX,
    );
  }

  private function isValidPayload(array $payload): bool {
    return isset($payload['items'], $payload['sig'])
      && is_array($payload['items'])
      && is_string($payload['sig']);
  }

  private function verifySignature(array $payload): bool {
    return hash_equals($this->sign($payload['items']), $payload['sig']);
  }

  /**
   * @param array<string, mixed> $items
   *   Raw cookie items.
   *
   * @return array<string, int[]>
   *   Normalized items.
   */
  private function normalizeItems(array $items): array {
    $normalized = [];
    foreach ($items as $entityTypeId => $entityIds) {
      if (!is_string($entityTypeId) || !is_array($entityIds)) {
        continue;
      }
      $normalized[$entityTypeId] = array_values(array_filter(array_unique(array_map('intval', $entityIds))));
    }

    return $normalized;
  }

  private function sign(array $items): string {
    $salt = (string) $this->settings->getHashSalt();
    return hash_hmac('sha256', Json::encode($items), $this->privateKey->get() . $salt);
  }

}
