<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Builds reusable admin flowchart render arrays.
 */
final class AdminFlowchartBuilder {

  use StringTranslationTrait;

  /**
   * Wraps flowchart children in a diagram container.
   *
   * @param array<string, mixed> $children
   *   Flowchart elements keyed for the render array.
   *
   * @return array<string, mixed>
   *   Diagram container render array.
   */
  public function buildDiagram(array $children, string $ariaLabel): array {
    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['ps-admin-flowchart'],
        'role' => 'img',
        'aria-label' => $ariaLabel,
      ],
    ] + $children;
  }

  /**
   * Builds an expandable details section around a flowchart diagram.
   *
   * @param array<string, mixed> $diagramChildren
   *   Flowchart elements keyed for the render array.
   *
   * @return array<string, mixed>
   *   Details render array containing the diagram.
   */
  public function buildDetailsSection(
    string $title,
    string $intro,
    array $diagramChildren,
    string $ariaLabel,
    string $wrapperClass,
  ): array {
    return [
      '#type' => 'details',
      '#title' => $title,
      '#open' => TRUE,
      '#attributes' => ['class' => [$wrapperClass]],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $intro,
        '#attributes' => ['class' => [$wrapperClass . '-intro']],
      ],
      'diagram' => $this->buildDiagram($diagramChildren, $ariaLabel),
    ];
  }

  /**
   * Builds one flowchart node.
   *
   * @param array{optional?: bool, variant?: string, compact?: bool} $options
   *   Optional styling flags.
   *
   * @return array<string, mixed>
   *   Flowchart node render array.
   */
  public function buildNode(
    string $key,
    string $title,
    string $description,
    ?string $meta = NULL,
    array $options = [],
  ): array {
    $classes = ['ps-admin-flowchart__node'];
    if (!empty($options['optional'])) {
      $classes[] = 'ps-admin-flowchart__node--optional';
    }
    if (!empty($options['compact'])) {
      $classes[] = 'ps-admin-flowchart__node--compact';
    }
    if (!empty($options['variant'])) {
      $classes[] = 'ps-admin-flowchart__node--' . $options['variant'];
    }

    $node = [
      '#type' => 'container',
      '#attributes' => [
        'class' => $classes,
        'data-flow-step' => $key,
      ],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $title,
        '#attributes' => ['class' => ['ps-admin-flowchart__node-title']],
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $description,
        '#attributes' => ['class' => ['ps-admin-flowchart__node-description']],
      ],
    ];

    if (!empty($options['optional'])) {
      $node['badge'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $this->t('Optional'),
        '#attributes' => ['class' => ['ps-admin-flowchart__node-badge']],
      ];
    }

    if ($meta !== NULL && $meta !== '') {
      $node['meta'] = [
        '#type' => 'html_tag',
        '#tag' => 'code',
        '#value' => $meta,
        '#attributes' => ['class' => ['ps-admin-flowchart__node-meta']],
      ];
    }

    return $node;
  }

  /**
   * Builds a vertical connector between flowchart nodes.
   *
   * @return array<string, mixed>
   *   Connector render array.
   */
  public function buildConnector(bool $optional = FALSE): array {
    $classes = ['ps-admin-flowchart__connector'];
    if ($optional) {
      $classes[] = 'ps-admin-flowchart__connector--optional';
    }

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => $classes,
        'aria-hidden' => 'true',
      ],
    ];
  }

  /**
   * Builds a fork with labelled branches.
   *
   * @param array<string, array{label: string, lane: list<array<string, mixed>>}> $branches
   *   Branch metadata keyed by branch id.
   *
   * @return array<string, mixed>
   *   Fork render array.
   */
  public function buildFork(string $title, array $branches): array {
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-admin-flowchart__fork']],
      'connector' => $this->buildConnector(),
      'hub' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $title,
        '#attributes' => ['class' => ['ps-admin-flowchart__fork-hub']],
      ],
      'branches' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-admin-flowchart__fork-branches']],
      ],
    ];

    foreach ($branches as $branchKey => $branch) {
      $laneChildren = [];
      foreach ($branch['lane'] as $index => $element) {
        $laneChildren['lane_' . $index] = $element;
      }

      $build['branches'][$branchKey] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'ps-admin-flowchart__branch',
            'ps-admin-flowchart__branch--' . $branchKey,
          ],
        ],
        'label' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $branch['label'],
          '#attributes' => ['class' => ['ps-admin-flowchart__branch-label']],
        ],
        'lane' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-admin-flowchart__branch-lane']],
        ] + $laneChildren,
      ];
    }

    return $build;
  }

  /**
   * Builds a hub with a responsive grid of compact domain nodes.
   *
   * @param list<array{key: string, title: string, description: string}> $domains
   *   Domain node metadata.
   *
   * @return array<string, mixed>
   *   Domain hub render array.
   */
  public function buildDomainGrid(string $title, array $domains): array {
    $gridChildren = [];
    foreach ($domains as $domain) {
      $gridChildren['domain_' . $domain['key']] = $this->buildNode(
        $domain['key'],
        $domain['title'],
        $domain['description'],
        NULL,
        ['compact' => TRUE],
      );
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-admin-flowchart__domain-hub']],
      'connector' => $this->buildConnector(),
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $title,
        '#attributes' => ['class' => ['ps-admin-flowchart__domain-hub-title']],
      ],
      'grid' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-admin-flowchart__domain-grid']],
      ] + $gridChildren,
    ];
  }

}
