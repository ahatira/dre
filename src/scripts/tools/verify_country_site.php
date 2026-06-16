<?php

/**
 * @file
 * Verifies a Property Search country site is clean and complete.
 *
 * Usage:
 *   drush php:script scripts/tools/verify_country_site.php shell fr
 *   drush php:script scripts/tools/verify_country_site.php demo es
 *   drush php:script scripts/tools/verify_country_site.php full it
 */

declare(strict_types=1);

/**
 * Prints a verification line.
 */
function verify_line(string $status, string $message): void {
  echo strtoupper($status) . ": {$message}\n";
}

/**
 * @param list<string> $failures
 */
function verify_fail(array &$failures, string $message): void {
  $failures[] = $message;
  verify_line('fail', $message);
}

/**
 * @param list<string> $warnings
 */
function verify_warn(array &$warnings, string $message): void {
  $warnings[] = $message;
  verify_line('warn', $message);
}

function verify_pass(string $message): void {
  verify_line('ok', $message);
}

$mode = isset($extra[0]) ? strtolower((string) $extra[0]) : '';
$country = isset($extra[1]) ? strtolower((string) $extra[1]) : '';

$valid_modes = ['shell', 'demo', 'full'];
if (!in_array($mode, $valid_modes, TRUE)) {
  echo "Usage: drush php:script scripts/tools/verify_country_site.php <shell|demo|full> <country>\n";
  exit(2);
}

$expected_matrix = [
  'com' => ['default' => 'en', 'langs' => ['en', 'fr']],
  'fr' => ['default' => 'fr', 'langs' => ['fr', 'en']],
  'be' => ['default' => 'fr', 'langs' => ['en', 'fr', 'nl']],
  'es' => ['default' => 'es', 'langs' => ['en', 'es']],
  'ie' => ['default' => 'en', 'langs' => ['en']],
  'it' => ['default' => 'it', 'langs' => ['en', 'it']],
  'lu' => ['default' => 'fr', 'langs' => ['en', 'fr', 'lb']],
  'nl' => ['default' => 'nl', 'langs' => ['en', 'nl']],
  'pl' => ['default' => 'pl', 'langs' => ['en', 'pl']],
];

if ($country === '' || !isset($expected_matrix[$country])) {
  verify_line('fail', 'Invalid or missing country. Use: com, fr, be, es, ie, it, lu, nl, pl');
  exit(2);
}

$expected = $expected_matrix[$country];
$failures = [];
$warnings = [];

$bootstrap = \Drupal::hasContainer();
if (!$bootstrap) {
  verify_fail($failures, 'Drupal bootstrap failed');
  echo "SUMMARY mode={$mode} country={$country} fail=" . count($failures) . " warn=0\n";
  exit(1);
}
verify_pass('Drupal bootstrap OK');

$lm = \Drupal::languageManager();
$defaultLang = $lm->getDefaultLanguage()->getId();
$enabledLangs = array_keys($lm->getLanguages());

if ($defaultLang !== $expected['default']) {
  verify_fail($failures, "default_langcode={$defaultLang}, expected {$expected['default']}");
}
else {
  verify_pass("default_langcode={$defaultLang}");
}

$expectedLangs = $expected['langs'];
$missingLangs = array_diff($expectedLangs, $enabledLangs);
$extraLangs = array_diff($enabledLangs, $expectedLangs);
if ($missingLangs !== []) {
  verify_fail($failures, 'Missing languages: ' . implode(',', $missingLangs));
}
if ($extraLangs !== []) {
  verify_warn($warnings, 'Extra languages: ' . implode(',', $extraLangs));
}
if ($missingLangs === [] && $extraLangs === []) {
  verify_pass('languages=' . implode(',', $enabledLangs));
}

$frontTheme = (string) \Drupal::config('system.theme')->get('default');
if ($frontTheme !== 'ps_theme') {
  verify_fail($failures, "Front theme={$frontTheme}, expected ps_theme");
}
else {
  verify_pass('Front theme ps_theme');
}

$shell_modules = [
  'ps_core',
  'ps_dictionary',
  'ps_offer',
  'ps_search',
  'ps_homepage',
  'ps_block',
];
$shell_ok = TRUE;
foreach ($shell_modules as $module) {
  if (!\Drupal::moduleHandler()->moduleExists($module)) {
    verify_fail($failures, "Required shell module missing: {$module}");
    $shell_ok = FALSE;
  }
}
if ($shell_ok) {
  verify_pass('Shell PS modules enabled');
}

$homepageUuid = (string) (\Drupal::config('ps_demo.settings')->get('homepage_uuid') ?? 'b2000001-0000-4000-8000-000000000001');
$homepage = NULL;
try {
  $homepage = \Drupal::service('entity.repository')->loadEntityByUuid('node', $homepageUuid);
}
catch (\Exception) {
  $homepage = NULL;
}

$menuStorage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
$demoMenuCount = (int) $menuStorage->getQuery()
  ->accessCheck(FALSE)
  ->condition('uuid', 'a1000001-0000-4000-8000-000000000101', '=')
  ->count()
  ->execute();

$frontPage = (string) \Drupal::config('system.site')->get('page.front');
$offerCount = (int) \Drupal::entityTypeManager()->getStorage('node')->getQuery()
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->condition('status', 1)
  ->count()
  ->execute();

if ($mode === 'shell') {
  if (!\Drupal::moduleHandler()->moduleExists('ps_demo')) {
    verify_fail($failures, 'ps_demo must not be enabled in shell mode');
  }
  else {
    verify_pass('ps_demo not enabled');
  }

  if ($homepage !== NULL) {
    verify_fail($failures, "Demo homepage node present (nid={$homepage->id()}) — run purge or reinstall --minimal");
  }
  else {
    verify_pass('No demo homepage node');
  }

  if ($demoMenuCount > 0) {
    verify_fail($failures, 'Demo main menu link (Find a property) present — stellar content not clean');
  }
  else {
    verify_pass('No demo stellar menu links');
  }

  if ($frontPage !== '' && preg_match('/^\/node\/\d+$/', $frontPage) && $homepage !== NULL) {
    verify_fail($failures, "Front page {$frontPage} points to demo homepage");
  }
  else {
    verify_pass("Front page={$frontPage} (no demo homepage binding)");
  }

  if ($offerCount > 0) {
    verify_warn($warnings, "Published offers={$offerCount} (expected 0 for pure shell — OK if sample XML imported)");
  }
  else {
    verify_pass('No published offers');
  }
}

if ($mode === 'demo' || $mode === 'full') {
  if (!\Drupal::moduleHandler()->moduleExists('ps_demo')) {
    verify_fail($failures, 'ps_demo not enabled — run: make demo');
  }
  else {
    verify_pass('ps_demo enabled');
  }

  if (!\Drupal::moduleHandler()->moduleExists('ps_favorite')) {
    verify_warn($warnings, 'ps_favorite not enabled (dependency of ps_demo — check module enable)');
  }
  else {
    verify_pass('ps_favorite enabled');
  }

  if (!$homepage) {
    verify_fail($failures, "Demo homepage missing (uuid={$homepageUuid})");
  }
  else {
    verify_pass("Demo homepage nid={$homepage->id()}");
    $expectedFront = '/node/' . $homepage->id();
    if ($frontPage !== $expectedFront) {
      verify_fail($failures, "Front page={$frontPage}, expected {$expectedFront}");
    }
    else {
      verify_pass("Front page={$frontPage}");
    }

    $homepageLangs = array_keys($homepage->getTranslationLanguages());
    $missingHomepageLangs = array_diff($enabledLangs, $homepageLangs);
    if ($missingHomepageLangs !== []) {
      verify_fail($failures, 'Homepage missing translations: ' . implode(',', $missingHomepageLangs));
    }
    else {
      verify_pass('Homepage has all enabled language translations');
    }

    if ($homepage->hasField('layout_builder__layout')) {
      $sections = count($homepage->get('layout_builder__layout')->getSections());
      if ($sections < 9) {
        verify_fail($failures, "Homepage LB sections={$sections}, expected 9");
      }
      else {
        verify_pass("Homepage LB sections={$sections}");
      }
    }
  }

  if ($demoMenuCount === 0) {
    verify_fail($failures, 'Demo main menu link missing — run: make demo');
  }
  else {
    verify_pass('Demo stellar menu present');
  }

  $faqUuids = [
    'b2000004-0000-4000-8000-000000000001',
    'b2000004-0000-4000-8000-000000000002',
    'b2000004-0000-4000-8000-000000000003',
    'b2000004-0000-4000-8000-000000000004',
  ];
  $faq_ok = TRUE;
  foreach ($faqUuids as $faqUuid) {
    try {
      $faq = \Drupal::service('entity.repository')->loadEntityByUuid('node', $faqUuid);
    }
    catch (\Exception) {
      $faq = NULL;
    }
    if (!$faq) {
      verify_fail($failures, "FAQ missing uuid={$faqUuid}");
      $faq_ok = FALSE;
      continue;
    }
    $faqLangs = array_keys($faq->getTranslationLanguages());
    $missingFaqLangs = array_diff($enabledLangs, $faqLangs);
    if ($missingFaqLangs !== []) {
      verify_fail($failures, "FAQ {$faqUuid} missing langs: " . implode(',', $missingFaqLangs));
      $faq_ok = FALSE;
    }
  }
  if ($faq_ok) {
    verify_pass('FAQ demo nodes present with all language translations');
  }

  try {
    $hero = \Drupal::service('entity.repository')->loadEntityByUuid('media', 'c1000002-0000-4000-8000-000000000001');
  }
  catch (\Exception) {
    $hero = NULL;
  }
  if (!$hero) {
    verify_warn($warnings, 'Hero demo media missing (uuid=c1000002-...)');
  }
  else {
    verify_pass('Hero demo media present');
  }

  $sampleMenu = $menuStorage->loadByProperties(['uuid' => 'a1000001-0000-4000-8000-000000000101']);
  if ($sampleMenu) {
    $sample = reset($sampleMenu);
    $defLang = $expected['default'];
    if ($sample->hasTranslation($defLang)) {
      $title = (string) $sample->getTranslation($defLang)->label();
      if ($defLang !== 'en' && str_starts_with($title, 'Find ')) {
        verify_warn($warnings, "Main menu [{$defLang}] still EN: {$title}");
      }
      else {
        verify_pass("Main menu [{$defLang}]={$title}");
      }
    }
  }
}

if ($mode === 'full') {
  if (!\Drupal::moduleHandler()->moduleExists('ps_migrate')) {
    verify_fail($failures, 'ps_migrate not enabled — run: make post-install');
  }
  else {
    verify_pass('ps_migrate enabled');
  }

  if ($offerCount <= 0) {
    verify_fail($failures, 'No published offers — run: make post-install or import sample XML');
  }
  else {
    verify_pass("Published offers={$offerCount}");
  }

  $indexed = 0;
  if (\Drupal::moduleHandler()->moduleExists('search_api')) {
    $indexStorage = \Drupal::entityTypeManager()->getStorage('search_api_index');
    $index = $indexStorage->load('offers');
    if ($index) {
      $indexed = (int) $index->indexedCount();
    }
  }
  if ($indexed <= 0) {
    verify_warn($warnings, 'Solr offers index empty — run: make index-solr');
  }
  else {
    verify_pass("Solr offers indexed={$indexed}");
  }
}

echo "SUMMARY mode={$mode} country={$country} fail=" . count($failures) . ' warn=' . count($warnings) . "\n";
exit($failures !== [] ? 1 : 0);
