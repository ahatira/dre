<?php

declare(strict_types=1);

namespace Drupal\ps_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\ps_form\Service\ContactEmailPreviewBuilder;
use Drupal\ps_form\Service\ContactNeedRouter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Admin landing page for contact confirmation email settings.
 */
final class ContactEmailAdminOverviewController extends ControllerBase {

  public function __construct(
    private readonly ContactEmailPreviewBuilder $previewBuilder,
    private readonly ContactNeedRouter $contactNeedRouter,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_form.contact_email_preview_builder'),
      $container->get('ps_form.contact_need_router'),
    );
  }

  /**
   * Redirects the email hub path to the overview tab.
   */
  public function redirectToOverview(): RedirectResponse {
    return $this->redirect('ps_form.email_overview');
  }

  /**
   * Redirects legacy preview URL to email overview.
   */
  public function redirectLegacyPreview(): RedirectResponse {
    return $this->redirect('ps_form.email_overview', [], [], 301);
  }

  /**
   * Email overview with live confirmation template preview.
   */
  public function overview(Request $request): array {
    $webformIds = $this->previewBuilder->getWebformIds();
    $queryWebform = $request->query->getString('webform');
    $selected = in_array($queryWebform, $webformIds, TRUE) ? $queryWebform : ($webformIds[0] ?? 'find_property');

    $definitions = $this->contactNeedRouter->getAllManagedWebforms();
    $options = [];
    foreach ($webformIds as $webformId) {
      $options[$webformId] = $definitions[$webformId]['title'] ?? $webformId;
    }

    $previewUrl = Url::fromRoute('ps_form.email_overview')->toString();
    $metadata = $this->previewBuilder->getPreviewMetadata($selected);

    $build = [
      '#attached' => [
        'library' => ['ps_form/admin_email_preview'],
      ],
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.query_args:webform', 'languages:language_interface', 'user.permissions'],
      ],
    ];

    $build['intro'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-form-email-admin-overview__intro']],
      'lead' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Sample confirmation email using current settings and representative submission data. The preview below mimics an inbox view — headers reflect the webform handler; the frame shows the HTML sent to visitors.'),
      ],
    ];

    $build['selector'] = [
      '#type' => 'select',
      '#title' => $this->t('Webform'),
      '#options' => $options,
      '#default_value' => $selected,
      '#attributes' => [
        'class' => ['ps-form-email-preview__webform-select'],
        'onchange' => 'window.location.href = ' . json_encode($previewUrl, JSON_THROW_ON_ERROR) . ' + "?webform=" + encodeURIComponent(this.value)',
      ],
    ];

    if ($metadata === NULL) {
      $build['preview_layout'] = [
        '#markup' => '<p>' . $this->t('Could not render preview for the selected webform.') . '</p>',
      ];
    }
    else {
      $frameUrl = Url::fromRoute('ps_form.email_preview_frame', ['webform' => $selected])->toString();
      $fromLine = $metadata['from_name'] . ' <' . $metadata['from_email'] . '>';
      $toLine = $metadata['to_name'] . ' <' . $metadata['to_email'] . '>';

      $build['preview_layout'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-form-email-preview-layout']],
        'client' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-form-email-client']],
          'toolbar' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['ps-form-email-client__toolbar']],
            'title' => [
              '#type' => 'html_tag',
              '#tag' => 'span',
              '#value' => $this->t('Inbox preview'),
              '#attributes' => ['class' => ['ps-form-email-client__toolbar-title']],
            ],
            'viewport_toggle' => [
              '#type' => 'container',
              '#attributes' => [
                'class' => ['ps-form-email-client__viewport-toggle'],
                'role' => 'group',
                'aria-label' => $this->t('Preview width'),
              ],
              'desktop' => [
                '#type' => 'html_tag',
                '#tag' => 'button',
                '#value' => $this->t('Desktop'),
                '#attributes' => [
                  'type' => 'button',
                  'class' => ['ps-form-email-client__viewport-btn', 'is-active'],
                  'data-viewport' => 'desktop',
                ],
              ],
              'mobile' => [
                '#type' => 'html_tag',
                '#tag' => 'button',
                '#value' => $this->t('Mobile'),
                '#attributes' => [
                  'type' => 'button',
                  'class' => ['ps-form-email-client__viewport-btn'],
                  'data-viewport' => 'mobile',
                ],
              ],
            ],
          ],
          'headers' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['ps-form-email-client__headers']],
            'from' => $this->buildHeaderRow($this->t('From'), $fromLine),
            'to' => $this->buildHeaderRow($this->t('To'), $toLine),
            'subject' => $this->buildHeaderRow($this->t('Subject'), $metadata['subject'], TRUE),
            'date' => $this->buildHeaderRow($this->t('Date'), $metadata['sent_at']),
            'heading' => [
              '#type' => 'container',
              '#attributes' => ['class' => ['ps-form-email-client__headers-note']],
              'text' => [
                '#type' => 'html_tag',
                '#tag' => 'p',
                '#value' => $this->t('Email heading: @title', ['@title' => $metadata['display_title']]),
              ],
            ],
          ],
          'viewport' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['ps-form-email-client__viewport']],
            'iframe' => [
              '#type' => 'html_tag',
              '#tag' => 'iframe',
              '#attributes' => [
                'class' => ['ps-form-email-preview__iframe'],
                'title' => $this->t('Email preview'),
                'src' => $frameUrl,
                'loading' => 'lazy',
              ],
            ],
          ],
        ],
      ];
    }

    $build['footer'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-form-email-admin-overview__footer']],
      'back' => Link::createFromRoute(
        $this->t('Back to Forms overview'),
        'ps_form.admin_overview',
      )->toRenderable(),
    ];

    return $build;
  }

  /**
   * Builds a mail header row for the preview chrome.
   *
   * @return array<string, mixed>
   *   Render array for one header row.
   */
  private function buildHeaderRow(\Stringable|string $label, string $value, bool $emphasize = FALSE): array {
    $valueClasses = ['ps-form-email-client__header-value'];
    if ($emphasize) {
      $valueClasses[] = 'is-emphasis';
    }

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-form-email-client__header-row']],
      'label' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $label,
        '#attributes' => ['class' => ['ps-form-email-client__header-label']],
      ],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $value,
        '#attributes' => ['class' => $valueClasses],
      ],
    ];
  }

}
