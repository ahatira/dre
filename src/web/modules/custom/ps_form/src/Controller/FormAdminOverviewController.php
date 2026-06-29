<?php

declare(strict_types=1);

namespace Drupal\ps_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Admin landing page for ps_form configuration.
 */
final class FormAdminOverviewController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->languageManager = $container->get('language_manager');
    return $instance;
  }

  /**
   * Redirects the hub path to the overview tab.
   */
  public function redirectToOverview(): RedirectResponse {
    return $this->redirect('ps_form.admin_overview');
  }

  /**
   * Redirects the legacy monolithic settings URL.
   */
  public function redirectLegacyContactSettings(): RedirectResponse {
    return $this->redirect('ps_form.admin_overview', [], [], 301);
  }

  /**
   * Overview of contact form configuration sections.
   */
  public function overview(): array {
    $build = [
      '#cache' => [
        'contexts' => ['user.permissions', 'languages:language_interface'],
        'max-age' => 0,
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-form-admin-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Configure contact hub webforms, display mode, urgency phone block and visitor confirmation emails.'),
      ],
    ];

    $this->addMultilingualHelpMessage();

    foreach ($this->configurationGroups() as $group) {
      $links = $this->buildGroupLinks($group['items']);
      if ($links === []) {
        continue;
      }

      $build['groups'][$group['id']] = [
        '#type' => 'details',
        '#title' => $group['title'],
        '#description' => $group['description'],
        '#open' => TRUE,
        '#attributes' => ['class' => ['ps-form-admin-overview__group']],
        'links' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-form-admin-overview__group-links']],
          '#theme' => 'admin_block_content',
          '#content' => $links,
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-form-admin-overview__footer']],
      'hub' => Link::createFromRoute(
        $this->t('Back to PS configuration hub'),
        'ps_core.config',
      )->toRenderable(),
    ];

    return $build;
  }

  /**
   * Adds an info message when additional content languages are enabled.
   */
  private function addMultilingualHelpMessage(): void {
    if (!$this->moduleHandler()->moduleExists('config_translation')) {
      return;
    }

    $default = $this->languageManager->getDefaultLanguage()->getId();
    $targets = array_filter(
      $this->languageManager->getLanguages(),
      static fn(string $langcode): bool => $langcode !== $default,
      ARRAY_FILTER_USE_KEY,
    );
    if ($targets === []) {
      return;
    }

    $this->messenger()->addMessage(
      $this->t('Urgency contact and confirmation email wording are translatable. Edit the default site language on each settings tab, then use the Translate tab for other enabled languages.'),
      'info',
    );
  }

  /**
   * Returns grouped configuration sections for the overview page.
   *
   * @return list<array{id: string, title: \Drupal\Core\StringTranslation\TranslatableMarkup, description: \Drupal\Core\StringTranslation\TranslatableMarkup, items: list<array{title: \Drupal\Core\StringTranslation\TranslatableMarkup, description: \Drupal\Core\StringTranslation\TranslatableMarkup, route: string}>}>
   *   Configuration groups.
   */
  private function configurationGroups(): array {
    return [
      [
        'id' => 'display',
        'title' => $this->t('Display and urgency'),
        'description' => $this->t('How contact webforms open on the site and the urgency phone block below actions.'),
        'items' => [
          [
            'title' => $this->t('Display and urgency'),
            'description' => $this->t('Offcanvas, modal or page display mode and urgency contact block wording.'),
            'route' => 'ps_form.settings',
          ],
        ],
      ],
      [
        'id' => 'hub',
        'title' => $this->t('Contact hub'),
        'description' => $this->t('Webforms shown on /form/contact and their order.'),
        'items' => [
          [
            'title' => $this->t('Hub webforms'),
            'description' => $this->t('Enable hub webforms and drag rows to set their order.'),
            'route' => 'ps_form.hub_settings',
          ],
        ],
      ],
      [
        'id' => 'email',
        'title' => $this->t('Confirmation emails'),
        'description' => $this->t('Styled visitor confirmation emails for hub webforms.'),
        'items' => [
          [
            'title' => $this->t('Email settings overview'),
            'description' => $this->t('Copy, footer, hero banners and live confirmation preview.'),
            'route' => 'ps_form.email_overview',
          ],
        ],
      ],
    ];
  }

  /**
   * Builds admin block links for a configuration group.
   *
   * @param list<array{title: \Drupal\Core\StringTranslation\TranslatableMarkup, description: \Drupal\Core\StringTranslation\TranslatableMarkup, route: string}> $items
   *   Group link definitions.
   *
   * @return list<array<string, mixed>>
   *   Items for the admin_block_content theme.
   */
  private function buildGroupLinks(array $items): array {
    $links = [];

    foreach ($items as $item) {
      $url = Url::fromRoute($item['route']);
      if (!$url->access($this->currentUser())) {
        continue;
      }

      $links[] = [
        'title' => $item['title'],
        'url' => $url,
        'description' => $item['description'],
      ];
    }

    return $links;
  }

}
