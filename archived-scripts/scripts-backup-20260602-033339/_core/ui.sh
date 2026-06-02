#!/usr/bin/env bash

ps_header() {
  local title="$1"
  echo ""
  echo "============================================================"
  echo " ${title}"
  echo "============================================================"
}

ps_confirm() {
  local prompt="$1"
  local response=""
  read -r -p "${prompt} [y/N]: " response
  [[ "${response}" == "y" || "${response}" == "Y" ]]
}
