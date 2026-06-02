#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Git Diagnostics"
if ps_git_available; then
  ps_info "Branch: $(ps_git_current_branch)"
  git status --short
else
  ps_warn "Current directory is not a git repository"
fi
