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
❌ PROBLÈME: Utilisation de couleurs arbitraires ou tokens génériques
✅ SOLUTION: Utiliser UNIQUEMENT les tokens sémantiques de brand.css:
- primary = `--brand-primary` (green #00915A)
- secondary = `--brand-secondary` (purple #E0388C)
- success = `--btn-success` (green-600)
- warning = `--btn-warning` (yellow-500)
- danger = `--btn-danger` (red-600)
- info = `--btn-info` (blue-600)

Si le composant a des variantes de couleur, il DOIT supporter TOUTES ces 6 couleurs sémantiques.

### 3. COMPLETE IMPLEMENTATION (Règle critique)
❌ PROBLÈME: Stories ou README incomplets
✅ SOLUTION:
- Stories: Créer une story individuelle pour CHAQUE variant (Default, Primary, Secondary, Success, Warning, Danger, Info, etc.)
- Stories: Créer des showcases groupés (AllStyles, UseCases)
- README: Props table complète, BEM structure, tokens utilisés, exemples d'usage, cas réels
- YAML: Commentaires listant toutes les options disponibles

### 4. MINIMAL HTML OUTPUT (Règle critique)
❌ PROBLÈME: Classes de modifiers ajoutées même pour valeurs par défaut
✅ SOLUTION:
```twig
{%- set root_classes = ['ps-component'] -%}
{%- if variant != 'default' -%}
  {%- set root_classes = root_classes|merge(['ps-component--' ~ variant]) -%}
{%- endif -%}
```
Output par défaut doit être: `<div class="ps-component">`
PAS: `<div class="ps-component ps-component--default ps-component--medium">`

### 5. CSS MODIFIERS INDEPENDENCE (Règle critique)
❌ PROBLÈME: Modifiers qui nécessitent des classes composées
Exemple: `.ps-component--horizontal.ps-component--primary { color: green; }`
✅ SOLUTION: Base class contient defaults, modifiers fonctionnent seuls:
```css
.ps-component {
  color: var(--gray-500); /* default */
}
.ps-component--primary {
  color: var(--brand-primary); /* fonctionne seul */
}
```

### 6. DESIGN TOKENS FROM SPEC (Règle critique)
❌ PROBLÈME: Tokens génériques (--gray-500) ou valeurs en dur (#00915A)
✅ SOLUTION:
- Lire docs/design/[level]/[component].md pour identifier tokens requis
- Utiliser tokens officiels avec fallbacks: `var(--brand-primary, var(--bnp-green))`
- Vérifier que les valeurs hex correspondent exactement au spec

### 7. TWIG CLASS HANDLING (Règle critique)
❌ PROBLÈME: Ajout de classes vides ou undefined
Exemple: `text ? 'class--with-text' : ''` → ajoute chaîne vide dans array
✅ SOLUTION:
```twig
{%- set classes = ['base-class'] -%}
{%- if text -%}
  {%- set classes = classes|merge(['base-class--with-text']) -%}
{%- endif -%}
```

### 8. STORYBOOK PATTERNS (Règle critique)
❌ PROBLÈME: Import Twig avec nom générique, JSX/React dans .stories.jsx
✅ SOLUTION:
```jsx
import componentTwig from './component.twig'; // Nom unique
import data from './component.yml';

export default {
  title: 'Elements/Component',
  tags: ['autodocs'],
  render: (args) => componentTwig(args), // Pas de JSX
};

export const AllStyles = {
  render: () => `
    <div>
      ${componentTwig({ variant: 'primary' })}
      ${componentTwig({ variant: 'secondary' })}
    </div>
  `,
};
```

---

## APRÈS L'AUDIT:

Une fois l'analyse terminée, liste TOUS les problèmes trouvés avec:
1. ❌ Problème spécifique
2. ✅ Solution à appliquer
3. 📄 Fichiers concernés

Puis demande: "Dois-je corriger tous ces points maintenant?"

Quand j'accepte, applique TOUTES les corrections en une seule opération avec multi_replace_string_in_file, puis commit avec message détaillé:
`refactor([component]): apply all component rules - [list of fixes]`

---

## EXEMPLE D'UTILISATION:

"Vérifie la cohérence du composant Button avec nos règles du projet."

Ou pour plusieurs composants:

"Audite les composants Badge, Alert, et Card pour conformité aux règles."
```

---

## WORKFLOW COMPLET:

1. **Audit** → Liste des problèmes
2. **Validation** → Demander confirmation
3. **Fix** → Corrections groupées (multi_replace)
4. **Commit** → Message détaillé
5. **Vérification** → Confirmer conformité 100%
