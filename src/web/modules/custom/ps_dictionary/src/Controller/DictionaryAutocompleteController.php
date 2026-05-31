<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class DictionaryAutocompleteController extends ControllerBase {

  public function handle(string $dictionary_type, Request $request): JsonResponse {
    $resolver = \Drupal::service('ps_dictionary.resolver');
    $query = $request->query->get('q', '');
    $matches = [];
    foreach ($resolver->all($dictionary_type) as $entry) {
      if ($query === '' || stripos($entry['label'], $query) !== FALSE || stripos($entry['code'], $query) !== FALSE) {
        $matches[] = [
          'value' => $entry['code'],
          'label' => $entry['label'] . ' (' . $entry['code'] . ')',
        ];
      }
    }
    return new JsonResponse($matches);
  }
}
