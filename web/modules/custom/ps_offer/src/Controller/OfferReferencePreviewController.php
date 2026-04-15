<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferReferenceBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns live reference previews for offer edit forms.
 */
final class OfferReferencePreviewController extends ControllerBase {

  /**
   * Constructs the preview controller.
   */
  public function __construct(
    protected OfferReferenceBuilder $referenceBuilder,
    protected EntityTypeManagerInterface $offerEntityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_offer.reference_builder'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Builds a live reference preview from current form values.
   */
  public function preview(Request $request): JsonResponse {
    $payload = $request->request->all();
    if ($payload === []) {
      try {
        $payload = $request->toArray();
      }
      catch (\JsonException) {
        $payload = [];
      }
    }

    $fields = is_array($payload['fields'] ?? NULL) ? $payload['fields'] : [];
    $nid = (int) ($payload['nid'] ?? 0);

    $node = $this->buildTemporaryOfferNode($nid);
    if (!$node instanceof NodeInterface) {
      return new JsonResponse(['reference' => ''], 200);
    }

    foreach ($fields as $field_name => $value) {
      if (!is_string($field_name) || $field_name === '') {
        continue;
      }

      if (!$node->hasField($field_name) || !is_scalar($value)) {
        continue;
      }

      $raw = trim((string) $value);
      if ($raw === '') {
        continue;
      }

      // Most offer source fields are scalar list/text/date values. Setting
      // scalar values directly keeps the preview lightweight and safe.
      $node->set($field_name, $raw);
    }

    $generated = $this->referenceBuilder->generate($node);
    return new JsonResponse([
      'reference' => (string) ($generated['reference'] ?? ''),
    ]);
  }

  /**
   * Creates a transient offer node for preview generation.
   */
  protected function buildTemporaryOfferNode(int $nid): ?NodeInterface {
    $storage = $this->offerEntityTypeManager->getStorage('node');

    if ($nid > 0) {
      $loaded = $storage->load($nid);
      if ($loaded instanceof NodeInterface && $loaded->bundle() === 'offer') {
        return clone $loaded;
      }
    }

    $created = $storage->create(['type' => 'offer']);
    return $created instanceof NodeInterface ? $created : NULL;
  }

}
