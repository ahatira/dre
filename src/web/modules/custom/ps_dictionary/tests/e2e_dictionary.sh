#!/usr/bin/env bash
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

ACTION="${1:-}" 
TYPE="${2:-}"
CODE="${3:-}"
LABEL="${4:-}"
WEIGHT="${5:-0}"

run_eval() {
  local php_code="$1"
  ps_e2e_drush php:eval "$php_code"
}

entry_id() {
  local type="$1"
  local code="$2"
  local lowered
  lowered="$(echo "$code" | tr '[:upper:]' '[:lower:]')"
  echo "${type}.${lowered}"
}

case "$ACTION" in
  ensure_type)
    run_eval "\$id='${TYPE}'; \$label='${LABEL:-Test Type}'; \$storage=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_type'); \$entity=\$storage->load(\$id); if (!\$entity) { \$entity=\$storage->create(['id'=>\$id,'label'=>\$label]); \$entity->save(); print 'PASS: dictionary type ensured'; } else { print 'PASS: dictionary type already exists'; }"
    ;;
  type_exists)
    run_eval "\$entity=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_type')->load('${TYPE}'); if (!\$entity) { print 'FAIL: dictionary type missing'; exit(1);} print 'PASS: dictionary type exists';"
    ;;
  create_entry)
    ID="$(entry_id "$TYPE" "$CODE")"
    run_eval "\$storage=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_entry'); if (\$storage->load('${ID}')) { print 'ERROR: duplicate code'; exit(2);} \$entity=\$storage->create(['id'=>'${ID}','type'=>'${TYPE}','code'=>'${CODE}','label'=>'${LABEL}','weight'=>(int) '${WEIGHT}']); \$entity->save(); print 'PASS: dictionary entry created';"
    ;;
  entry_exists)
    ID="$(entry_id "$TYPE" "$CODE")"
    run_eval "\$entity=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_entry')->load('${ID}'); if (!\$entity) { print 'FAIL: dictionary entry missing'; exit(1);} print 'PASS: dictionary entry exists';"
    ;;
  update_entry)
    ID="$(entry_id "$TYPE" "$CODE")"
    run_eval "\$storage=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_entry'); \$entity=\$storage->load('${ID}'); if (!\$entity) { print 'FAIL: dictionary entry missing'; exit(1);} \$entity->set('label','${LABEL}'); \$entity->save(); print 'PASS: dictionary entry updated';"
    ;;
  delete_entry)
    ID="$(entry_id "$TYPE" "$CODE")"
    run_eval "\$storage=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_entry'); \$entity=\$storage->load('${ID}'); if (!\$entity) { print 'PASS: dictionary entry already deleted'; return; } \$entity->delete(); print 'PASS: dictionary entry deleted';"
    ;;
  entry_not_exists)
    ID="$(entry_id "$TYPE" "$CODE")"
    run_eval "\$entity=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_entry')->load('${ID}'); if (\$entity) { print 'FAIL: dictionary entry still exists'; exit(1);} print 'PASS: dictionary entry not found';"
    ;;
  resolve_label)
    run_eval "\$resolver=\\Drupal::service('ps_dictionary.resolver'); \$label=\$resolver->resolveLabel('${TYPE}','${CODE}'); if (\$label === NULL) { print 'RESULT:NULL'; return; } print 'RESULT:' . \$label;"
    ;;
  autocomplete_contains)
    RESPONSE="$(curl -sS "${BASE}/ps-dictionary/autocomplete/${TYPE}?q=${CODE}")"
    echo "$RESPONSE"
    if echo "$RESPONSE" | grep -qi "${CODE}\|${LABEL}"; then
      echo "PASS: autocomplete contains expected value"
    else
      echo "FAIL: autocomplete missing expected value"
      exit 1
    fi
    ;;
  cleanup_type)
    run_eval "\$type='${TYPE}'; \$type_storage=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_type'); \$entry_storage=\\Drupal::entityTypeManager()->getStorage('ps_dictionary_entry'); \$entries=\$entry_storage->loadByProperties(['type'=>\$type]); foreach (\$entries as \$entry) { \$entry->delete(); } \$entity=\$type_storage->load(\$type); if (\$entity) { \$entity->delete(); } print 'PASS: cleanup completed';"
    ;;
  *)
    echo "FAIL: unknown action '${ACTION}'"
    exit 1
    ;;
esac
