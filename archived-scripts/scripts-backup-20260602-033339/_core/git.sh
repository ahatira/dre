#!/usr/bin/env bash

ps_git_available() {
  git rev-parse --is-inside-work-tree >/dev/null 2>&1
}

ps_git_current_branch() {
  git rev-parse --abbrev-ref HEAD
}
