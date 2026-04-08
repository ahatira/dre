<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;

/**
 * Builds the Agent settings landing page.
 */
final class AgentSettingsController extends ControllerBase
{
  /**
   * Agent settings overview.
   */
    public function overview(): array
    {
        $links = [
            Link::createFromRoute(
                $this->t('Manage fields'),
                'entity.agent.field_ui_fields'
            )->toRenderable(),
            Link::createFromRoute(
                $this->t('Manage form display'),
                'entity.entity_form_display.agent.default'
            )->toRenderable(),
            Link::createFromRoute(
                $this->t('Manage display'),
                'entity.entity_view_display.agent.default'
            )->toRenderable(),
        ];

        return [
            '#type' => 'container',
            '#attributes' => ['class' => ['ps-agent-settings-overview']],
            'description' => [
                '#markup' => $this->t(
                    'Use the tabs on this page to manage Agent fields, form display, and display modes.'
                ),
            ],
            'links_title' => [
                '#type' => 'html_tag',
                '#tag' => 'h2',
                '#value' => $this->t('Agent administration'),
            ],
            'links' => [
                '#theme' => 'item_list',
                '#items' => $links,
            ],
        ];
    }
}
