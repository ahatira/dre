---
description: "Run release prep checks for ui_suite_bnppre with the project-standard asset validation and cache clear sequence."
name: "Theme Release Prep"
argument-hint: "Optional release notes or scope"
agent: "agent"
---

Prepare this workspace for a theme release using the exact project sequence.

Context:
- Theme path: [web/themes/custom/vendor/bin/drush cr](../web/themes/custom/ui_suite_bnppre)
- Global guidance: [.github/copilot-instructions.md](../copilot-instructions.md)
- Theme instruction: [.github/instructions/ui-suite-bnppre-theme.instructions.md](../instructions/ui-suite-bnppre-theme.instructions.md)

Steps:
1. Run commands in this order, stopping at first failure:
   - From theme folder, run npm run build
   - From theme folder, run npm run build:bnppre-icons:check
   - From theme folder, run npm run build:theme-yaml:check
   - From repo root, run vendor/bin/drush cr
2. If any command fails, include the failing command, key error lines, and the most likely fix.
3. If all commands pass, provide a concise release-prep summary with:
   - Command status list in order
   - Any generated files detected
   - Suggested next action before release

Input from user:
- Use user argument as release context only. Do not change command order based on the argument.

Output format:
- Release Prep Result: PASS or FAIL
- Command Results: one line per command in order
- Notes: concise, actionable, and repo-specific
