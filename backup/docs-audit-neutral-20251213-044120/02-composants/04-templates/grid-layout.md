# Grid Layout (Template)

Type: Template / Layout
Rôle: Grille responsive 12 colonnes avec gaps définis.
Statut: ✅ Stable
Version: 1.0.0

---

## Description
Grille responsive basée sur tokens `layout.grid.*` et `spacing.grid.gap`. Déclinaisons 2/3/4 colonnes.

## BEM
```
ps-grid
  ps-grid__item

Modificateurs:
  ps-grid--2-cols | --3-cols | --4-cols
```

## API
```yaml
name: 'PS Grid Layout'
status: stable
group: templates
props:
  type: object
  properties:
    columns: { type: number, enum: [2,3,4], default: 4 }
    gap: { type: string, default: '28px' }
    items: { type: array }
```

## Twig
```twig
<div class="ps-grid ps-grid--{{ columns }}-cols" style="gap: {{ gap }}">
  {% for item in items %}
    <div class="ps-grid__item">{{ item }}</div>
  {% endfor %}
</div>
```

## Tokens
- `layout.grid.*`, `spacing.grid.gap`

## A11y
- Ordre logique des items, pas de pièges clavier
