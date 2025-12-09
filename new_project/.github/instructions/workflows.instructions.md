# Instructions Workflows

## Workflow principal : Création composant

### Étapes 1-11 (ordre strict)

**1. Lire spécification**
```bash
# Consulter doc design
cat docs/design/{level}/{component}.md
```

**2. Valider niveau atomic**
- Atom : élément HTML de base, autonome
- Molecule : 2-5 atoms combinés, fonction simple
- Organism : section complète, layout complexe
- Template : structure page (slots)
- Page : template + données réelles

**3. Vérifier dépendances**
```bash
# Lister dépendances requises
grep "Requires:" docs/design/{level}/{component}.md

# Vérifier disponibilité
ls components/{level}/{dependency}/
```

**4. Créer dépendances manquantes**
- Créer composants de niveau inférieur AVANT
- Valider chacun individuellement
- Commit après chaque composant

**5. Générer structure**
```bash
npm run generate:pattern -- --type=atom --name="component-name"
# ou mode interactif
npm run generate:pattern
```

**6. Implémenter 5 fichiers**
- `component.twig` : Template + header + defaults + classes
- `component.css` : Styles tokens uniquement, nesting, BEM
- `component.component.yml` : Schema SDC + props + slots
- `component.stories.jsx` : Stories + argTypes + tags: ['autodocs']
- `README.md` : Usage + Props + BEM + Tokens + A11y

**7. Tester localement**
```bash
npm run watch
# → http://localhost:6006
# Vérifier visuellement toutes variantes
```

**8. Build validation**
```bash
npm run build
# Doit passer sans erreurs (lint, format, compile)
```

**9. Audit conformité (100% requis)**

#### Checklist conformité composant

**Structure (10 points)** :
- [ ] 5 fichiers présents (twig, css, yml, stories, README)
- [ ] Nommage kebab-case cohérent

**Twig (20 points)** :
- [ ] Header comment complet
- [ ] Defaults avec merge
- [ ] Classes avec ternary (pas arrow functions)
- [ ] Includes avec `only`

**CSS (20 points)** :
- [ ] Tokens uniquement (aucune valeur hardcodée)
- [ ] Nesting max 3 niveaux
- [ ] BEM strict (`.ps-block__element--modifier`)
- [ ] Focus-visible pour interactifs
- [ ] Couleurs sémantiques (`--primary`, `--success`, etc.)

**YAML (15 points)** :
- [ ] Schema complet avec types
- [ ] Properties avec descriptions
- [ ] Enums pour options limitées
- [ ] LibraryOverrides avec dependencies

**Storybook (20 points)** :
- [ ] `tags: ['autodocs']` présent (sauf base/*)
- [ ] ArgTypes catégorisés (Content, Appearance, State, Behavior)
- [ ] Default story + Showcases (variantes, sizes, states)
- [ ] Données Faker.js contextuelles (Real Estate)

**README (10 points)** :
- [ ] Usage avec exemple Twig
- [ ] Props table complète
- [ ] BEM structure
- [ ] Design tokens utilisés
- [ ] Accessibility guidelines

**Accessibilité (5 points)** :
- [ ] WCAG 2.2 AA compliant
- [ ] ARIA attributes si nécessaire
- [ ] Keyboard navigation

**Score minimum** : 100/100

**10. Commit structuré**
```bash
git add components/{level}/{component}/
git commit -m "feat({level}): Add {component} component

- Implement 5-file structure (twig, css, yml, stories, README)
- Support variants: {list variants}
- Full Autodocs with categorized argTypes
- References spec: docs/design/{level}/{component}.md"
```

**11. Mettre à jour changelog**
```bash
# Ajouter entrée dans docs/ps-design/CHANGELOG.md
## [Date] - {Component} ({Level})
- Description
- Variants implemented
- Dependencies
```

## Workflow secondaire : Audit et standardisation

### Pour composants legacy ou refactoring

**1. Lancer audit**
```bash
# Vérifier structure fichiers
ls -la components/{level}/{component}/

# Vérifier tokens CSS
grep -E "(#[0-9a-fA-F]{3,6}|[0-9]+px|[0-9]+ms)" components/{level}/{component}/*.css

# Vérifier autodocs
grep "tags.*autodocs" components/{level}/{component}/*.stories.jsx
```

**2. Checklist conformité**
Utiliser checklist audit (étape 9 workflow principal)

**3. Corriger non-conformités**
- Priorité 1 : Tokens hardcodés → Remplacer par `var(--token)`
- Priorité 2 : Autodocs manquants → Ajouter `tags: ['autodocs']`
- Priorité 3 : BEM incohérent → Renommer classes
- Priorité 4 : Focus-visible manquant → Ajouter styles
- Priorité 5 : README incomplet → Enrichir sections

**4. Re-tester**
```bash
npm run build
npm run watch
# Valider visuellement + build
```

**5. Commit refactoring**
```bash
git commit -m "refactor({level}): Standardize {component} to 100% conformity

- Replace hardcoded values with tokens
- Add missing autodocs tags
- Update BEM class names
- Add focus-visible styles
- Enrich README documentation"
```

## Workflow tertiaire : Génération en masse

### Pour 47 composants génériques (H0-H48)

**Phase 1 : Atoms (H0-H16)**
```bash
# 16 atoms × 1h = 16h
for atom in button badge input select textarea checkbox radio toggle icon avatar link label heading text divider spinner; do
  npm run generate:pattern -- --type=atom --name="$atom"
  # Implémenter 5 fichiers
  npm run build
  git commit -m "feat(elements): Add $atom component"
done
```

**Phase 2 : Molecules (H16-H31)**
```bash
# 15 molecules × 1h = 15h
for molecule in form-field card dropdown tabs accordion breadcrumb pagination table modal toast tooltip alert progress-bar search-bar tag-list; do
  # Vérifier dépendances atoms
  npm run generate:pattern -- --type=molecule --name="$molecule"
  # Implémenter 5 fichiers
  npm run build
  git commit -m "feat(components): Add $molecule component"
done
```

**Phase 3 : Organisms (H31-H39)**
```bash
# 8 organisms × 1h = 8h
for organism in header footer navigation listing hero article-preview sidebar contact-form; do
  npm run generate:pattern -- --type=organism --name="$organism"
  # Implémenter 5 fichiers
  npm run build
  git commit -m "feat(collections): Add $organism component"
done
```

**Phase 4 : Templates (H39-H43)**
```bash
# 4 templates × 1h = 4h
for template in one-column two-column three-column hero-layout; do
  npm run generate:pattern -- --type=template --name="$template"
  # Implémenter 5 fichiers
  npm run build
  git commit -m "feat(layouts): Add $template template"
done
```

**Phase 5 : Pages (H43-H48)**
```bash
# 4 pages × 1.25h = 5h
for page in home listing detail contact; do
  npm run generate:pattern -- --type=page --name="$page"
  # Implémenter 5 fichiers
  npm run build
  git commit -m "feat(pages): Add $page page"
done
```

## Commandes utiles

### Vérification tokens
```bash
# Rechercher token dans props/
npm run tokens:check -- --primary

# Résultat :
# source/props/brand.css:10:  --primary: #00915A;
# source/patterns/elements/button/button.css:5:  background: var(--primary);
# (12 usages found)
```

### Génération pattern
```bash
# Mode interactif
npm run generate:pattern

# Mode flags
npm run generate:pattern -- --type=molecule --name="property-badge"
```

### Build & watch
```bash
# Compile + lint + format check
npm run build

# Dev mode : Vite + Storybook hot reload
npm run watch

# Storybook static build
npm run storybook:build
```

### Validation lint/format
```bash
# Biome check (lint + format)
npx biome check .

# Biome fix (auto-correct)
npx biome check --apply .
```

## Git hooks (si configured)

### Pre-commit
```bash
# Auto-run avant chaque commit
- Biome lint
- Biome format
- CSS compilation check
```

### Pre-push
```bash
# Auto-run avant push
- npm run build (full validation)
- Tests (si présents)
```

## Résolution problèmes

### Build échoue
```bash
# 1. Vérifier erreurs lint
npx biome check .

# 2. Vérifier erreurs CSS
npm run build 2>&1 | grep -A 5 "error"

# 3. Vérifier syntaxe Twig (via Storybook)
npm run watch
# → Console browser pour erreurs rendering
```

### Stories ne s'affichent pas
```bash
# 1. Vérifier tags autodocs
grep "tags" components/**/*.stories.jsx

# 2. Vérifier export default
grep "export default" components/**/*.stories.jsx

# 3. Rebuild Storybook
rm -rf storybook/ && npm run storybook:build
```

### Tokens manquants
```bash
# 1. Vérifier existence
npm run tokens:check -- --my-token

# 2. Si absent, documenter besoin
echo "Token request: --my-token for {usage}" >> docs/tokens-requests.md

# 3. NE PAS ajouter directement dans source/props/
```

## Performance

### Lazy loading images
```twig
{# ✅ CORRECT - Loading lazy #}
<img 
  src="{{ props.src }}" 
  alt="{{ props.alt }}"
  loading="lazy"
  decoding="async"
>
```

### Icon sprite
```html
<!-- ✅ CORRECT - Use sprite, not individual SVGs -->
<span data-icon="check"></span>

<!-- ❌ INTERDIT - Inline SVG pour chaque usage -->
<svg>...</svg>
```

### CSS bundle size
- Vite auto-split par entry points
- PostCSS purge unused (production)
- Minimize nesting pour reduce specificity

## Sécurité

### XSS prevention
```twig
{# ✅ CORRECT - Auto-escaped #}
{{ props.userInput }}

{# ❌ INTERDIT - Raw HTML #}
{{ props.userInput|raw }}
```

### CSRF tokens
```twig
{# ✅ CORRECT - Drupal CSRF #}
<form action="{{ props.action }}" method="post">
  {{ csrf_token }}
  <!-- fields -->
</form>
```

## Documentation

### Storybook Docs
- Autodocs auto-générés depuis argTypes
- Description component dans parameters.docs
- Exemples code via stories sources

### README
- Always include Usage, Props, BEM, Tokens, A11y
- Real-world examples (pas juste Default)
- Link to Storybook story

### Changelog
- Format : `## [Date] - {Component} ({Level})`
- Mises à jour chronologiques
- Link commits importants
