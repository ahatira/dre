<?php

declare(strict_types=1);

namespace Drupal\ps_context\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Hub page linking to config translation for matrix entities.
 */
final class MatrixTranslationOverviewController extends ControllerBase {

  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    ModuleHandlerInterface $moduleHandler,
    LanguageManagerInterface $languageManager,
    RouteProviderInterface $routeProvider,
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->moduleHandler = $moduleHandler;
    $this->languageManager = $languageManager;
    $this->routeProvider = $routeProvider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('module_handler'),
      $container->get('language_manager'),
      $container->get('router.route_provider'),
    );
  }

  /**
   * Lists translation links for rules and label profiles.
   */
  public function overview(): array {
    if (!$this->moduleHandler->moduleExists('config_translation')) {
      return [
        '#markup' => '<p>' . $this->t('Enable the Configuration Translation module to translate matrix wording.') . '</p>',
      ];
    }

    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    $targetLanguages = array_filter(
      $this->languageManager->getLanguages(),
      static fn(string $langcode): bool => $langcode !== $defaultLangcode,
      ARRAY_FILTER_USE_KEY,
    );

    if ($targetLanguages === []) {
      return [
        '#markup' => '<p>' . $this->t('Only one site language is enabled. Add languages under Regional settings to translate matrix labels.') . '</p>',
      ];
    }

    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-context-admin-overview__translate']],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Translate rule titles and label wording for each enabled language. Default-language values are edited in the Rules and Labels tabs.'),
      ],
    ];

    $build['rules'] = $this->buildEntityTranslationSection(
      'ps_context_rule',
      (string) $this->t('Context rules'),
      $targetLanguages,
    );

    $build['profiles'] = $this->buildEntityTranslationSection(
      'ps_context_label_profile',
      (string) $this->t('Labels'),
      $targetLanguages,
    );

    return $build;
  }

  /**
   * @param array<string, \Drupal\Core\Language\LanguageInterface> $targetLanguages
   *
   * @return array<string, mixed>
   */
  private function buildEntityTranslationSection(string $entityTypeId, string $title, array $targetLanguages): array {
    $editRoute = 'config_translation.item.edit.entity.' . $entityTypeId . '.edit_form';
    if (!$this->routeExists($editRoute)) {
      return [
        '#type' => 'details',
        '#title' => $title,
        '#open' => TRUE,
        'missing' => [
          '#markup' => '<p>' . $this->t('Translation routes are not available for @type.', ['@type' => $entityTypeId]) . '</p>',
        ],
      ];
    }

    $storage = $this->entityTypeManager->getStorage($entityTypeId);
    $entities = $storage->loadMultiple();

    $header = array_merge(
      [$this->t('Item')],
      array_map(static fn($language) => $language->getName(), $targetLanguages),
    );

    $rows = [];
    foreach ($entities as $entity) {
      $row = [Link::fromTextAndUrl($entity->label(), $entity->toUrl('edit-form'))];
      foreach ($targetLanguages as $langcode => $language) {
        $url = Url::fromRoute($editRoute, [
          $entityTypeId => $entity->id(),
          'langcode' => $langcode,
        ]);
        if ($url->access($this->currentUser())) {
          $row[] = Link::fromTextAndUrl($this->t('Translate'), $url);
        }
        else {
          $row[] = '—';
        }
      }
      $rows[] = $row;
    }

    return [
      '#type' => 'details',
      '#title' => $title,
      '#open' => TRUE,
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No items.'),
      ],
    ];
  }

  /**
   *
   */
  private function routeExists(string $routeName): bool {
    try {
      $this->routeProvider->getRouteByName($routeName);
      return TRUE;
    }
    catch (RouteNotFoundException) {
      return FALSE;
    }
  }

}
