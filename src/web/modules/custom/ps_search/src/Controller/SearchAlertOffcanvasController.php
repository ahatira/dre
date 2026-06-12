<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_search\Service\SearchAlertCriteriaSerializer;
use Drupal\ps_search\Service\SearchAlertCriteriaSummaryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Loads the search alert webform inside the results offcanvas.
 */
final class SearchAlertOffcanvasController extends ControllerBase {

  public function __construct(
    private readonly SearchAlertCriteriaSerializer $criteriaSerializer,
    private readonly SearchAlertCriteriaSummaryBuilder $summaryBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.alert_criteria_serializer'),
      $container->get('ps_search.alert_criteria_summary_builder'),
    );
  }

  /**
   * Builds offcanvas content with prefilled webform.
   */
  public function offcanvas(Request $request): array {
    if (!$this->moduleHandler()->moduleExists('webform')) {
      return [
        '#markup' => $this->t('Install the Webform module to create search alerts.'),
      ];
    }

    $webform = $this->entityTypeManager()->getStorage('webform')->load('search_alert');
    if ($webform === NULL) {
      return [
        '#markup' => $this->t('The search alert webform is not available yet.'),
      ];
    }

    $criteria = $this->criteriaSerializer->fromRequest($request);
    $alertTitle = trim((string) $request->query->get('alert_title', ''));
    if ($alertTitle === '' && !empty($criteria['search_path'])) {
      $alertTitle = (string) $this->t('Alert for @path', ['@path' => $criteria['search_path']]);
    }

    return [
      '#theme' => 'ps_search_alert_offcanvas',
      '#webform' => $this->entityTypeManager()->getViewBuilder('webform')->view($webform, 'default'),
      '#attached' => [
        'library' => ['ps_search/search-alert-offcanvas', 'ps_theme/form'],
      ],
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.query_args', 'user'],
      ],
    ];
  }

  /**
   * Page title callback.
   */
  public function title(): TranslatableMarkup {
    return $this->t('Create an alert');
  }

}
