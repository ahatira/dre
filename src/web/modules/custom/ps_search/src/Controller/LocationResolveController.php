<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_search\Api\RequestValidator;
use Drupal\ps_search\Contract\LocationResolverInterface;
use Drupal\ps_search\Location\LocationResolver;
use Drupal\ps_search\Service\SearchContentLanguageResolver;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\LocationResolveResult;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resolves free-text location queries to GeoContext JSON (L3).
 */
final class LocationResolveController extends ControllerBase {

  public function __construct(
    private readonly LocationResolverInterface $locationResolver,
    private readonly SearchEngineSettingsReader $engineSettings,
    private readonly SearchContentLanguageResolver $contentLanguageResolver,
    private readonly RequestValidator $requestValidator,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.location_resolver'),
      $container->get('ps_search.engine_settings_reader'),
      $container->get('ps_search.content_language_resolver'),
      $container->get('ps_search.api.request_validator'),
    );
  }

  /**
   * Returns resolved GeoContext for a location query string.
   */
  public function resolve(Request $request): JsonResponse {
    if (!$this->engineSettings->isSearchContextEnabled()) {
      return new JsonResponse(['error' => 'search_context_disabled'], 403);
    }

    $query = $this->requestValidator->sanitizeText($request->query->get('q'));
    if ($query === NULL || mb_strlen($query) < 2) {
      return new JsonResponse(['error' => 'invalid_query'], 400);
    }

    $country = $this->requestValidator->sanitizeText($request->query->get('country'));
    if ($country === NULL && $this->locationResolver instanceof LocationResolver) {
      $country = $this->locationResolver->resolveCountryCode();
    }
    $country = strtolower($country ?? 'com');

    $langcode = $this->contentLanguageResolver->resolvePrimaryLangcode($request);
    $result = $this->locationResolver->resolveQuery($query, $country, $langcode);

    return new JsonResponse($this->buildPayload($result));
  }

  /**
   * @return array<string, mixed>
   */
  private function buildPayload(LocationResolveResult $result): array {
    return [
      'geo' => $result->geo instanceof GeoContext ? $this->geoToArray($result->geo) : NULL,
      'ambiguous' => $result->ambiguous,
      'candidates' => array_map(
        fn (GeoContext $geo): array => $this->geoToArray($geo),
        $result->candidates,
      ),
    ];
  }

  /**
   * @return array<string, mixed>
   */
  private function geoToArray(GeoContext $geo): array {
    return [
      'id' => $geo->id,
      'slug' => $geo->slug,
      'type' => $geo->type->value,
      'label' => $geo->label,
      'lat' => $geo->lat,
      'lng' => $geo->lng,
      'bbox' => $geo->bbox->toConfigArray(),
      'postalPrefixes' => $geo->postalPrefixes,
      'radiusM' => $geo->radiusM,
      'precision' => $geo->precision->value,
      'source' => $geo->source,
    ];
  }

}
