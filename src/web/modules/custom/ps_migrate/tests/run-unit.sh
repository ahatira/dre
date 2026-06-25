#!/usr/bin/env bash
# Run ps_migrate Unit tests (Drupal bootstrap via core phpunit.xml).
set -euo pipefail

SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)"
cd "${SRC}"
exec vendor/bin/phpunit -c web/core/phpunit.xml.dist \
  web/modules/custom/ps_migrate/tests/src/Unit "$@"
