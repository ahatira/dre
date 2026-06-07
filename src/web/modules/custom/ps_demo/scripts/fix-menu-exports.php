<?php

declare(strict_types=1);

/**
 * @file
 * One-shot sync of footer menu default content exports with stellar_menus.yml.
 */

$base = dirname(__DIR__) . "/export/content/menu_link_content";

$patch = static function (string $file, callable $fn): void {
  $path = $base . "/" . $file;
  if (!is_file($path)) {
    fwrite(STDERR, "Missing: $file\n");
    return;
  }
  $data = file_get_contents($path);
  $data = $fn($data);
  file_put_contents($path, $data);
};

// Headings.
$patch("a1000003-0000-4000-8000-000000000301.yml", static fn (string $d) => str_replace("value: Contact", "value: \"About BNP Paribas Real Estate\"", $d));
$patch("a1000003-0000-4000-8000-000000000303.yml", static function (string $d) {
  $d = str_replace("value: \"Our websites\"", "value: \"Business websites\"", $d);
  $d = str_replace("value: \"Nos sites\"", "value: \"Sites métier\"", $d);
  return preg_replace("/weight:\n    -\n      value: 20/", "weight:\n    -\n      value: 0", $d) ?? $d;
});

// Disable legacy column headings.
foreach (["302", "311", "312"] as $id) {
  $patch("a1000003-0000-4000-8000-000000000{$id}.yml", static function (string $d) {
    return preg_replace("/(enabled:\n    -\n      )value: true/", "$1value: false", $d, 2) ?? $d;
  });
}

// Legal labels + URIs.
$patch("a1000005-0000-4000-8000-000000000501.yml", static function (string $d) {
  $d = str_replace("value: \"Legal notice\"", "value: \"Data protection\"", $d);
  $d = str_replace("value: \"Mentions légales\"", "value: \"Données personnelles\"", $d);
  return str_replace("legal-notice/", "privacy-policy/", $d);
});
$patch("a1000005-0000-4000-8000-000000000503.yml", static function (string $d) {
  $d = str_replace("value: \"Privacy policy\"", "value: \"Disclaimer\"", $d);
  $d = str_replace("value: \"Politique de confidentialité\"", "value: \"Avertissement\"", $d);
  $d = str_replace("privacy-policy/", "legal-notice/", $d);
  return $d;
});
$patch("a1000005-0000-4000-8000-000000000504.yml", static function (string $d) {
  $d = str_replace("value: Accessibility", "value: \"Suppliers: BNP Paribas is committed to its partners and suppliers\"", $d);
  $d = str_replace("value: Accessibilité", "value: \"Fournisseurs : BNP Paribas sengage envers ses partenaires et fournisseurs\"", $d);
  return str_replace("accessibility/", "suppliers/", $d);
});

$template = static function (string $uuid, string $parent, string $menu, int $weight, string $enTitle, string $frTitle, string $uri) use ($base): void {
  $yaml = <<<YAML
_meta:
  version: 1.0
  entity_type: menu_link_content
  uuid: {$uuid}
  bundle: menu_link_content
  default_langcode: en
  depends:
    {$parent}: menu_link_content
default:
  enabled:
    -
      value: true
  title:
    -
      value: {}
  menu_name:
    -
      value: {$menu}
  link:
    -
      uri: {}
      title: null
      options:
        attributes:
          target: _blank
          rel: noopener