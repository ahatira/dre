<?php

/**
 * @file
 * Audits demo/homepage translations for the current Drush URI site.
 *
 * Usage: drush php:script scripts/tools/audit_demo_translations.php --uri=...
 */

use Drupal\ps_homepage\Service\HomepageBlockDefaultsLoader;

$lm = \Drupal::languageManager();
$defaultLang = $lm->getDefaultLanguage()->getId();
$enabledLangs = array_keys($lm->getLanguages());

$plugins = [
  'ps_homepage_search_hero_block',
  'ps_content_services_grid_block',
  'ps_content_outils_accordion_block',
  'ps_offer_offers_carousel_block',
  'ps_search_search_shortcuts_block',
  'ps_content_experts_accompagnement_block',
];

$loader = \Drupal::service('ps_homepage.block_defaults_loader');
if (!$loader instanceof HomepageBlockDefaultsLoader) {
  echo "WARN: unexpected loader service\n";
}

echo "SITE default={$defaultLang} langs=" . implode(',', $enabledLangs) . "\n";
echo 'front=' . (string) \Drupal::config('system.site')->get('page.front') . "\n";

$uuid = (string) (\Drupal::config('ps_demo.settings')->get('homepage_uuid') ?? '');
$homepage = NULL;
if ($uuid !== '') {
  try {
    $homepage = \Drupal::service('entity.repository')->loadEntityByUuid('node', $uuid);
  }
  catch (\Exception) {
    $homepage = NULL;
  }
}

if (!$homepage) {
  echo "FAIL: homepage node missing (uuid={$uuid})\n";
}
else {
  echo "homepage nid={$homepage->id()} translations=" . implode(',', array_keys($homepage->getTranslationLanguages())) . "\n";
  foreach ($enabledLangs as $lang) {
    if (!$homepage->hasTranslation($lang)) {
      echo "FAIL: homepage missing translation {$lang}\n";
      continue;
    }
    $t = $homepage->getTranslation($lang);
    $sections = $t->hasField('layout_builder__layout')
      ? count($t->get('layout_builder__layout')->getSections())
      : 0;
    echo "  homepage[{$lang}] title={$t->label()} lb_sections={$sections}\n";
  }
}

foreach ($enabledLangs as $lang) {
  foreach ($plugins as $pluginId) {
    $cfg = $loader->forPlugin($pluginId, $lang);
    if ($cfg === []) {
      echo "FAIL: block_defaults {$pluginId} empty for {$lang}\n";
      continue;
    }
    $hasItems = isset($cfg['items']) && is_array($cfg['items']) && $cfg['items'] !== [];
    $hasSteps = isset($cfg['steps']) && is_array($cfg['steps']) && $cfg['steps'] !== [];
    if ($pluginId === 'ps_homepage_search_hero_block') {
      $title = (string) ($cfg['title'] ?? '');
      if ($title === '' || $title === 'What are you looking for?') {
        echo "WARN: hero title EN fallback for {$lang}: {$title}\n";
      }
    }
    if (str_contains($pluginId, 'services_grid') && !$hasItems) {
      echo "WARN: services_grid missing items for {$lang}\n";
    }
    if (str_contains($pluginId, 'outils_accordion') && !$hasItems) {
      echo "WARN: outils missing items for {$lang}\n";
    }
    if (str_contains($pluginId, 'search_shortcuts') && !$hasItems) {
      echo "WARN: shortcuts missing items for {$lang}\n";
    }
    if (str_contains($pluginId, 'experts') && !$hasSteps) {
      echo "WARN: experts missing steps for {$lang}\n";
    }
  }
}

$menuStorage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
$ids = $menuStorage->getQuery()->accessCheck(FALSE)->execute();
$missingMenu = [];
$enMenuOnDefault = 0;
foreach ($menuStorage->loadMultiple($ids) as $entity) {
  foreach ($enabledLangs as $lang) {
    if (!$entity->hasTranslation($lang)) {
      $missingMenu[$lang] = ($missingMenu[$lang] ?? 0) + 1;
    }
  }
  if ($defaultLang !== 'en' && $entity->hasTranslation($defaultLang)) {
    $enTitle = $entity->hasTranslation('en') ? (string) $entity->getTranslation('en')->getTitle() : '';
    $defTitle = (string) $entity->getTranslation($defaultLang)->getTitle();
    if ($enTitle !== '' && $defTitle === $enTitle) {
      $enMenuOnDefault++;
    }
  }
}

foreach ($enabledLangs as $lang) {
  $n = $missingMenu[$lang] ?? 0;
  if ($n > 0) {
    echo "FAIL: menu links missing translation {$lang}: {$n}\n";
  }
}
if ($enMenuOnDefault > 0 && $defaultLang !== 'en') {
  echo "WARN: {$enMenuOnDefault} menu links still EN title on default lang {$defaultLang}\n";
}

$overlayLoader = new \Drupal\ps_demo\Service\DemoTranslationOverlayLoader(
  \Drupal::service('extension.path.resolver'),
);
$overlayUuids = [
  'a1000001-0000-4000-8000-000000000101',
  'a1000001-0000-4000-8000-000000000104',
  'a1000001-0000-4000-8000-000000000106',
];
foreach ($overlayUuids as $menuUuid) {
  foreach ($enabledLangs as $lang) {
    $expected = $overlayLoader->menuLinkTitle($menuUuid, $lang);
    if ($expected === NULL) {
      continue;
    }
    $sample = $menuStorage->loadByProperties(['uuid' => $menuUuid]);
    if ($sample === []) {
      continue;
    }
    $entity = reset($sample);
    if (!$entity->hasTranslation($lang)) {
      echo "FAIL: menu overlay uuid={$menuUuid} missing translation {$lang}\n";
      continue;
    }
    $actual = (string) $entity->getTranslation($lang)->label();
    if ($actual !== $expected && $lang === $defaultLang) {
      echo "WARN: menu overlay uuid={$menuUuid} [{$lang}] expected={$expected} actual={$actual}\n";
    }
  }
}

$faqUuids = [
  'b2000004-0000-4000-8000-000000000001',
  'b2000004-0000-4000-8000-000000000002',
  'b2000004-0000-4000-8000-000000000003',
  'b2000004-0000-4000-8000-000000000004',
];
$faqStorage = \Drupal::entityTypeManager()->getStorage('node');
foreach ($faqUuids as $faqUuid) {
  try {
    $faqNode = \Drupal::service('entity.repository')->loadEntityByUuid('node', $faqUuid);
  }
  catch (\Exception) {
    $faqNode = NULL;
  }
  if (!$faqNode) {
    echo "FAIL: FAQ node missing uuid={$faqUuid}\n";
    continue;
  }
  $faqLangs = array_keys($faqNode->getTranslationLanguages());
  echo "faq[{$faqUuid}] nid={$faqNode->id()} translations=" . implode(',', $faqLangs) . "\n";
  foreach ($enabledLangs as $lang) {
    if (!$faqNode->hasTranslation($lang)) {
      echo "FAIL: FAQ {$faqUuid} missing translation {$lang}\n";
      continue;
    }
    $q = (string) $faqNode->getTranslation($lang)->get('field_question')->value;
    if ($defaultLang !== 'en' && $lang === $defaultLang && str_starts_with($q, 'How ')) {
      echo "WARN: FAQ {$faqUuid} default lang {$lang} still EN question: {$q}\n";
    }
  }
}

$sampleUuid = 'a1000001-0000-4000-8000-000000000101';
$sample = $menuStorage->loadByProperties(['uuid' => $sampleUuid]);
if ($sample) {
  $e = reset($sample);
  foreach ($enabledLangs as $lang) {
  if ($e->hasTranslation($lang)) {
    echo "sample_main[{$lang}]=" . $e->getTranslation($lang)->label() . "\n";
  }
  }
}

echo "DONE\n";
