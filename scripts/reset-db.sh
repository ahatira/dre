#!/usr/bin/env bash
# Script pour drop et recréer la base PostgreSQL "drupal" dans le conteneur Docker

set -euo pipefail

DB_NAME="drupal"
DB_USER="drupal"
DB_HOST="db"

# Drop la base (ignore les erreurs si elle n'existe pas)
# Recrée la base

# Terminate all connections to the database
docker compose exec -T db psql -U "$DB_USER" -d postgres -c "\
	SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '$DB_NAME' AND pid <> pg_backend_pid();\
"
# Drop the database
docker compose exec -T db psql -U "$DB_USER" -d postgres -c "DROP DATABASE IF EXISTS \"$DB_NAME\";"
# Recreate the database
docker compose exec -T db psql -U "$DB_USER" -d postgres -c "CREATE DATABASE \"$DB_NAME\";"

echo "Base PostgreSQL '$DB_NAME' vidée et recréée."
