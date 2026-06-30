<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\ps_email\ValueObject\EmailTransactionDefinition;

/**
 * Builds the transactional email admin hub overview.
 */
final class EmailAdminOverviewBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly EmailTransactionRegistry $emailTransactionRegistry,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ModuleHandlerInterface $moduleHandler,
    private readonly AccountProxyInterface $currentUser,
  ) {}

  /**
   * Builds the email hub render array.
   *
   * @return array<string, mixed>
   *   Render array.
   */
  public function buildOverview(): array {
    $definitions = $this->emailTransactionRegistry->getDefinitions();

    $build = [
      '#attached' => [
        'library' => ['ps_email/email_admin_overview'],
      ],
      '#cache' => [
        'contexts' => ['user.permissions', 'languages:language_interface'],
        'max-age' => 0,
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-email-admin-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Central hub for Property Search transactional emails (Symfony Mailer + MJML). Validate sends with Mailpit (<code>make email-e2e</code>). MJML template preview is reserved for technical administrators.'),
      ],
    ];

    $build['types'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Email'),
        $this->t('Mailer policy'),
        $this->t('MJML theme'),
        $this->t('Actions'),
      ],
      '#attributes' => ['class' => ['ps-email-admin-overview__table']],
      '#empty' => $this->t('No transactional email modules are enabled.'),
    ];

    foreach ($definitions as $definition) {
      $build['types'][$definition->id] = [
        'label' => [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '<strong>{{ label }}</strong><br><span class="ps-email-admin-overview__description">{{ description }}</span>',
            '#context' => [
              'label' => $definition->label,
              'description' => $definition->description,
            ],
          ],
        ],
        'policy' => [
          '#markup' => '<code>' . $definition->mailerPolicyId . '</code>',
        ],
        'theme' => [
          '#markup' => '<code>' . $this->getMailerTheme($definition->mailerPolicyId) . '</code>',
        ],
        'actions' => [
          'data' => [
            '#theme' => 'item_list',
            '#items' => $this->buildActionLinks($definition),
            '#attributes' => ['class' => ['ps-email-admin-overview__actions']],
          ],
        ],
      ];
    }

    if ($this->currentUser->hasPermission('administer ps_email')) {
      $build['ops'] = [
        '#type' => 'details',
        '#title' => $this->t('Dev tools and CI'),
        '#open' => TRUE,
        '#attributes' => ['class' => ['ps-email-admin-overview__ops']],
        'list' => [
          '#theme' => 'item_list',
          '#items' => $this->buildOpsLinks(),
        ],
        'ci' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Run the full Mailpit suite: <code>make email-e2e</code> (default country: com).'),
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-email-admin-overview__footer']],
      'hub' => Link::createFromRoute(
        $this->t('Back to PS configuration hub'),
        'ps_core.config',
      )->toRenderable(),
    ];

    if ($this->currentUser->hasPermission('manage ps_email content')) {
      $build['shell'] = [
        '#type' => 'details',
        '#title' => $this->t('Email shell'),
        '#open' => FALSE,
        '#attributes' => ['class' => ['ps-email-admin-overview__shell']],
        'links' => [
          '#theme' => 'item_list',
          '#items' => array_filter([
            ($url = Url::fromRoute('ps_email.contact'))->access($this->currentUser)
              ? Link::fromTextAndUrl($this->t('Contact copy (per webform)'), $url)->toRenderable()
              : NULL,
            ($url = Url::fromRoute('ps_email.shell_footer'))->access($this->currentUser)
              ? Link::fromTextAndUrl($this->t('Footer and legal'), $url)->toRenderable()
              : NULL,
            ($url = Url::fromRoute('ps_email.user_account'))->access($this->currentUser)
              ? Link::fromTextAndUrl($this->t('User account emails'), $url)->toRenderable()
              : NULL,
          ]),
        ],
      ];
    }

    return $build;
  }

  /**
   * Builds action links for one email type row.
   *
   * @return list<array<string, mixed>>
   *   Link render arrays.
   */
  private function buildActionLinks(EmailTransactionDefinition $definition): array {
    $links = [];

    if ($definition->id === 'contact_confirmation' && $this->moduleHandler->moduleExists('webform')) {
      $url = Url::fromRoute('entity.webform.collection');
      if ($url->access($this->currentUser)) {
        $links[] = Link::fromTextAndUrl($this->t('Webform UI'), $url)->toRenderable();
      }
    }

    if ($definition->configRoute !== NULL) {
      $url = Url::fromRoute($definition->configRoute);
      if ($url->access($this->currentUser)) {
        $links[] = Link::fromTextAndUrl($this->t('Settings'), $url)->toRenderable();
      }
    }

    if ($definition->mjmlPreviewTemplate !== NULL
      && $this->moduleHandler->moduleExists('mjml_render_devel')
      && $this->currentUser->hasPermission('administer ps_email')) {
      $url = Url::fromRoute('mjml_render_devel.preview', [
        'template_path' => $definition->mjmlPreviewTemplate,
      ]);
      if ($url->access($this->currentUser)) {
        $links[] = Link::fromTextAndUrl($this->t('MJML preview'), $url)->toRenderable();
      }
    }

    if ($definition->e2eScript !== NULL && $this->currentUser->hasPermission('administer ps_email')) {
      $links[] = [
        '#markup' => '<code>' . $definition->e2eScript . '</code>',
      ];
    }

    if ($links === []) {
      $links[] = ['#markup' => $this->t('No actions available for your role.')];
    }

    return $links;
  }

  /**
   * Builds dev/ops helper links for the overview page.
   *
   * @return list<array<string, mixed>>
   *   Link render arrays.
   */
  private function buildOpsLinks(): array {
    $links = [];

    if ($this->moduleHandler->moduleExists('mjml_render_devel')) {
      $url = Url::fromRoute('mjml_render_devel.list');
      if ($url->access($this->currentUser)) {
        $links[] = Link::fromTextAndUrl($this->t('MJML template gallery'), $url)->toRenderable();
      }
    }

    $links[] = [
      '#markup' => Link::fromTextAndUrl(
        $this->t('Mailpit inbox (dev)'),
        Url::fromUri('http://localhost:8025', ['attributes' => ['target' => '_blank']]),
      )->toString(),
    ];

    return $links;
  }

  /**
   * Resolves the configured email theme for a mailer policy id.
   */
  private function getMailerTheme(string $policyId): string {
    $config = $this->configFactory->get('mailer_policy.mailer_policy.' . $policyId);
    $theme = $config->get('configuration.email_theme.theme');
    return is_string($theme) && $theme !== '' ? $theme : 'ps_theme';
  }

}
