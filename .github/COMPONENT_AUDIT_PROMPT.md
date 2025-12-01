> DEPRECATED — Use `.github/COMPONENT_CONFORMITY_PROMPT.md`

This file is deprecated in favor of `COMPONENT_CONFORMITY_PROMPT.md`, which is the single canonical prompt for auditing and fixing components. Please switch to the new file to avoid divergence.

Why:
- Single source of truth for audits
- Reduced maintenance and inconsistencies

Quick usage: "Vérifie la cohérence du composant [Name] en respectant STRICTEMENT toutes les règles du projet" → see full scoring and steps in the new file.

# Component Audit & Compliance Prompt

Use this prompt to automatically audit and fix any component to ensure 100% compliance with project rules.

---

## PROMPT:

```
Vérifie la cohérence du composant [COMPONENT_NAME] avec nos règles du projet.

Analyse ces fichiers:
1. source/patterns/[level]/[component]/[component].twig
2. source/patterns/[level]/[component]/[component].css
3. source/patterns/[level]/[component]/[component].yml
4. source/patterns/[level]/[component]/[component].stories.jsx
5. source/patterns/[level]/[component]/README.md
6. docs/design/[level]/[component].md (spec)

Vérifie TOUS ces points critiques:

### 1. ICONS IN CSS (Règle critique)
❌ PROBLÈME: Présence de <i>, <svg>, ou autres éléments HTML pour icônes dans le .twig
✅ SOLUTION: 
- Remplacer par <span class="ps-[component]__icon"></span>
- Ajouter CSS: 
  ```css
  .ps-[component]__icon {
    font-family: 'bnpre-icons';
    font-style: normal;
    line-height: 1;
  }
  .ps-[component]__icon::before {
    content: var(--ps-[component]-icon, "\e800");
  }
  ```

### 2. SEMANTIC COLOR NAMING (Règle critique)
❌ PROBLÈME: Utilisation de noms de couleurs arbitraires (green, purple, blue, red, etc.) au lieu de noms sémantiques
✅ SOLUTION: Utiliser UNIQUEMENT les noms sémantiques standardisés:
- **primary** = `--brand-primary` (green #00915A) - Action principale
- **secondary** = `--brand-secondary` (purple #E0388C) - Action secondaire
- **success** = `--btn-success` (green-600) - Succès/validation
- **warning** = `--btn-warning` (yellow-500) - Avertissement
- **danger** = `--btn-danger` (red-600) - Erreur/danger
- **info** = `--btn-info` (blue-600) - Information

**INTERDICTIONS:**
- ❌ PAS de `color: 'green'` → ✅ `color: 'primary'`
- ❌ PAS de `color: 'purple'` → ✅ `color: 'secondary'`
- ❌ PAS de `color: 'blue'` → ✅ `color: 'info'`
- ❌ PAS de `color: 'red'` → ✅ `color: 'danger'`
- ❌ PAS de `color: 'yellow'` → ✅ `color: 'warning'`

**RÈGLES:**
1. Les props DOIVENT accepter les noms sémantiques (primary, secondary, success, warning, danger, info)
2. Les classes BEM DOIVENT utiliser les noms sémantiques (ps-component--primary, ps-component--secondary)
3. Les tokens CSS DOIVENT mapper vers brand.css (--brand-primary, --brand-secondary, --btn-success, etc.)
4. Si le composant a des variantes de couleur, il DOIT supporter TOUTES les 6 couleurs sémantiques
5. La documentation (README, stories, YAML) DOIT utiliser exclusivement les noms sémantiques

**EXEMPLE DE CORRECTION:**
```yaml
# ❌ AVANT (non-conforme)
color: 'green'  # Options: green | purple | blue | red

# ✅ APRÈS (conforme)
color: 'primary'  # Options: primary | secondary | success | warning | danger | info
```

```css
/* ❌ AVANT (non-conforme) */
.ps-component--green { color: var(--bnp-green); }
.ps-component--purple { color: var(--bnp-accent-pink); }

/* ✅ APRÈS (conforme) */
.ps-component--primary { color: var(--brand-primary); }
.ps-component--secondary { color: var(--brand-secondary); }
```

### 3. COMPLETE IMPLEMENTATION (Règle critique)
❌ PROBLÈME: Stories ou README incomplets
✅ SOLUTION:
- Stories: Créer une story individuelle pour CHAQUE variant (Default, Primary, Secondary, Success, Warning, Danger, Info, etc.)
- Stories: Créer des showcases groupés (AllStyles, UseCases)
- README: Props table complète, BEM structure, tokens utilisés, exemples d'usage, cas réels
- YAML: Commentaires listant toutes les options disponibles

### 4. MINIMAL HTML OUTPUT (Règle critique)

❌ PROBLÈME: Classes de modifiers ajoutées même pour valeurs par défaut
