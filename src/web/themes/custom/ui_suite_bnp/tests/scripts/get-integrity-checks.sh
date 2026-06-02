#!/usr/bin/env bash

ASSET_PATH='/project/app/themes/custom/ui_suite_bnp/assets/vendor/'

FILES=(
  "${ASSET_PATH}"bootstrap/bootstrap.bundle.min.js
  "${ASSET_PATH}"bootstrap/bootstrap.min.css
)

for FILE in "${FILES[@]}"
do
  echo -e "Scanning: ${FILE}"
  openssl dgst -sha384 -binary ${FILE} | openssl base64 -A
  echo -e "\n"
done
