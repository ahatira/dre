<?php

declare(strict_types=1);

namespace Drupal\ps_email\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\ps_email\Service\ContactWebformEmailSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Lists hub webforms for contact confirmation email copy settings.
 */
final class ContactEmailOverviewController extends ControllerBase {

  use StringTranslationTrait;

  /**
   * Human-readable labels for hub webforms.
   *
   * @var array<string, string>
   */
  private const WEBFORM_LABELS = [
    'find_property' => 'Find a property',
    'entrust_search' => 'Entrust a search',
    'get_advice' => 'Get advice',
    'entrust_property' => 'Entrust a property',
    'invest_sell' => 'Invest or sell',
    'other_request' => 'Other request',
  ];

  public function __construct(
    private readonly ContactWebformEmailSettings $contactWebformEmailSettings,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_email.contact_webform_email_settings'),
    );
  }

  /**
   * Builds the contact email copy overview page.
   *
   * @return array<string, mixed>
   *   Render array.
   */
  public function overview(): array {
    $rows = [];
    foreach ($this->contactWebformEmailSettings->getWebformIds() as $webformId) {
      $label = self::WEBFORM_LABELS[$webformId] ?? $webformId;
      $url = Url::fromRoute('ps_email.contact_webform', ['webform_id' => $webformId]);
      $rows[] = [
        $label,
        ['data' => Link::fromTextAndUrl($this->t('Edit copy'), $url)->toRenderable()],
      ];
    }

    return [
      '#type' => 'container',
      'intro' => [
        '#markup' => '<p>' . $this->t(
          'Configure confirmation email wording per contact hub webform. Hero images are managed in Webform UI.',
        ) . '</p>',
      ],
      'table' => [
        '#type' => 'table',
        '#header' => [$this->t('Webform'), $this->t('Actions')],
        '#rows' => $rows,
        '#empty' => $this->t('No hub webforms configured.'),
      ],
    ];
  }

}
