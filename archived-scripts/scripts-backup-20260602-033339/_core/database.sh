#!/usr/bin/env bash

ps_db_recreate_postgres_database() {
  local db_user="$1"
  local db_name="$2"
  ps_docker_exec_db "psql -U ${db_user} -d postgres -c \"DROP DATABASE IF EXISTS ${db_name};\" && psql -U ${db_user} -d postgres -c \"CREATE DATABASE ${db_name};\""
}
