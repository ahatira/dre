#!/usr/bin/env bash
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

ACTION="${1:-}"
GROUP_ID="${2:-}"
DEF_ID="${3:-}"
LABEL="${4:-}"
TYPE_DRIVER="${5:-flag}"
CODE="${6:-}"
PAYLOAD_B64="${7:-}"
REQUIRED_ASSETS_B64="${8:-}"

run_eval() {
  local php_code="$1"
  ps_e2e_drush php:eval "$php_code"
}

run_drush() {
  local drush_args="$1"
  ps_e2e_drush ${drush_args}
}

case "$ACTION" in
  cleanup_group)
    run_eval "\$group='${GROUP_ID}'; \$def_storage=\\Drupal::entityTypeManager()->getStorage('fb_feature_definition'); \$defs=\$def_storage->loadByProperties(['group'=>\$group]); foreach (\$defs as \$d) { \$d->delete(); } \$g=\\Drupal::entityTypeManager()->getStorage('fb_feature_group')->load(\$group); if (\$g) { \$g->delete(); } print 'PASS: cleanup completed';"
    ;;
  ensure_group)
    run_eval "\$storage=\\Drupal::entityTypeManager()->getStorage('fb_feature_group'); \$g=\$storage->load('${GROUP_ID}'); if (!\$g) { \$g=\$storage->create(['id'=>'${GROUP_ID}','label'=>'${LABEL}','weight'=>0,'status'=>TRUE]); \$g->save(); print 'PASS: feature group created'; } else { print 'PASS: feature group already exists'; }"
    ;;
  group_exists)
    run_eval "\$g=\\Drupal::entityTypeManager()->getStorage('fb_feature_group')->load('${GROUP_ID}'); if (!\$g) { print 'FAIL: feature group missing'; exit(1);} print 'PASS: feature group exists';"
    ;;
  create_definition)
    run_eval "\$storage=\\Drupal::entityTypeManager()->getStorage('fb_feature_definition'); \$id='${DEF_ID}'; if (\$storage->load(\$id)) { print 'ERROR: definition id exists'; exit(2);} \$dup=\$storage->loadByProperties(['group'=>'${GROUP_ID}','code'=>'${CODE}']); if (!empty(\$dup)) { print 'ERROR: duplicate code in group'; exit(3);} \$payload=[]; if ('${PAYLOAD_B64}' !== '') { \$decoded=base64_decode('${PAYLOAD_B64}', TRUE); if (\$decoded === FALSE) { print 'ERROR: invalid payload base64'; exit(4);} \$json=json_decode(\$decoded, TRUE); if (!is_array(\$json)) { print 'ERROR: invalid payload json'; exit(5);} \$payload=\$json; } if ('${TYPE_DRIVER}' === 'taxonomy') { \$vocabulary_id=(string) (\$payload['vocabulary_id'] ?? ''); if (\$vocabulary_id === '') { print 'ERROR: taxonomy vocabulary is required'; exit(8);} if (!\\Drupal::entityTypeManager()->hasDefinition('taxonomy_vocabulary')) { print 'ERROR: taxonomy vocabulary entity type unavailable'; exit(9);} \$vocabulary=\\Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->load(\$vocabulary_id); if (!\$vocabulary) { print 'ERROR: taxonomy vocabulary does not exist'; exit(10);} } \$required=[]; if ('${REQUIRED_ASSETS_B64}' !== '') { \$decoded=base64_decode('${REQUIRED_ASSETS_B64}', TRUE); if (\$decoded === FALSE) { print 'ERROR: invalid required assets base64'; exit(6);} \$json=json_decode(\$decoded, TRUE); if (!is_array(\$json)) { print 'ERROR: invalid required assets json'; exit(7);} \$required=array_values(array_filter(array_map(static fn(\$v): string => trim((string) \$v), \$json), static fn(string \$v): bool => \$v !== '')); } \$d=\$storage->create(['id'=>\$id,'label'=>'${LABEL}','code'=>'${CODE}','group'=>'${GROUP_ID}','type_driver'=>'${TYPE_DRIVER}','weight'=>0,'status'=>TRUE,'required_asset_types'=>\$required,'payload_defaults'=>\$payload]); \$d->save(); print 'PASS: feature definition created';"
    ;;
  definition_exists)
    run_eval "\$d=\\Drupal::entityTypeManager()->getStorage('fb_feature_definition')->load('${DEF_ID}'); if (!\$d) { print 'FAIL: feature definition missing'; exit(1);} print 'PASS: feature definition exists';"
    ;;
  update_definition_label)
    run_eval "\$storage=\\Drupal::entityTypeManager()->getStorage('fb_feature_definition'); \$d=\$storage->load('${DEF_ID}'); if (!\$d) { print 'FAIL: feature definition missing'; exit(1);} \$d->set('label','${LABEL}'); \$d->save(); print 'PASS: feature definition updated';"
    ;;
  definition_label)
    run_eval "\$d=\\Drupal::entityTypeManager()->getStorage('fb_feature_definition')->load('${DEF_ID}'); if (!\$d) { print 'FAIL: feature definition missing'; exit(1);} print 'RESULT:' . \$d->label();"
    ;;
  definition_payload_json)
    run_eval "\$d=\\Drupal::entityTypeManager()->getStorage('fb_feature_definition')->load('${DEF_ID}'); if (!\$d) { print 'FAIL: feature definition missing'; exit(1);} print 'RESULT:' . json_encode(\$d->getPayloadDefaults(), JSON_UNESCAPED_UNICODE);"
    ;;
  delete_definition)
    run_eval "\$storage=\\Drupal::entityTypeManager()->getStorage('fb_feature_definition'); \$d=\$storage->load('${DEF_ID}'); if (!\$d) { print 'PASS: feature definition already deleted'; return; } \$d->delete(); print 'PASS: feature definition deleted';"
    ;;
  definition_not_exists)
    run_eval "\$d=\\Drupal::entityTypeManager()->getStorage('fb_feature_definition')->load('${DEF_ID}'); if (\$d) { print 'FAIL: feature definition still exists'; exit(1);} print 'PASS: feature definition not found';"
    ;;
  feature_type_exists)
    run_eval "\$defs=\\Drupal::service('plugin.manager.feature_type')->getDefinitions(); if (!isset(\$defs['${TYPE_DRIVER}'])) { print 'FAIL: feature type missing'; exit(1);} print 'PASS: feature type exists';"
    ;;
  ensure_dictionary_fixture)
    run_eval "\$type_id='${GROUP_ID}'; \$type_storage=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_type'); \$entry_storage=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_entry'); \$type=\$type_storage->load(\$type_id); if (!\$type) { \$type=\$type_storage->create(['id'=>\$type_id,'label'=>'Test Dictionary Type']); \$type->save(); } \$entries=[['code'=>'A','label'=>'Option A'],['code'=>'B','label'=>'Option B']]; foreach (\$entries as \$row) { \$entry_id=\$type_id . '.' . strtolower(\$row['code']); \$entry=\$entry_storage->load(\$entry_id); if (!\$entry) { \$entry=\$entry_storage->create(['id'=>\$entry_id,'type'=>\$type_id,'code'=>\$row['code'],'label'=>\$row['label'],'weight'=>0]); } else { \$entry->set('label', \$row['label']); } \$entry->save(); } print 'PASS: dictionary fixture ready';"
    ;;
  ensure_vocabulary)
    HAS_TAXONOMY="$(run_eval 'print (\Drupal::entityTypeManager()->hasDefinition("taxonomy_vocabulary") ? "1" : "0");')"
    if [[ "$HAS_TAXONOMY" != "1" ]]; then
      run_drush "pm:enable taxonomy -y"
    fi
    run_eval "\$vocabulary_id='${GROUP_ID}'; if (!\\Drupal::entityTypeManager()->hasDefinition('taxonomy_vocabulary')) { print 'ERROR: taxonomy vocabulary entity type unavailable'; exit(1);} \$storage=\\Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary'); \$vocabulary=\$storage->load(\$vocabulary_id); if (!\$vocabulary) { \$vocabulary=\$storage->create(['vid'=>\$vocabulary_id, 'name'=>'Test Vocabulary ' . strtoupper(\$vocabulary_id)]); \$vocabulary->save(); print 'PASS: vocabulary created'; } else { print 'PASS: vocabulary already exists'; }"
    ;;
  catalogue_options_count)
    run_eval "\$builder=\\Drupal::service('ps_feature.catalogue_builder'); \$node_type_storage=\\Drupal::entityTypeManager()->getStorage('node_type'); \$type_ids=array_keys(\$node_type_storage->loadMultiple()); \$bundle=reset(\$type_ids) ?: 'article'; \$node=\\Drupal::entityTypeManager()->getStorage('node')->create(['type'=>\$bundle, 'title'=>'Feature Catalogue Probe']); \$catalogue=\$builder->buildForEntity(\$node); \$count=-1; foreach (\$catalogue['definitions'] as \$definition) { if ((\$definition['id'] ?? '') === '${DEF_ID}') { \$count=count(\$definition['options'] ?? []); break; } } if (\$count < 0) { print 'FAIL: definition not found in catalogue'; exit(1);} print 'RESULT:' . (string) \$count;"
    ;;
  catalogue_definition_visible_for_asset)
    run_eval "\$builder=\\Drupal::service('ps_feature.catalogue_builder'); \$node_type_storage=\\Drupal::entityTypeManager()->getStorage('node_type'); \$bundle='offer'; if (!\$node_type_storage->load(\$bundle)) { \$type_ids=array_keys(\$node_type_storage->loadMultiple()); \$bundle=reset(\$type_ids) ?: 'article'; } \$node=\\Drupal::entityTypeManager()->getStorage('node')->create(['type'=>\$bundle, 'title'=>'Feature Catalogue Visibility Probe']); if (\$node->hasField('field_asset_type')) { \$node->set('field_asset_type', '${CODE}'); } \$catalogue=\$builder->buildForEntity(\$node); \$visible=0; foreach (\$catalogue['definitions'] as \$definition) { if ((\$definition['id'] ?? '') === '${DEF_ID}') { \$visible=1; break; } } print 'RESULT:' . (string) \$visible;"
    ;;
  roundtrip_builder_state)
    run_eval "\$requested_nid=(int) '${GROUP_ID}'; \$definition_id='${DEF_ID}'; \$node_storage=\\Drupal::entityTypeManager()->getStorage('node'); \$builder=\\Drupal::service('ps_feature.state_builder'); \$query=\\Drupal::entityQuery('node')->condition('type', 'offer')->range(0, 1)->accessCheck(FALSE); \$ids=\$query->execute(); \$fallback_nid=\$ids ? (int) reset(\$ids) : 0; \$nid=\$requested_nid > 0 ? \$requested_nid : \$fallback_nid; if (\$nid <= 0) { print 'FAIL: no offer node available'; exit(1);} \$node=\$node_storage->load(\$nid); if (!\$node) { print 'FAIL: offer node not found'; exit(1);} if (!\$node->hasField('field_features')) { print 'FAIL: field_features missing on node'; exit(1);} \$initial_raw=base64_decode('${PAYLOAD_B64}', TRUE); \$updated_raw=base64_decode('${REQUIRED_ASSETS_B64}', TRUE); if (\$initial_raw === FALSE || \$updated_raw === FALSE) { print 'FAIL: invalid roundtrip payload encoding'; exit(1);} \$initial=json_decode(\$initial_raw, TRUE); \$updated=json_decode(\$updated_raw, TRUE); if (!is_array(\$initial) || !is_array(\$updated)) { print 'FAIL: invalid roundtrip payload json'; exit(1);} if (\$node->hasField('field_reference_auto')) { \$node->set('field_reference_auto', 1); } if (\$node->hasField('field_operation_type')) { \$node->set('field_operation_type', 'LOC'); } if (\$node->hasField('field_asset_type')) { \$node->set('field_asset_type', 'BUR'); } if (\$node->hasField('field_media_gallery') && count(\$node->get('field_media_gallery')) === 0) { \$media_storage=\\Drupal::entityTypeManager()->getStorage('media'); \$media_ids=\\Drupal::entityQuery('media')->accessCheck(FALSE)->range(0,1)->execute(); if (\$media_ids) { \$node->set('field_media_gallery', [['target_id' => (int) reset(\$media_ids)]]); } } \$node->set('field_features', [['feature_definition_id' => \$definition_id, 'payload' => json_encode(\$initial, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION)]]); \$node->setUnpublished(); \$node->save(); \$saved=\$node_storage->load(\$nid); \$state1=\$builder->buildFromItems(\$saved->get('field_features')); \$payload1=\$state1['features'][0]['payload'] ?? NULL; if (json_encode(\$payload1, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION) !== json_encode(\$initial, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION)) { print 'FAIL: roundtrip state initial mismatch'; print '\nDEBUG initial expected=' . json_encode(\$initial, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION); print '\nDEBUG initial actual=' . json_encode(\$payload1, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION); exit(1);} print 'PASS: roundtrip state initial payload'; \$saved->set('field_features', [['feature_definition_id' => \$definition_id, 'payload' => json_encode(\$updated, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION)]]); \$saved->setUnpublished(); \$saved->save(); \$reloaded=\$node_storage->load(\$nid); \$state2=\$builder->buildFromItems(\$reloaded->get('field_features')); \$payload2=\$state2['features'][0]['payload'] ?? NULL; if (json_encode(\$payload2, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION) !== json_encode(\$updated, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION)) { print 'FAIL: roundtrip state updated mismatch'; print '\nDEBUG updated expected=' . json_encode(\$updated, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION); print '\nDEBUG updated actual=' . json_encode(\$payload2, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION); exit(1);} if (json_encode(\$payload2, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION) === json_encode(\$payload1, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION)) { print 'FAIL: roundtrip state unchanged after update'; exit(1);} print 'PASS: roundtrip state updated payload'; print '\nPASS: roundtrip state changed after update';"
    ;;
  *)
    echo "FAIL: unknown action '${ACTION}'"
    exit 1
    ;;
esac
