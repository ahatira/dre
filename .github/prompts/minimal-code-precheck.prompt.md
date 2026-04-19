---
description: "Precheck a request with strict config-first and minimal-custom-code gates before implementing changes."
name: "Minimal Code Precheck"
argument-hint: "Describe the requested feature/fix and target scope"
agent: "agent"
---

Run a strict minimal-code precheck before writing or editing code.

Context:
- Global guidance: [.github/copilot-instructions.md](../copilot-instructions.md)
- Drupal expert skill: [./.claude/skills/drupal-expert/SKILL.md](../../.claude/skills/drupal-expert/SKILL.md)
- Theme guidance (if theme scope): [.github/instructions/ui-suite-bnppre-theme.instructions.md](../instructions/ui-suite-bnppre-theme.instructions.md)
- PHP DI guidance (if theme Hook/Utility PHP): [.github/instructions/ui-suite-bnppre-theme-php-di.instructions.md](../instructions/ui-suite-bnppre-theme-php-di.instructions.md)

Goal:
- Prefer existing Drupal/core/contrib configuration over custom code.
- Add custom code only when configuration is insufficient.
- Keep any required custom implementation as the smallest viable delta.

Process:
1. Restate the request in one sentence.
2. Identify target area:
   - Core/contrib config
   - Existing formatter/widget/view display settings
   - Install/update-time YAML config
   - Runtime custom code (only if required)
3. Run the config-first gate in this order:
   - Check existing admin settings that can satisfy the request.
   - Check formatter/widget/display options.
   - Check if install/update-time config can solve it without runtime logic.
   - Check if contrib module already provides the behavior.
4. Decide path:
   - If config can solve it: propose exact config path and do not propose custom runtime code.
   - If config cannot solve it: justify why, then propose minimal custom change.
5. If custom code is needed, include a minimality checklist:
   - No dead branches/options left behind
   - No duplicate logic with existing config
   - Hooks/services/JS kept thin
   - For PHP classes, DI respected and no new service-locator usage in business logic

Output format:
- Request Summary: <one sentence>
- Config Alternatives Found: Yes/No
- Recommended Path: Config-only or Minimal custom code
- Exact Implementation Plan:
  - Step 1
  - Step 2
  - Step 3
- Why Not Less: <brief justification>
- Risk Check:
  - Behavior regression risk
  - Cache/config deployment considerations
  - Validation commands to run

Input from user:
- Use the user argument as the feature/fix context.
- Do not skip the config-first gate.
