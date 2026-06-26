<?php

declare(strict_types=1);

namespace Drupal\ps_core\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ps_core\Service\PermissionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class PsCoreController extends ControllerBase implements ContainerInjectionInterface {

  public function __construct(
    private readonly PermissionManager $permissionManager,
  ) {}

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps_core.permission_manager'),
    );
  }

  public function content(): array {
    return $this->buildSectionPage(
      (string) $this->t('Content hub'),
      (string) $this->t('Central access to Property Search content operations.'),
      [
        [
          'title' => 'Health checks',
          'route' => 'ps_core.health',
          'permission' => 'administer ps_core',
          'description' => 'Inspect basic runtime diagnostics.',
        ],
      ],
    );
  }

  public function structure(): array {
    return $this->buildSectionPage(
      (string) $this->t('Structure hub'),
      (string) $this->t('Manage structural entities and dictionaries.'),
      [
        [
          'title' => 'Dictionary types',
          'route' => 'ps_dictionary.type_collection',
          'permission' => 'manage ps_dictionary',
          'description' => 'Manage dictionary types and entries.',
        ],
      ],
    );
  }

  public function configuration(): array {
    return $this->buildSectionPage(
      (string) $this->t('Configuration hub'),
      (string) $this->t('Configure cross-cutting Property Search settings.'),
      [
        [
          'title' => 'Global settings',
          'route' => 'ps_core.settings_form',
          'permission' => 'administer ps_core',
          'description' => 'Site contact details and platform service defaults.',
        ],
        [
          'title' => 'Health checks',
          'route' => 'ps_core.health',
          'permission' => 'administer ps_core',
          'description' => 'Inspect basic runtime diagnostics.',
        ],
      ],
    );
  }

  public function health(): array {
    return [
      '#type' => 'markup',
      '#markup' => '<p>ps_core health: OK</p>',
    ];
  }

  /**
   * Builds a small admin section page with permission-aware links.
   *
   * @param array<int,array{title:string,route:string,permission:string,description:string}> $items
   *   Candidate links for the section.
   */
  private function buildSectionPage(string $title, string $description, array $items): array {
    $routeToPermission = [];
    foreach ($items as $item) {
      $routeToPermission[$item['route']] = $item['permission'];
    }

    $allowedRouteMap = array_flip($this->permissionManager->allowedRoutes($routeToPermission));
    $rows = [];
    foreach ($items as $item) {
      if (!isset($allowedRouteMap[$item['route']])) {
        continue;
      }
      $rows[] = [
        '#type' => 'container',
        'link' => Link::fromTextAndUrl($this->t($item['title']), Url::fromRoute($item['route']))->toRenderable(),
        'description' => [
          '#markup' => '<div>' . $this->t('@description', ['@description' => $item['description']]) . '</div>',
        ],
      ];
    }

    if ($rows === []) {
      $rows[] = [
        '#type' => 'container',
        '#markup' => '<p>' . $this->t('No actions are currently available for your permissions.') . '</p>',
      ];
    }

    return [
      '#type' => 'container',
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $title,
      ],
      'description' => [
        '#markup' => '<p>' . $description . '</p>',
      ],
      'items' => [
        '#theme' => 'item_list',
        '#items' => $rows,
      ],
    ];
  }

}
