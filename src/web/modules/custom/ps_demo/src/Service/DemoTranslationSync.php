<?php

declare(strict_types=1);

namespace Drupal\ps_demo\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;

/**
 * Ensures demo content has translations for every enabled site language.
 *
 * Default content exports provide EN (+ FR in translations). After import,
 * missing translations are created from the best available source language.
 */
final class DemoTranslationSync {

  /**
   * Demo FAQ node UUIDs (ps_demo export, ps_faq FaqDefaultItems).
   *
   * @var list<string>
   */
  private const FAQ_UUIDS = [
    'b2000004-0000-4000-8000-000000000001',
    'b2000004-0000-4000-8000-000000000002',
    'b2000004-0000-4000-8000-000000000003',
    'b2000004-0000-4000-8000-000000000004',
  ];

  /**
   * Demo article UUIDs.
   *
   * @var list<string>
   */
  private const ARTICLE_UUIDS = [
    'b2000005-0000-4000-8000-000000000001',
    'b2000005-0000-4000-8000-000000000002',
    'b2000005-0000-4000-8000-000000000003',
  ];

  /**
   * Demo market study UUIDs.
   *
   * @var list<string>
   */
  private const MARKET_STUDY_UUIDS = [
    'b2000006-0000-4000-8000-000000000001',
    'b2000006-0000-4000-8000-000000000002',
    'b2000006-0000-4000-8000-000000000003',
  ];

  /**
   * Demo taxonomy term UUIDs (news + market study categories).
   *
   * @var list<string>
   */
  private const TAXONOMY_TERM_UUIDS = [
    'b3000001-0000-4000-8000-000000000001',
    'b3000001-0000-4000-8000-000000000002',
    'b3000002-0000-4000-8000-000000000001',
    'b3000002-0000-4000-8000-000000000002',
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityRepositoryInterface $entityRepository,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly DemoTranslationOverlayLoader $overlayLoader,
  ) {}

  /**
   * Syncs menu links, FAQ nodes and homepage for all enabled languages.
   */
  public function sync(): void {
    $this->syncMenuLinkTranslations();
    $this->syncTaxonomyTerms();
    $this->syncFaqItemNodes();
    $this->syncArticleNodes();
    $this->syncMarketStudyNodes();
    $this->syncHomepageNode();
  }

  /**
   * Returns homepage URL aliases for each enabled site language.
   *
   * @return array<string, string>
   *   Aliases without leading slash, keyed by langcode.
   */
  public function homepageAliasesForEnabledLanguages(NodeInterface $homepage): array {
    $aliases = [];
    foreach ($this->enabledLanguageIds() as $langcode) {
      $alias = $this->resolveHomepageAlias($homepage, $langcode);
      if ($alias !== NULL) {
        $aliases[$langcode] = $alias;
      }
    }
    return $aliases;
  }

  /**
   * Ensures every menu link has translations for enabled languages.
   */
  private function syncMenuLinkTranslations(): void {
    $storage = $this->entityTypeManager->getStorage('menu_link_content');
    $ids = $storage->getQuery()->accessCheck(FALSE)->execute();
    if ($ids === []) {
      return;
    }

    foreach ($storage->loadMultiple($ids) as $entity) {
      if (!$entity instanceof ContentEntityInterface) {
        continue;
      }
      $this->ensureEntityTranslations($entity);
      foreach ($this->enabledLanguageIds() as $langcode) {
        $this->applyMenuLinkOverlay($entity, $langcode);
      }
      $entity->save();
    }
  }

  /**
   * Applies menu link title overlay when defined for a language.
   */
  private function applyMenuLinkOverlay(ContentEntityInterface $entity, string $langcode): void {
    if (!$entity->hasTranslation($langcode)) {
      return;
    }

    $title = $this->overlayLoader->menuLinkTitle($entity->uuid(), $langcode);
    if ($title === NULL) {
      return;
    }

    $translation = $entity->getTranslation($langcode);
    $translation->set('title', $title);
  }

  /**
   * Ensures demo FAQ nodes have translations and localized overlays.
   */
  private function syncFaqItemNodes(): void {
    $this->syncNodeOverlayBatch(self::FAQ_UUIDS, function (NodeInterface $node, string $langcode): void {
      $this->applyFaqTranslationOverlay($node, $langcode);
    });
  }

  /**
   * Ensures demo article nodes have translations and localized overlays.
   */
  private function syncArticleNodes(): void {
    $this->syncNodeOverlayBatch(self::ARTICLE_UUIDS, function (NodeInterface $node, string $langcode): void {
      $this->applyArticleTranslationOverlay($node, $langcode);
    });
  }

  /**
   * Ensures demo market study nodes have translations and localized overlays.
   */
  private function syncMarketStudyNodes(): void {
    $this->syncNodeOverlayBatch(self::MARKET_STUDY_UUIDS, function (NodeInterface $node, string $langcode): void {
      $this->applyMarketStudyTranslationOverlay($node, $langcode);
    });
  }

  /**
   * Ensures demo taxonomy terms have translations and localized overlays.
   */
  private function syncTaxonomyTerms(): void {
    $storage = $this->entityTypeManager->getStorage('taxonomy_term');

    foreach (self::TAXONOMY_TERM_UUIDS as $uuid) {
      try {
        $term = $this->entityRepository->loadEntityByUuid('taxonomy_term', $uuid);
      }
      catch (\Exception) {
        continue;
      }

      if (!$term instanceof ContentEntityInterface) {
        continue;
      }

      $editable = $storage->load($term->id());
      if (!$editable instanceof ContentEntityInterface) {
        continue;
      }

      $this->ensureEntityTranslations($editable);
      foreach ($this->enabledLanguageIds() as $langcode) {
        $this->applyTaxonomyTermOverlay($editable, $langcode);
      }
      $editable->save();
    }
  }

  /**
   * @param list<string> $uuids
   */
  private function syncNodeOverlayBatch(array $uuids, callable $applyOverlay): void {
    $storage = $this->entityTypeManager->getStorage('node');

    foreach ($uuids as $uuid) {
      try {
        $node = $this->entityRepository->loadEntityByUuid('node', $uuid);
      }
      catch (\Exception) {
        continue;
      }

      if (!$node instanceof NodeInterface) {
        continue;
      }

      $editable = $storage->load($node->id());
      if (!$editable instanceof NodeInterface) {
        continue;
      }

      $this->ensureEntityTranslations($editable);
      foreach ($this->enabledLanguageIds() as $langcode) {
        $applyOverlay($editable, $langcode);
      }
      $editable->save();
    }
  }

  /**
   * Ensures the homepage node has translations and configured titles.
   */
  private function syncHomepageNode(): void {
    $uuid = (string) ($this->configFactory->get('ps_demo.settings')->get('homepage_uuid') ?? '');
    if ($uuid === '') {
      return;
    }

    try {
      $node = $this->entityRepository->loadEntityByUuid('node', $uuid);
    }
    catch (\Exception) {
      $node = NULL;
    }

    if (!$node instanceof NodeInterface) {
      return;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $editable = $storage->load($node->id());
    if (!$editable instanceof NodeInterface) {
      return;
    }

    $this->ensureEntityTranslations($editable);

    $titles = $this->configFactory->get('ps_demo.homepage')->get('node.titles');
    $paths = $this->configFactory->get('ps_demo.homepage')->get('node.path');
    if (is_array($titles)) {
      foreach ($this->enabledLanguageIds() as $langcode) {
        if (!isset($titles[$langcode]) || !is_string($titles[$langcode])) {
          continue;
        }
        if ($editable->hasTranslation($langcode)) {
          $editable->getTranslation($langcode)->setTitle($titles[$langcode]);
        }
      }
    }

    if (is_array($paths)) {
      foreach ($this->enabledLanguageIds() as $langcode) {
        if (!isset($paths[$langcode]) || !is_string($paths[$langcode])) {
          continue;
        }
        if (!$editable->hasTranslation($langcode)) {
          continue;
        }
        $translation = $editable->getTranslation($langcode);
        if ($translation->hasField('path')) {
          $translation->set('path', [
            'alias' => '/' . ltrim($paths[$langcode], '/'),
            'langcode' => $langcode,
          ]);
        }
      }
    }

    $editable->save();
  }

  /**
   * Applies FAQ field overlays when defined for a language.
   */
  private function applyFaqTranslationOverlay(NodeInterface $node, string $langcode): void {
    if (!$node->hasTranslation($langcode)) {
      return;
    }

    $overlay = $this->overlayLoader->faqItemOverlay($node->uuid(), $langcode);
    if ($overlay === []) {
      return;
    }

    $translation = $node->getTranslation($langcode);
    if (isset($overlay['title']) && is_string($overlay['title'])) {
      $translation->setTitle($overlay['title']);
    }
    if (isset($overlay['field_question']) && is_string($overlay['field_question'])) {
      $translation->set('field_question', $overlay['field_question']);
    }
    if (isset($overlay['field_answer']) && is_array($overlay['field_answer'])) {
      $value = $overlay['field_answer']['value'] ?? '';
      $format = $overlay['field_answer']['format'] ?? 'basic_html';
      if (is_string($value) && is_string($format)) {
        $translation->set('field_answer', [
          'value' => $value,
          'format' => $format,
        ]);
      }
    }
  }

  /**
   * Applies article field overlays when defined for a language.
   */
  private function applyArticleTranslationOverlay(NodeInterface $node, string $langcode): void {
    if (!$node->hasTranslation($langcode)) {
      return;
    }

    $overlay = $this->overlayLoader->articleOverlay($node->uuid(), $langcode);
    if ($overlay === []) {
      return;
    }

    $this->applyTitleAndBodyOverlay($node->getTranslation($langcode), $overlay);
  }

  /**
   * Applies market study field overlays when defined for a language.
   */
  private function applyMarketStudyTranslationOverlay(NodeInterface $node, string $langcode): void {
    if (!$node->hasTranslation($langcode)) {
      return;
    }

    $overlay = $this->overlayLoader->marketStudyOverlay($node->uuid(), $langcode);
    if ($overlay === []) {
      return;
    }

    $this->applyTitleAndBodyOverlay($node->getTranslation($langcode), $overlay);
  }

  /**
   * Applies taxonomy term name overlays when defined for a language.
   */
  private function applyTaxonomyTermOverlay(ContentEntityInterface $term, string $langcode): void {
    if (!$term->hasTranslation($langcode)) {
      return;
    }

    $name = $this->overlayLoader->taxonomyTermName($term->uuid(), $langcode);
    if ($name === NULL) {
      return;
    }

    $translation = $term->getTranslation($langcode);
    if ($translation->hasField('name')) {
      $translation->set('name', $name);
    }
  }

  /**
   * @param array{title?: string, body?: array{value: string, format: string}} $overlay
   */
  private function applyTitleAndBodyOverlay(ContentEntityInterface $entity, array $overlay): void {
    if ($entity instanceof NodeInterface && isset($overlay['title']) && is_string($overlay['title'])) {
      $entity->setTitle($overlay['title']);
    }
    if (isset($overlay['body']) && is_array($overlay['body']) && $entity->hasField('body')) {
      $value = $overlay['body']['value'] ?? '';
      $format = $overlay['body']['format'] ?? 'basic_html';
      if (is_string($value) && is_string($format)) {
        $entity->set('body', [
          'value' => $value,
          'format' => $format,
        ]);
      }
    }
  }

  /**
   * Adds missing entity translations from the best source language.
   */
  private function ensureEntityTranslations(ContentEntityInterface $entity): void {
    foreach ($this->enabledLanguageIds() as $langcode) {
      if ($entity->hasTranslation($langcode)) {
        continue;
      }
      $sourceLangcode = $this->sourceLangcodeFor($entity, $langcode);
      if ($sourceLangcode === '') {
        continue;
      }
      $entity->addTranslation($langcode, $entity->getTranslation($sourceLangcode)->toArray());
    }
  }

  /**
   * Returns enabled language IDs for the current site.
   *
   * @return list<string>
   *   Langcodes.
   */
  private function enabledLanguageIds(): array {
    return array_keys($this->languageManager->getLanguages());
  }

  /**
   * Picks the best existing translation to copy for a target language.
   */
  private function sourceLangcodeFor(ContentEntityInterface $entity, string $targetLangcode): string {
    if ($targetLangcode === 'lb' && $entity->hasTranslation('fr')) {
      return 'fr';
    }

    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    $candidates = array_unique([
      $targetLangcode,
      $defaultLangcode,
      'en',
      'fr',
      $entity->language()->getId(),
    ]);

    foreach ($candidates as $candidate) {
      if ($entity->hasTranslation($candidate)) {
        return $candidate;
      }
    }

    foreach ($entity->getTranslationLanguages() as $langcode => $_language) {
      return $langcode;
    }

    return '';
  }

  /**
   * Resolves homepage alias: config override, then node path, then EN fallback.
   */
  private function resolveHomepageAlias(NodeInterface $homepage, string $langcode): ?string {
    $paths = $this->configFactory->get('ps_demo.homepage')->get('node.path');
    if (is_array($paths) && isset($paths[$langcode]) && is_string($paths[$langcode])) {
      return ltrim($paths[$langcode], '/');
    }

    if ($homepage->hasTranslation($langcode)) {
      $alias = $homepage->getTranslation($langcode)->get('path')->alias;
      if (is_string($alias) && $alias !== '') {
        return ltrim($alias, '/');
      }
    }

    if (is_array($paths) && isset($paths['en']) && is_string($paths['en'])) {
      return ltrim($paths['en'], '/');
    }

    return NULL;
  }

}
