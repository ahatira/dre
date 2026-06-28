<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\Routing\Route;
use Symfony\Component\Yaml\Yaml;

/**
 * Maps contact hub targets to direct-access webforms and shell routes.
 */
final class ContactNeedRouter {

  use StringTranslationTrait;

  public const HUB_WEBFORM_ID = 'contact';

  public const FROM_HUB_QUERY = 'from_hub';

  public const FROM_HUB_FIELD = 'ps_from_hub';

  /**
   * Default webform category for PS Form shipped webforms.
   */
  public const PS_WEBFORM_CATEGORY = 'Property Search';

  /**
   * Default hub-enabled webform ids (Stellar mockup order, step 1).
   *
   * @var list<string>
   */
  public const DEFAULT_HUB_ENABLED_WEBFORM_IDS = [
    'find_property',
    'entrust_search',
    'get_advice',
    'entrust_property',
    'invest_sell',
    'other_request',
  ];

  /**
   * Cached routable webform definitions keyed by webform id.
   *
   * @var array<string, array{path: string}>|null
   */
  private ?array $registeredDefinitions = NULL;

  /**
   * Cached public paths keyed by webform id.
   *
   * @var array<string, string>|null
   */
  private ?array $webformPaths = NULL;

  /**
   * Cached webform ids listed in the admin hub table.
   *
   * @var list<string>|null
   */
  private ?array $managedWebformIds = NULL;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly RouteProviderInterface $routeProvider,
    private readonly ModuleExtensionList $moduleExtensionList,
  ) {}

  /**
   * Returns hub-enabled webform definitions keyed by webform id.
   *
   * @return array<string, array{path: string}>
   *   Enabled hub webform definitions keyed by machine name.
   */
  public function getDirectDefinitions(): array {
    $definitions = [];
    foreach ($this->getEnabledHubWebformIds() as $webformId) {
      $definition = $this->getRegisteredDefinitions()[$webformId] ?? NULL;
      if ($definition !== NULL) {
        $definitions[$webformId] = $definition;
      }
    }

    return $definitions;
  }

  /**
   * Returns open webforms available on the contact hub (excluding hub).
   *
   * @return array<string, array{path: string}>
   *   Webform definitions keyed by machine name.
   */
  public function getRegisteredDefinitions(): array {
    if ($this->registeredDefinitions !== NULL) {
      return $this->registeredDefinitions;
    }

    $definitions = [];
    foreach ($this->getManagedWebformIds() as $webformId) {
      if ($webformId === self::HUB_WEBFORM_ID) {
        continue;
      }

      if (!$this->isWebformOpen($webformId)) {
        continue;
      }

      $definitions[$webformId] = [
        'path' => $this->buildWebformPath($webformId),
      ];
    }

    ksort($definitions);
    $this->registeredDefinitions = $definitions;

    return $definitions;
  }

  /**
   * Returns hub-enabled webform ids saved in config (admin order, unfiltered).
   *
   * @return list<string>
   *   Configured hub webform machine names.
   */
  public function getConfiguredEnabledHubWebformIds(): array {
    $enabled = $this->configFactory->get('ps_form.settings')->get('contact_hub_enabled_webforms');
    if (!is_array($enabled) || $enabled === []) {
      return self::DEFAULT_HUB_ENABLED_WEBFORM_IDS;
    }

    $ids = [];
    foreach ($enabled as $webformId) {
      if (is_string($webformId) && $webformId !== '') {
        $ids[] = $webformId;
      }
    }

    return $ids;
  }

  /**
   * Returns enabled hub webform ids visible on the front hub (open + routable).
   *
   * @return list<string>
   *   Enabled hub webform machine names.
   */
  public function getEnabledHubWebformIds(): array {
    $registered = array_keys($this->getRegisteredDefinitions());
    if ($registered === []) {
      return [];
    }

    $configured = $this->getConfiguredEnabledHubWebformIds();
    if ($configured === []) {
      return $registered;
    }

    $registeredLookup = array_fill_keys($registered, TRUE);
    $filtered = [];
    foreach ($configured as $webformId) {
      if (isset($registeredLookup[$webformId])) {
        $filtered[] = $webformId;
      }
    }

    return $filtered !== [] ? $filtered : $registered;
  }

  /**
   * Checks whether a webform is enabled on the contact hub.
   */
  public function isHubEnabledWebform(string $webformId): bool {
    return in_array($webformId, $this->getEnabledHubWebformIds(), TRUE);
  }

  /**
   * Checks whether a webform is open and can be loaded via the PS Form shell.
   */
  public function isRoutableWebform(string $webformId): bool {
    return isset($this->getRegisteredDefinitions()[$webformId]);
  }

  /**
   * Returns the default selected webform for the hub radios.
   */
  public function getDefaultHubWebformId(): ?string {
    $enabled = $this->getEnabledHubWebformIds();

    return $enabled[0] ?? NULL;
  }

  /**
   * Returns the public path for a webform id.
   */
  public function getPathForWebform(string $webformId): ?string {
    if ($webformId === self::HUB_WEBFORM_ID) {
      return '/form/contact';
    }

    if (!$this->webformConfigExists($webformId)) {
      return NULL;
    }

    return $this->buildWebformPath($webformId);
  }

  /**
   * Checks whether a webform can be toggled on the contact hub in admin.
   */
  public function isHubToggleableWebform(string $webformId): bool {
    return $webformId !== self::HUB_WEBFORM_ID
      && in_array($webformId, $this->getManagedWebformIds(), TRUE);
  }

  /**
   * Checks whether an enabled hub webform is visible on the front hub.
   */
  public function isHubVisibleWebform(string $webformId): bool {
    return $this->isWebformOpen($webformId) && $this->webformConfigExists($webformId);
  }

  /**
   * Returns all webforms shipped by ps_form for admin display.
   *
   * @return array<string, array{webform: string, title: string, path: string|null, opened: bool}>
   *   Webform metadata keyed by machine name.
   */
  public function getAllManagedWebforms(): array {
    $webforms = $this->getManagedWebformDefinitions();

    uasort($webforms, static function (array $a, array $b): int {
      if ($a['webform'] === self::HUB_WEBFORM_ID) {
        return -1;
      }
      if ($b['webform'] === self::HUB_WEBFORM_ID) {
        return 1;
      }
      $aHasPath = $a['path'] !== NULL;
      $bHasPath = $b['path'] !== NULL;
      if ($aHasPath !== $bHasPath) {
        return $aHasPath ? -1 : 1;
      }

      return strnatcasecmp($a['title'], $b['title']);
    });

    return $webforms;
  }

  /**
   * Returns PS Form webforms in admin table order (hub first, then the rest).
   *
   * @return list<string>
   *   Webform machine names.
   */
  public function getAdminWebformTableOrder(): array {
    $definitions = $this->getManagedWebformDefinitions();
    $ordered = $this->getConfiguredEnabledHubWebformIds();

    foreach (array_keys($definitions) as $webformId) {
      if (!in_array($webformId, $ordered, TRUE)) {
        $ordered[] = $webformId;
      }
    }

    return $ordered;
  }

  /**
   * Checks whether a webform config exists and is open.
   */
  public function isWebformOpen(string $webformId): bool {
    return $this->isWebformConfigOpen($webformId);
  }

  /**
   * Builds webform metadata keyed by machine name.
   *
   * @return array<string, array{webform: string, title: string, path: string|null, opened: bool}>
   *   Webform metadata keyed by machine name.
   */
  private function getManagedWebformDefinitions(): array {
    $webforms = [];

    foreach ($this->getManagedWebformIds() as $webformId) {
      $webforms[$webformId] = [
        'webform' => $webformId,
        'title' => $this->getWebformAdminTitle($webformId),
        'path' => $this->webformConfigExists($webformId)
          ? $this->getPathForWebform($webformId)
          : NULL,
        'opened' => $this->isWebformConfigOpen($webformId),
      ];
    }

    return $webforms;
  }

  /**
   * Lists routable direct webform ids (open + public path).
   *
   * @return list<string>
   *   Routable webform machine names.
   */
  public function getDirectWebformIds(): array {
    return array_keys($this->getRegisteredDefinitions());
  }

  /**
   * Maps hub-enabled webform ids to shell paths (deeplink / hub JS).
   *
   * @return array<string, string>
   *   Webform machine names keyed to public paths.
   */
  public function getWebformPathMap(): array {
    $map = [
      self::HUB_WEBFORM_ID => '/form/contact',
    ];

    foreach ($this->getDirectDefinitions() as $webformId => $definition) {
      $map[$webformId] = $definition['path'];
    }

    return $map;
  }

  /**
   * Maps all routable contact-family webforms to shell paths.
   *
   * @return array<string, string>
   *   Webform machine names keyed to public paths.
   */
  public function getRoutableWebformPathMap(): array {
    $map = [
      self::HUB_WEBFORM_ID => '/form/contact',
    ];

    foreach ($this->getRegisteredDefinitions() as $webformId => $definition) {
      $map[$webformId] = $definition['path'];
    }

    return $map;
  }

  /**
   * Checks whether a webform belongs to the contact family (hub or PS shell).
   */
  public function isContactFamilyWebform(string $webformId): bool {
    if ($webformId === self::HUB_WEBFORM_ID) {
      return TRUE;
    }

    return $this->webformConfigExists($webformId)
      && in_array($webformId, $this->getManagedWebformIds(), TRUE);
  }

  /**
   * Returns wizard page progress entries for a contact-family webform.
   *
   * @return list<array{name: string, title: string, type: string}>
   *   Ordered wizard pages excluding the hub need step.
   */
  public function getWizardProgressPages(string $webformId): array {
    $elementsYaml = $this->configFactory->get('webform.webform.' . $webformId)->get('elements');
    if (!is_string($elementsYaml) || $elementsYaml === '') {
      return [];
    }

    try {
      $elements = Yaml::parse($elementsYaml);
    }
    catch (\Throwable) {
      return [];
    }

    if (!is_array($elements)) {
      return [];
    }

    $pages = [];
    foreach ($elements as $key => $element) {
      if (!is_string($key) || !is_array($element)) {
        continue;
      }
      if (($element['#type'] ?? NULL) !== 'webform_wizard_page' || $key === 'step_need') {
        continue;
      }

      $title = $element['#title'] ?? $key;
      $pages[] = [
        'name' => $key,
        'title' => is_string($title) ? $title : (string) $title,
        'type' => 'page',
      ];
    }

    return $pages;
  }

  /**
   * Returns the translated title of the hub wizard target step.
   */
  public function getHubNeedStepTitle(): string {
    $elementsYaml = $this->configFactory->get('webform.webform.' . self::HUB_WEBFORM_ID)->get('elements');
    if (is_string($elementsYaml) && $elementsYaml !== '') {
      $elements = Yaml::parse($elementsYaml);
      if (is_array($elements)) {
        $title = $elements['step_need']['#title'] ?? NULL;
        if (is_string($title) && $title !== '') {
          return $title;
        }
      }
    }

    return (string) $this->t('Need');
  }

  /**
   * Resolves the page or link title for a contact-family webform.
   */
  public function getPageTitle(string $webformId): TranslatableMarkup|string {
    $configTitle = $this->getWebformConfigTitle($webformId);
    if ($configTitle !== NULL) {
      return $configTitle;
    }

    return $this->t('Contact us');
  }

  /**
   * Resolves a display title for a direct webform (legacy callers).
   */
  public function getTitleForWebform(string $webformId): ?string {
    return $this->getWebformConfigTitle($webformId);
  }

  /**
   * Reads the webform title from config for admin lists.
   */
  public function getWebformAdminTitle(string $webformId): string {
    $title = $this->configFactory->get('webform.webform.' . $webformId)->get('title');
    if (is_string($title) && $title !== '') {
      return $title;
    }

    return $webformId;
  }

  /**
   * Resolves the DOM panel id used by contact wizard CSS hooks.
   */
  public function resolvePanelId(string $webformId): string {
    if ($webformId === self::HUB_WEBFORM_ID) {
      return 'contact-panel';
    }

    return str_replace('_', '-', $webformId) . '-panel';
  }

  /**
   * Returns webform ids listed in the contact hub admin table.
   *
   * Includes PS Form install webforms and every webform entity on the site.
   *
   * @return list<string>
   *   Sorted webform ids.
   */
  private function getManagedWebformIds(): array {
    if ($this->managedWebformIds !== NULL) {
      return $this->managedWebformIds;
    }

    $ids = $this->getInstallWebformIds();
    $storage = $this->entityTypeManager->getStorage('webform');
    foreach (array_keys($storage->getQuery()->accessCheck(FALSE)->execute()) as $webformId) {
      $ids[] = $webformId;
    }

    $ids = array_values(array_unique($ids));
    sort($ids, SORT_NATURAL);
    $this->managedWebformIds = $ids;

    return $ids;
  }

  /**
   * Returns webform machine names shipped in ps_form config/install.
   *
   * @return list<string>
   *   Sorted webform ids.
   */
  private function getInstallWebformIds(): array {
    $installPath = $this->moduleExtensionList->getPath('ps_form') . '/config/install';
    $pattern = $installPath . '/webform.webform.*.yml';
    $ids = [];

    foreach (glob($pattern) ?: [] as $file) {
      $data = Yaml::parseFile($file);
      if (!is_array($data) || empty($data['id']) || !is_string($data['id'])) {
        continue;
      }
      $ids[] = $data['id'];
    }

    sort($ids, SORT_NATURAL);

    return $ids;
  }

  /**
   * Reads the translated webform title from config when the form is open.
   */
  private function getWebformConfigTitle(string $webformId): ?string {
    if (!$this->isWebformConfigOpen($webformId)) {
      return NULL;
    }

    $title = $this->configFactory->get('webform.webform.' . $webformId)->get('title');
    if (!is_string($title) || $title === '') {
      return NULL;
    }

    return $title;
  }

  /**
   * Builds the public shell path for a webform (pretty route or /form/{id}).
   */
  private function buildWebformPath(string $webformId): string {
    $explicit = $this->loadExplicitShellPaths();
    if (isset($explicit[$webformId])) {
      return $explicit[$webformId];
    }

    return '/form/' . $webformId;
  }

  /**
   * Checks whether a webform config exists.
   */
  private function webformConfigExists(string $webformId): bool {
    return !$this->configFactory->get('webform.webform.' . $webformId)->isNew();
  }

  /**
   * Reads dedicated shell paths from ps_form.routing.yml entries.
   *
   * @return array<string, string>
   *   Paths keyed by webform machine name.
   */
  private function loadExplicitShellPaths(): array {
    if ($this->webformPaths !== NULL) {
      return $this->webformPaths;
    }

    $paths = [];
    foreach ($this->routeProvider->getAllRoutes() as $route) {
      if (!$route instanceof Route) {
        continue;
      }

      $controller = $route->getDefault('_controller');
      if (!is_string($controller) || !str_contains($controller, 'FormOffcanvasController')) {
        continue;
      }

      $webformId = $route->getDefault('webform');
      if (!is_string($webformId) || $webformId === '') {
        continue;
      }

      $path = $route->getPath();
      if ($path === '' || str_contains($path, '{webform}')) {
        continue;
      }

      $paths[$webformId] = $path;
    }

    $this->webformPaths = $paths;

    return $paths;
  }

  /**
   * Checks whether a webform config exists and is open.
   */
  private function isWebformConfigOpen(string $webformId): bool {
    $config = $this->configFactory->get('webform.webform.' . $webformId);
    if ($config->isNew()) {
      return FALSE;
    }

    $status = $config->get('status');
    return $status === 'open' || $status === TRUE;
  }

}
