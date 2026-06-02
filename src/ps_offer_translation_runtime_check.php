<?php
$c = \Drupal::config('migrate_plus.migration.ps_offer_translations_from_xml');
print 'source.plugin=' . (string) $c->get('source.plugin') . PHP_EOL;
print 'ids=' . json_encode($c->get('source.ids')) . PHP_EOL;
print 'process.langcode=' . json_encode($c->get('process.langcode')) . PHP_EOL;
print 'process.content_translation_source=' . json_encode($c->get('process.content_translation_source')) . PHP_EOL;
