# Drupal AI Skills

Skills source : [edutrul/drupal-ai](https://github.com/edutrul/drupal-ai) (MIT).

## Structure

```
.claude/skills/{skill-name}/SKILL.md   # source
.cursor/rules/skills/{skill-name}.mdc # symlink → Cursor rules
```

## Mise à jour

```bash
git clone --depth 1 https://github.com/edutrul/drupal-ai /tmp/drupal-ai
rsync -a --delete /tmp/drupal-ai/.claude/skills/ .claude/skills/
```

Puis recréer les symlinks :

```bash
mkdir -p .cursor/rules/skills
for skill_dir in .claude/skills/*/; do
  name=$(basename "$skill_dir")
  ln -sf "../../../.claude/skills/${name}/SKILL.md" ".cursor/rules/skills/${name}.mdc"
done
```

## Skills ignorés pour PS

- `ddev-expert` — projet Docker Compose, pas DDEV

## Règle projet hooks

Les skills drupal-ai disent « prefer OOP ». PS Project impose **OOP uniquement** :
voir `.cursor/rules/drupal-hooks.mdc` (prioritaire sur le skill).
