#!/usr/bin/env bash
# Script pour exporter la base PostgreSQL "drupal" depuis le conteneur Docker

set -euo pipefail

DB_NAME="drupal"
DB_USER="drupal"
DB_HOST="db"
EXPORT_DIR="$(pwd)/db-export"
EXPORT_FILE="$EXPORT_DIR/drupal-$(date +%Y%m%d-%H%M%S).sql"

mkdir -p "$EXPORT_DIR"

echo "Export de la base $DB_NAME dans $EXPORT_FILE ..."
docker compose exec -T db pg_dump -U "$DB_USER" "$DB_NAME" > "$EXPORT_FILE"
echo "Export terminé : $EXPORT_FILE"
