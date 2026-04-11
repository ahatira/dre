<?php

declare(strict_types=1);

namespace Drupal\ps\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps\Service\SettingsManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Admin controller for PropertySearch.
 */
final class AdminController extends ControllerBase {

  /**
   * Constructor.
   */
  public function __construct(
    protected SettingsManagerInterface $settingsManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps.settings'),
    );
  }

  /**
   * Dashboard page.
   *
   * @return array<string, mixed>
   *   Render array.
   */
  public function dashboard(): array {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('<h1>PropertySearch Dashboard</h1><p>Welcome to PropertySearch administration.</p>'),
      '#cache' => [
        'tags' => ['ps_dashboard'],
        'max-age' => 300,
      ],
    ];
  }

}
