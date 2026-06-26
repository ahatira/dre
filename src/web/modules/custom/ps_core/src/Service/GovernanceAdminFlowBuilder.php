<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Builds the governance hierarchy flowchart for the admin overview page.
 */
final class GovernanceAdminFlowBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly AdminFlowchartBuilder $flowchartBuilder,
    private readonly ImportGovernanceRegistry $governanceRegistry,
    private readonly ImportGovernanceGlobalResolver $globalResolver,
    private readonly ModuleHandlerInterface $moduleHandler,
  ) {}

  /**
   * Builds the governance flow diagram render array.
   *
   * @return array<string, mixed>
   *   Details render array for the overview page.
   */
  public function buildFlowRenderArray(): array {
    $mainLane = [];

    if ($this->moduleHandler->moduleExists('ps_migrate')) {
      $mainLane[] = $this->flowchartBuilder->buildNode(
        'pipeline',
        (string) $this->t('CRM import pipeline'),
        (string) $this->t('Global folders, batch limits, conflict window and default lock strategy for protected entities.'),
        $this->globalResolver->getGlobalLockStrategyLabel(),
      );
      $mainLane[] = $this->flowchartBuilder->buildConnector();
    }

    $mainLane[] = $this->flowchartBuilder->buildNode(
      'global_defaults',
      (string) $this->t('Global governance defaults'),
      (string) $this->t('Inheritance hints on domain forms and reference to the global CRM lock strategy source.'),
      $this->globalResolver->shouldShowDomainInheritanceHints()
        ? (string) $this->t('Inheritance hints: enabled')
        : (string) $this->t('Inheritance hints: disabled'),
    );

    $domains = [];
    foreach ($this->governanceRegistry->getPolicies() as $pluginId => $policy) {
      if ($policy->getSettingsRouteName() === NULL) {
        continue;
      }

      $domains[] = [
        'key' => $pluginId,
        'title' => (string) $policy->getAdminLabel(),
        'description' => (string) $policy->getAdminDescription(),
      ];
    }

    if ($domains !== []) {
      $mainLane[] = $this->flowchartBuilder->buildDomainGrid(
        (string) $this->t('Domain governance'),
        $domains,
      );
    }

    $mainLane[] = $this->flowchartBuilder->buildConnector();
    $mainLane[] = $this->flowchartBuilder->buildNode(
      'import_apply',
      (string) $this->t('CRM import applies rules'),
      (string) $this->t('During Migrate, each entity update is evaluated against the effective domain policy and protection flags.'),
    );

    $diagramChildren = [];
    foreach ($mainLane as $index => $element) {
      $diagramChildren['main_' . $index] = $element;
    }

    $diagramChildren['entity_decision'] = $this->flowchartBuilder->buildFork(
      (string) $this->t('Per entity'),
      [
        'protected' => [
          'label' => (string) $this->t('Protected'),
          'lane' => [
            $this->flowchartBuilder->buildNode(
              'lock_strategy',
              (string) $this->t('Lock strategy'),
              (string) $this->t('Internal lock or checksum conflict triggers the effective strategy (global or domain override).'),
              $this->globalResolver->getGlobalLockStrategyLabel(),
              ['variant' => 'protected'],
            ),
          ],
        ],
        'unprotected' => [
          'label' => (string) $this->t('Unprotected'),
          'lane' => [
            $this->flowchartBuilder->buildNode(
              'field_sync',
              (string) $this->t('Field sync'),
              (string) $this->t('Domain rules decide which CRM fields overwrite local values and which snapshot fields stay in sync.'),
              NULL,
              ['variant' => 'unprotected'],
            ),
          ],
        ],
      ],
    );

    return $this->flowchartBuilder->buildDetailsSection(
      (string) $this->t('How import governance works'),
      (string) $this->t('Global CRM pipeline defaults cascade to domain policies, then to per-entity protection during each import run.'),
      $diagramChildren,
      (string) $this->t('Import governance flowchart from global pipeline defaults to per-entity protection.'),
      'ps-governance-admin-overview__flow',
    );
  }

}
