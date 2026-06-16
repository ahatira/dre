#!/usr/bin/env php
<?php

/**
 * @file
 * Builds a per-country CRM sample XML (~50 offers: 10 per asset type).
 *
 * Usage:
 *   php scripts/tools/generate_country_sample_xml.php fr
 *   php scripts/tools/generate_country_sample_xml.php all
 */

declare(strict_types=1);

const SOURCE_XML = 'data/xml/bnppre_sample_50_per_type.xml';
const OFFERS_PER_TYPE = 10;
const ASSET_TYPES = ['BUR', 'ACT', 'COM', 'ENT', 'TER'];

/** @var array<string, string> */
const COUNTRY_ISO = [
  'com' => 'FR',
  'fr' => 'FR',
  'be' => 'BE',
  'es' => 'ES',
  'ie' => 'GB',
  'it' => 'IT',
  'lu' => 'LU',
  'nl' => 'NL',
  'pl' => 'PL',
];

/** @var array<string, list<string>> Drupal langcode => XML LANGUAGE codes */
const COUNTRY_XML_LANGUAGES = [
  'com' => ['EN', 'FR'],
  'fr' => ['FR', 'EN'],
  'be' => ['FR', 'EN', 'NL'],
  'es' => ['ES', 'EN'],
  'ie' => ['EN'],
  'it' => ['IT', 'EN'],
  'lu' => ['FR', 'EN', 'LB'],
  'nl' => ['NL', 'EN'],
  'pl' => ['PL', 'EN'],
];

/** @var array<string, int> */
const COUNTRY_ID_OFFSET = [
  'com' => 1000000,
  'fr' => 2000000,
  'be' => 3000000,
  'es' => 4000000,
  'ie' => 5000000,
  'it' => 6000000,
  'lu' => 7000000,
  'nl' => 8000000,
  'pl' => 9000000,
];

function project_root(): string {
  return dirname(__DIR__, 3);
}

function usage(): void {
  fwrite(STDERR, "Usage: php scripts/tools/generate_country_sample_xml.php <country|all>\n");
  fwrite(STDERR, "Countries: com, be, es, fr, ie, it, lu, nl, pl\n");
}

function xml_lang_for_drupal(string $drupalLang): string {
  return match ($drupalLang) {
    'lb' => 'LB',
    default => strtoupper($drupalLang),
  };
}

/**
 * @return list<string>
 */
function country_codes(string $arg): array {
  if ($arg === 'all') {
    return array_keys(COUNTRY_ISO);
  }
  $arg = strtolower($arg);
  if (!isset(COUNTRY_ISO[$arg])) {
    fwrite(STDERR, "Unknown country: {$arg}\n");
    exit(1);
  }
  return [$arg];
}

/**
 * @return array<string, list<DOMElement>>
 */
function bucket_offers_by_type(DOMDocument $doc): array {
  $buckets = array_fill_keys(ASSET_TYPES, []);
  $xpath = new DOMXPath($doc);
  /** @var DOMElement $offer */
  foreach ($xpath->query('/OFFERS_LIST/OFFER') ?: [] as $offer) {
    $typeNodes = $xpath->query('./TYPE_CODE', $offer);
    if ($typeNodes === FALSE || $typeNodes->length === 0) {
      continue;
    }
    $type = trim($typeNodes->item(0)?->textContent ?? '');
    if ($type === '' || !isset($buckets[$type])) {
      continue;
    }
    if (count($buckets[$type]) >= OFFERS_PER_TYPE) {
      continue;
    }
    $buckets[$type][] = $offer;
  }
  return $buckets;
}

function ensure_multilingual_nodes(DOMDocument $doc, DOMElement $offer, array $xmlLanguages): void {
  $xpath = new DOMXPath($doc);
  foreach (['ML_DESCRIPTION_1', 'ML_DESCRIPTION_2', 'ML_DESCRIPTION_4', 'ML_AVAILABILITY'] as $containerName) {
  /** @var DOMElement|null $container */
    $container = $xpath->query("./{$containerName}", $offer)?->item(0);
    if (!$container instanceof DOMElement) {
      continue;
    }
    $childTag = $containerName === 'ML_AVAILABILITY' ? 'AVAILABILITY' : 'DESCRIPTION';
    /** @var array<string, string> $existing */
    $existing = [];
    foreach ($xpath->query("./{$childTag}", $container) ?: [] as $node) {
      if (!$node instanceof DOMElement) {
        continue;
      }
      $lang = strtoupper(trim($node->getAttribute('LANGUAGE')));
      if ($lang !== '') {
        $existing[$lang] = $node->textContent ?? '';
      }
    }
    foreach ($xmlLanguages as $xmlLang) {
      if (isset($existing[$xmlLang])) {
        continue;
      }
      $source = $existing[$xmlLang] ?? $existing['EN'] ?? $existing['FR'] ?? reset($existing) ?: 'Sample offer description.';
      $element = $doc->createElement($childTag);
      $element->setAttribute('LANGUAGE', $xmlLang);
      $element->appendChild($doc->createTextNode($source));
      $container->appendChild($element);
    }
  }
}

function remap_offer(DOMDocument $doc, DOMElement $offer, string $country, int $sequence): void {
  $iso = COUNTRY_ISO[$country];
  $offset = COUNTRY_ID_OFFSET[$country];
  $xpath = new DOMXPath($doc);

  $businessId = $offset + $sequence;
  foreach ($xpath->query('./BUSINESS_ID', $offer) ?: [] as $node) {
    $node->textContent = (string) $businessId;
  }

  foreach ($xpath->query('./COUNTRY', $offer) ?: [] as $node) {
    $node->textContent = $iso;
  }

  foreach ($xpath->query('./TECHNICAL_ID', $offer) ?: [] as $node) {
    $technical = strtoupper($country) . '-' . trim($node->textContent ?? 'OFFER') . '-' . $sequence;
    $node->textContent = $technical;
  }

  foreach ($xpath->query('./ADDRESS_LIST/ADDRESS/COUNTRY_ISO', $offer) ?: [] as $node) {
    $node->textContent = $iso;
  }

  ensure_multilingual_nodes($doc, $offer, COUNTRY_XML_LANGUAGES[$country]);
}

function generate_for_country(string $country, DOMDocument $sourceDoc): string {
  $buckets = bucket_offers_by_type($sourceDoc);
  $output = new DOMDocument('1.0', 'UTF-8');
  $output->formatOutput = TRUE;
  $root = $output->createElement('OFFERS_LIST');
  $output->appendChild($root);

  $sequence = 1;
  foreach (ASSET_TYPES as $type) {
    foreach ($buckets[$type] as $sourceOffer) {
      $imported = $output->importNode($sourceOffer, TRUE);
      if (!$imported instanceof DOMElement) {
        continue;
      }
      remap_offer($output, $imported, $country, $sequence);
      $root->appendChild($imported);
      $sequence++;
    }
  }

  $outDir = project_root() . '/data/xml/samples/' . $country;
  if (!is_dir($outDir) && !mkdir($outDir, 0775, TRUE) && !is_dir($outDir)) {
    throw new RuntimeException("Cannot create directory: {$outDir}");
  }
  $target = $outDir . '/offers.xml';
  $output->save($target);
  return $target;
}

$args = array_slice($argv, 1);
if ($args === [] || in_array('-h', $args, TRUE) || in_array('--help', $args, TRUE)) {
  usage();
  exit($args === [] ? 1 : 0);
}

if (in_array('all', $args, TRUE)) {
  $countries = array_keys(COUNTRY_ISO);
}
else {
  $countries = [];
  foreach ($args as $arg) {
    $countries = array_merge($countries, country_codes($arg));
  }
}

$sourcePath = project_root() . '/' . SOURCE_XML;
if (!is_readable($sourcePath)) {
  fwrite(STDERR, "Source XML not found: {$sourcePath}\n");
  exit(1);
}

$sourceDoc = new DOMDocument();
$sourceDoc->preserveWhiteSpace = FALSE;
$sourceDoc->formatOutput = TRUE;
if (!$sourceDoc->load($sourcePath)) {
  fwrite(STDERR, "Failed to parse source XML.\n");
  exit(1);
}

foreach ($countries as $country) {
  $target = generate_for_country($country, $sourceDoc);
  $count = (new DOMXPath((function () use ($target) {
    $doc = new DOMDocument();
    $doc->load($target);
    return $doc;
  })()))->query('/OFFERS_LIST/OFFER')->length;
  echo "Generated {$count} offers for {$country}: {$target}\n";
}
