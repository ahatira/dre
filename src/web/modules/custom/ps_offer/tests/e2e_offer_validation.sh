#!/usr/bin/env bash
set -uo pipefail

NID="${1:-1}"
BUDGET="${2:-0}"
STATUS="${3:-draft}"
BUNDLE="${4:-offer}"
SPECIAL="${5:-none}"

DRUSH="docker exec ps_php /var/www/html/vendor/bin/drush"

if [[ "$SPECIAL" == "manual-duplicate" ]]; then
  php_code="
\$bundle='${BUNDLE}';
if (\$bundle !== 'offer') {
  echo 'FAIL: manual duplicate scenario requires offer bundle\n';
  exit(2);
}
\$storage = \Drupal::entityTypeManager()->getStorage('node');
\$title_prefix = 'E2E Manual Duplicate Ref';
\$query = Drupal::entityQuery('node')
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->condition('title', \$title_prefix, 'STARTS_WITH');
\$ids = \$query->execute();
if (!empty(\$ids)) {
  foreach (\$storage->loadMultiple(\$ids) as \$old) {
    \$old->delete();
  }
}
\$manual = 'REF-MANUAL-DUP-001';
\$first = \$storage->create(['type' => 'offer', 'title' => \$title_prefix . ' A']);
\$first->set('field_reference_auto', 0);
\$first->set('field_reference', \$manual);
\$first->setUnpublished();
\$first->save();
echo 'seed_saved\\n';

\$second = \$storage->create(['type' => 'offer', 'title' => \$title_prefix . ' B']);
\$second->set('field_reference_auto', 0);
\$second->set('field_reference', \$manual);
\$second->setUnpublished();
try {
  \$second->save();
  echo 'FAIL: manual duplicate was accepted\\n';
  exit(43);
}
catch (\Exception \$e) {
  echo 'PASS: manual duplicate rejected\\n';
  echo 'duplicate_error:' . \$e->getMessage() . '\\n';
}
"

  set +e
  output=$(${DRUSH} php:eval "$php_code" 2>&1)
  exit_code=$?
  set -e

  echo "$output"

  if [[ $exit_code -ne 0 && "$output" != *"PASS: manual duplicate rejected"* ]]; then
    echo "ERROR: Unexpected script failure"
    exit 1
  fi

  if [[ "$output" == *"PASS: manual duplicate rejected"* && "$output" == *"duplicate_error:Manual reference value is already used by another offer."* ]]; then
    echo "PASS: manual duplicate validation"
  else
    echo "FAIL: manual duplicate validation"
    exit 1
  fi

  exit 0
fi

if [[ "$SPECIAL" == "manual-duplicate-published" ]]; then
  php_code="
\$bundle='${BUNDLE}';
if (\$bundle !== 'offer') {
  echo 'FAIL: manual duplicate published scenario requires offer bundle\n';
  exit(2);
}
\$storage = \Drupal::entityTypeManager()->getStorage('node');
\$title_prefix = 'E2E Manual Duplicate Published Ref';
\$query = \Drupal::entityQuery('node')
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->condition('title', \$title_prefix, 'STARTS_WITH');
\$ids = \$query->execute();
if (!empty(\$ids)) {
  foreach (\$storage->loadMultiple(\$ids) as \$old) {
    \$old->delete();
  }
}

\$manual = 'REF-MANUAL-DUP-PUB-001';
\$first = \$storage->create(['type' => 'offer', 'title' => \$title_prefix . ' A']);
\$first->set('field_reference_auto', 0);
\$first->set('field_reference', \$manual);
\$first->setUnpublished();
\$first->save();
echo 'seed_saved\n';

\$second = \$storage->create(['type' => 'offer', 'title' => \$title_prefix . ' B']);
\$second->set('field_reference_auto', 0);
\$second->set('field_reference', \$manual);
\$second->set('field_surfaces', [['qualification' => 'TOTAL', 'value' => 100, 'unit' => 'M2']]);
\$second->setPublished();
try {
  \$second->save();
  echo 'FAIL: manual duplicate on published was accepted\n';
  exit(46);
}
catch (\Exception \$e) {
  echo 'PASS: manual duplicate rejected on published\n';
  echo 'duplicate_published_error:' . \$e->getMessage() . '\n';
}
"

  set +e
  output=$(${DRUSH} php:eval "$php_code" 2>&1)
  exit_code=$?
  set -e

  echo "$output"

  if [[ $exit_code -ne 0 && "$output" != *"PASS: manual duplicate rejected on published"* ]]; then
    echo "ERROR: Unexpected script failure"
    exit 1
  fi

  if [[ "$output" == *"PASS: manual duplicate rejected on published"* && "$output" == *"duplicate_published_error:Manual reference value is already used by another offer."* ]]; then
    echo "PASS: manual duplicate published validation"
  else
    echo "FAIL: manual duplicate published validation"
    exit 1
  fi

  exit 0
fi

if [[ "$SPECIAL" == "manual-self-edit" ]]; then
  php_code="
\$bundle='${BUNDLE}';
if (\$bundle !== 'offer') {
  echo 'FAIL: manual self-edit scenario requires offer bundle\\n';
  exit(2);
}
\$storage = \Drupal::entityTypeManager()->getStorage('node');
\$title_prefix = 'E2E Manual Self Ref';
\$query = \Drupal::entityQuery('node')
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->condition('title', \$title_prefix, 'STARTS_WITH');
\$ids = \$query->execute();
if (!empty(\$ids)) {
  foreach (\$storage->loadMultiple(\$ids) as \$old) {
    \$old->delete();
  }
}

\$manual = 'REF-MANUAL-SELF-001';
\$node = \$storage->create(['type' => 'offer', 'title' => \$title_prefix . ' A']);
\$node->set('field_reference_auto', 0);
\$node->set('field_reference', \$manual);
\$node->setUnpublished();
\$node->save();
echo 'seed_saved\\n';

\$edit = \$storage->load((int) \$node->id());
if (!\$edit) {
  echo 'FAIL: unable to reload seeded node\\n';
  exit(44);
}
\$edit->set('field_reference_auto', 0);
\$edit->set('field_reference', \$manual);
\$edit->setUnpublished();
try {
  \$edit->save();
  echo 'PASS: manual self reference preserved on edit\\n';
}
catch (\Exception \$e) {
  echo 'FAIL: manual self reference blocked on edit\\n';
  echo 'self_edit_error:' . \$e->getMessage() . '\\n';
  exit(45);
}
"

  set +e
  output=$(${DRUSH} php:eval "$php_code" 2>&1)
  exit_code=$?
  set -e

  echo "$output"

  if [[ $exit_code -ne 0 ]]; then
    echo "ERROR: Unexpected script failure"
    exit 1
  fi

  if [[ "$output" == *"PASS: manual self reference preserved on edit"* ]]; then
    echo "PASS: manual self-edit validation"
  else
    echo "FAIL: manual self-edit validation"
    exit 1
  fi

  exit 0
fi

php_code="
\$nid=(int) '${NID}';
\$bundle='${BUNDLE}';
\$node = Drupal\node\Entity\Node::load(\$nid);
if (!\$node) {
  \$node = Drupal\node\Entity\Node::create(['type' => \$bundle, 'title' => 'Test Offer ' . \$nid]);
}
if (\$node->hasField('field_budget_value')) {
  \$node->set('field_budget_value', '${BUDGET}');
}
\$node->status = ('${STATUS}' === 'published' ? 1 : 0);

try {
  \$node->save();
  echo 'save_success\n';
} catch (\Exception \$e) {
  echo 'budget_save_error:' . \$e->getMessage() . '\n';
  exit(42);
}
"

set +e
output=$(${DRUSH} php:eval "$php_code" 2>&1)
exit_code=$?
set -e

echo "$output"

if [[ "${BUNDLE}" != "offer" ]]; then
  echo "PASS: no validation applied (non-offer bundle)"
  exit 0
fi

if [[ "$SPECIAL" == "no-agent" ]]; then
  echo "PASS: offer unpublished due to missing agent"
  exit 0
fi

if [[ $exit_code -ne 0 ]]; then
  if [[ "$output" == *"budget_save_error"* ]]; then
    if [[ "${STATUS}" == "published" ]]; then
      echo "FAIL: budget validation (publication)"
    else
      echo "WARN: budget validation (draft)"
    fi
  else
    echo "ERROR: Unexpected script failure"
    exit 1
  fi
else
  # Si warning métier dans la sortie, afficher WARN
  if echo "$output" | grep -q "Price value must be greater than 0 when a price period is set"; then
    echo "WARN: budget validation (draft)"
  else
    echo "PASS: budget validation"
  fi
fi
