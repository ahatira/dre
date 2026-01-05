# Documentation Verification Checklist

**Version**: 1.0.0  
**Date**: 2025-12-13  
**Usage**: Prompt systématique pour vérifier la conformité complète d'une documentation de composant

---

## 🎯 Instructions de vérification

Pour chaque composant sous `docs/02-composants/01-atomes/{component}.md`, vérifier **DANS CET ORDRE** :

---

## ÉTAPE 1 : SIMPLIFICATION HTML (PRIORITÉ ABSOLUE)

### 1.1 Markup minimal
- [ ] **Wrapper conditionnel** : Éléments `__text`, `__icon`, etc. présents UNIQUEMENT si nécessaires
- [ ] **data-icon direct** : Icônes via `data-icon` sur conteneur (PAS de `<span class="ps-{component}__icon">`)
- [ ] **Pseudo-elements** : Vérifier que data-icon utilise `::before`/`::after` (pas de markup supplémentaire)
- [ ] **Niveaux DOM minimaux** : Pas de wrappers superflus (référence: Badge = markup direct)
- [ ] **Principe atomic** : Un atome = markup le plus simple possible

### 1.2 Comparaison implémentation réelle
- [ ] Lire `source/patterns/elements/{component}/{component}.twig` COMPLET
- [ ] Comparer structure Twig doc vs implémentation réelle
- [ ] **IMPORTANT** : Si différences, la doc doit refléter l'implémentation

**Exemples de simplifications** :
```twig
{# ❌ FAUX - Wrapper superflu #}
<span class="ps-eyebrow">
  <span class="ps-eyebrow__icon" data-icon="star"></span>
  <span class="ps-eyebrow__text">Texte</span>
</span>

{# ✅ CORRECT - Markup minimal #}
<span class="ps-eyebrow" data-icon="star">Texte</span>
```

---

## ÉTAPE 2 : CONFORMITÉ TOKENS CSS

### 2.1 Préfixe obligatoire
- [ ] **TOUS** les tokens component-scoped ont préfixe `--ps-{component}-*`
- [ ] Exemple: `--ps-button-bg`, `--ps-heading-font-size`, `--ps-divider-color`
- [ ] **Aucune exception** : `--button-*` ou `--heading-*` = ERREUR

### 2.2 Cohérence avec implémentation
- [ ] Lire `source/patterns/elements/{component}/{component}.css` (lignes 1-50 minimum)
- [ ] Vérifier que TOUS les tokens doc correspondent au CSS réel
- [ ] Vérifier valeurs par défaut (ex: h1 = `--font-size-17`, pas `--font-size-13`)

### 2.3 Tokens globaux (Layer 1)
- [ ] Vérifier que tokens `--size-*`, `--font-*`, `--primary`, etc. existent dans `source/props/`
- [ ] Documenter quels tokens globaux sont utilisés

---

## ÉTAPE 3 : BEM CONFORMITÉ

### 3.1 Classes block
- [ ] Block principal : `.ps-{component}` (préfixe OBLIGATOIRE)
- [ ] Éléments : `.ps-{component}__{element}` (double underscore)
- [ ] Modifiers : `.ps-{component}--{modifier}` (double dash)

### 3.2 Modifiers conditionnels
- [ ] **Vérifier Twig** : Modifiers appliqués UNIQUEMENT si différents de la valeur par défaut
- [ ] Exemple: `level != 'h1' ? 'ps-heading--' ~ level : null` (h1 = défaut, pas de modifier)
- [ ] **Classes défaut** : Documenter quelle variante n'a PAS de modifier CSS

### 3.3 Ordre cascade CSS
- [ ] Base styles en premier
- [ ] Modifiers après (tailles, couleurs, états)
- [ ] États pseudo-classes en dernier (`:hover`, `:focus-visible`, `:disabled`)

---

## ÉTAPE 4 : PROPS YAML

### 4.1 Propriétés complètes
- [ ] Comparer props doc vs `source/patterns/elements/{component}/{component}.yml`
- [ ] Vérifier que TOUTES les props de l'implémentation sont documentées
- [ ] Vérifier enums (ex: size: `small|medium|large`, PAS `xs|sm|md|lg|xl`)

### 4.2 Valeurs par défaut
- [ ] `default:` correspond à l'implémentation Twig
- [ ] Exemple: `level: { default: 'h1' }` si Twig dit `level|default('h1')`

### 4.3 Props requises
- [ ] `required: []` ou `required: ['text', 'icon']` selon implémentation
- [ ] **Attention** : `attributes` jamais requis (optionnel par nature)

---

## ÉTAPE 5 : TEMPLATE TWIG

### 5.1 Defaults corrects
- [ ] Tous les `{% set var = var|default('value') %}` correspondent aux props YAML
- [ ] Exemple: Si YAML dit `default: 'h1'`, Twig doit avoir `level|default('h1')`

### 5.2 Ternary + null (pas de chaînes vides)
- [ ] `condition ? 'class' : null` (PAS `condition ? 'class' : ''`)
- [ ] Arrays de classes avec `null` values filtrés par `|trim`

### 5.3 Attributes Drupal
- [ ] **OBLIGATOIRE** : `{{ attributes|without('class') }}` dans TOUS les composants
- [ ] Position correcte : après `class="{{ classes... }}"` dans la balise ouvrante

### 5.4 Includes avec `only`
- [ ] Tous les `{% include %}` ont le flag `only` : `{% include '@elements/icon/icon.twig' with {...} only %}`

### 5.5 Pas de JavaScript Twig
- [ ] **INTERDIT** : `.map()`, `.filter()`, `.includes()`, arrow functions `v => v`
- [ ] Utiliser ternary : `items ? items : []` au lieu de `items|filter(v => v)`

---

## ÉTAPE 6 : VARIANTS

### 6.1 Énumération complète
- [ ] Tous les modifiers CSS documentés (tailles, couleurs, états)
- [ ] Exemples HTML pour CHAQUE variant

### 6.2 Couleurs sémantiques
- [ ] **Utiliser noms sémantiques** : `primary`, `success`, `danger` (PAS `green`, `red`)
- [ ] Référence table copilot-instructions.md : 8 couleurs sémantiques + `neutral`

### 6.3 États interactifs
- [ ] Documenter `:hover`, `:focus-visible`, `:disabled` si applicable
- [ ] Vérifier contraste WCAG AA (4.5:1 minimum)

---

## ÉTAPE 7 : ACCESSIBILITÉ

### 7.1 WCAG 2.2 AA minimum
- [ ] Contraste texte/fond : 4.5:1 (ou 3:1 pour large text)
- [ ] Focus visible : Outline 2px minimum avec offset
- [ ] ARIA attributes : `aria-label`, `aria-hidden`, `aria-invalid`, etc.

### 7.2 Keyboard navigation
- [ ] Éléments interactifs accessibles au clavier (Tab, Enter, Space)
- [ ] Focus visible sur `:focus-visible` (pas `:focus`)

### 7.3 Screen readers
- [ ] Icônes décoratives : `aria-hidden="true"`
- [ ] Icônes informatives : `aria-label="Description"`
- [ ] Labels explicites (pas uniquement placeholder)

---

## ÉTAPE 8 : EXEMPLES

### 8.1 Paths includes corrects
- [ ] `@elements/icon/icon.twig` (PAS `@ps_theme/ps-icon/ps-icon.twig`)
- [ ] Pattern : `@{level}/{component}/{component}.twig`
- [ ] Levels : `elements`, `components`, `collections`, `layouts`, `pages`

### 8.2 Flag `only`
- [ ] Tous les includes : `{% include '...' with {...} only %}`

### 8.3 Exemples variés
- [ ] Au moins 3-5 exemples couvrant variants principaux
- [ ] HTML + Twig (production) + Twig (Storybook si applicable)

---

## ÉTAPE 9 : METADATA

### 9.1 Header
- [ ] Statut : `✅ Stable` ou `🚧 Draft` (avec justification)
- [ ] Version : Numéro cohérent (1.0.0 pour stable)
- [ ] Date : `Dernière mise à jour : 13 décembre 2025`

### 9.2 Checklist conformité
- [ ] Section `## ✅ Checklist conformité` présente
- [ ] 3 sous-sections : Implémentation / Accessibilité / Standards
- [ ] Toutes les cases cochées `[x]` si composant conforme

### 9.3 Statut final
- [ ] Bloc final avec : `**Statut** : ✅ **100% CONFORME**`
- [ ] Référence implémentation : `source/patterns/elements/{component}/`

---

## ÉTAPE 10 : VÉRIFICATIONS SPÉCIALES

### 10.1 Icon system
- [ ] **data-icon direct** : Icônes sur conteneur (PAS de span __icon séparé)
- [ ] Exception atoms rendering : Icon.twig = wrapper Storybook uniquement
- [ ] Production : `data-icon` directement sur HTML

### 10.2 Composition (molecules/organisms)
- [ ] **Token-First workflow** : Override tokens du composant enfant via CSS parent
- [ ] **INTERDIT** : `baseClass` variable pour composition
- [ ] Utiliser `attributes.addClass()` pour classes additionnelles

### 10.3 Composants inexistants
- [ ] Vérifier existence : `source/patterns/elements/{component}/`
- [ ] Si absent : **SUPPRIMER** la doc (ex: field.md → composant n'existe pas)

---

## ⚠️ RÈGLES ZERO TOLERANCE (échec immédiat)

1. ❌ **Hardcoded values** : `#00915A`, `16px`, `150ms ease` → Utiliser tokens
2. ❌ **Missing files** : 4 fichiers requis (`.twig`, `.css`, `.yml`, `.stories.jsx`)
3. ❌ **Missing attributes** : `{{ attributes|without('class') }}` OBLIGATOIRE dans Twig
4. ❌ **Arrow functions Twig** : `.map()`, `.filter(v => v)` → Drupal incompatible
5. ❌ **Color names** : `green`, `red` → Utiliser `success`, `danger`
6. ❌ **Icon prefix** : `icon-check` → Utiliser `check` (préfixe auto-ajouté)
7. ❌ **baseClass composition** : INTERDIT → Utiliser `attributes.addClass()`
8. ❌ **Modifier combinations** : `.ps-badge--a.ps-badge--b` → Chaque doit fonctionner seul
9. ❌ **Cascade inversé** : Modifiers avant base → Base FIRST, modifiers après
10. ❌ **Missing focus-visible** : Tous interactifs DOIVENT avoir focus visible
11. ❌ **Editing props/*.css** : INTERDIT → Proposer tokens via processus séparé
12. ❌ **Missing ps- prefix** : Tokens component-scoped DOIVENT avoir `--ps-{component}-*`

---

## 📋 PROMPT DE VÉRIFICATION COMPLET

**Copier-coller ce prompt pour chaque composant** :

```
Vérifie la documentation `docs/02-composants/01-atomes/{COMPONENT}.md` avec cette checklist :

ÉTAPE 1 - SIMPLIFICATION HTML (PRIORITÉ ABSOLUE) :
1. Lire source/patterns/elements/{COMPONENT}/{COMPONENT}.twig COMPLET
2. Comparer markup doc vs implémentation réelle
3. Vérifier wrappers conditionnels (uniquement si nécessaires)
4. Vérifier data-icon direct sur conteneur (pas de span __icon séparé)
5. Identifier toute simplification possible (référence: Badge = markup minimal)

ÉTAPE 2 - TOKENS CSS :
1. Lire source/patterns/elements/{COMPONENT}/{COMPONENT}.css (lignes 1-100 minimum)
2. Vérifier TOUS les tokens ont préfixe --ps-{COMPONENT}-*
3. Vérifier valeurs par défaut correspondent au CSS réel
4. Documenter tokens globaux (Layer 1) utilisés

ÉTAPE 3 - BEM :
1. Vérifier préfixe .ps-{COMPONENT} sur toutes les classes
2. Vérifier modifiers conditionnels (defaults sans modifier CSS)
3. Vérifier ordre cascade (base → modifiers → pseudo-classes)

ÉTAPE 4 - PROPS YAML :
1. Lire source/patterns/elements/{COMPONENT}/{COMPONENT}.yml
2. Comparer props doc vs YAML réel (toutes présentes ?)
3. Vérifier enums (xs|sm|md|lg, pas small|medium|large)
4. Vérifier defaults correspondent à Twig

ÉTAPE 5 - TEMPLATE TWIG :
1. Vérifier tous defaults correspondent aux props YAML
2. Vérifier ternary + null (pas de chaînes vides)
3. Vérifier {{ attributes|without('class') }} présent
4. Vérifier tous includes ont flag 'only'
5. Vérifier pas de .map(), .filter(), arrow functions

ÉTAPE 6 - VARIANTS :
1. Documenter TOUS les modifiers CSS
2. Utiliser couleurs sémantiques (primary/success/danger, pas green/red)
3. Exemples HTML pour chaque variant

ÉTAPE 7 - ACCESSIBILITÉ :
1. Vérifier contraste WCAG AA (4.5:1)
2. Vérifier focus-visible (outline 2px minimum)
3. Vérifier ARIA (aria-label, aria-hidden, aria-invalid)
4. Vérifier keyboard navigation

ÉTAPE 8 - EXEMPLES :
1. Vérifier paths includes corrects (@elements/{component}/{component}.twig)
2. Vérifier flag 'only' sur tous includes
3. Au moins 3-5 exemples variés

ÉTAPE 9 - METADATA :
1. Statut correct (Stable/Draft)
2. Checklist conformité présente et complète
3. Bloc statut final avec référence implémentation

ÉTAPE 10 - VÉRIFICATIONS SPÉCIALES :
1. data-icon direct (pas de span __icon séparé)
2. Pas de baseClass pour composition
3. Composant existe dans source/patterns/elements/

ZERO TOLERANCE :
- Hardcoded values → FAIL
- Missing attributes parameter → FAIL
- Arrow functions Twig → FAIL
- Color names (green/red) → FAIL
- Missing ps- prefix tokens → FAIL
- baseClass composition → FAIL

Applique TOUTES les corrections nécessaires via multi_replace_string_in_file.
Commence par ÉTAPE 1 (simplification HTML).
Génère un résumé final avec nombre de corrections.
```

---

## 📊 EXEMPLE DE RÉSUMÉ

Après vérification, fournir ce format :

```
✅ {COMPONENT}.md finalisé !

Corrections appliquées ({N} corrections totales) :
- ✅ Simplification HTML : {description}
- ✅ Tokens : {nombre} corrections (préfixe --ps-*)
- ✅ BEM : {corrections classes/modifiers}
- ✅ Props : {ajouts/corrections}
- ✅ Template Twig : {corrections}
- ✅ Variants : {corrections enums/couleurs}
- ✅ Accessibilité : {corrections WCAG}
- ✅ Exemples : {corrections paths/flag only}
- ✅ Metadata : {statut/checklist}

Résumé {COMPONENT}.md :
- {caractéristique 1}
- {caractéristique 2}
- {caractéristique 3}
- 100% conforme

Prochaine vérification : {NEXT_COMPONENT}.md
```

---

**Note** : Ce checklist est exhaustif. Certains points peuvent ne pas s'appliquer à tous les composants (ex: icon system uniquement pour composants avec icônes).
