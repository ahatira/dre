#!/usr/bin/env bash
# Colors - Terminal color codes

if [[ -t 1 ]]; then
  readonly PS_COLOR_RESET='\033[0m'
  readonly PS_COLOR_RED='\033[0;31m'
  readonly PS_COLOR_GREEN='\033[0;32m'
  readonly PS_COLOR_YELLOW='\033[0;33m'
  readonly PS_COLOR_BLUE='\033[0;34m'
  readonly PS_COLOR_CYAN='\033[0;36m'
  readonly PS_COLOR_GRAY='\033[0;90m'
else
  readonly PS_COLOR_RESET=''
  readonly PS_COLOR_RED=''
  readonly PS_COLOR_GREEN=''
  readonly PS_COLOR_YELLOW=''
  readonly PS_COLOR_BLUE=''
  readonly PS_COLOR_CYAN=''
  readonly PS_COLOR_GRAY=''
fi
