# 🔧 Simple Component Conformity & Migration Prompt

## 📋 Copy-paste this prompt to audit and fix ANY component

```
Vérifie la cohérence du composant [{COMPONENT_NAME}] en respectant STRICTEMENT toutes les règles du projet.

## AUDIT & CONFORMITY

### 1. Structure des fichiers
- [ ] 5 fichiers présents: {component}.twig, {component}.css, {component}.yml, {component}.stories.jsx, README.md
- [ ] Tous les fichiers nommés correctement (lowercase, kebab-case)

### 2. Template Twig
- [ ] Header comment avec @param pour toutes les props
- [ ] Tous les defaults: {%- set prop = prop|default('value') -%}
- [ ] Classes avec ternary + null: condition ? 'class' : null
- [ ] ❌ JAMAIS: filter(v => v), map(), includes() (incompatible Drupal)
- [ ] Inclusion avec 'only' keyword: {% include '@elements/...' with {...} only %}
- [ ] Composition avec attributes.addClass() (PAS de baseClass)
- [ ] Contexte Real Estate (pas de texte générique)

### 3. CSS Styles
- [ ] ZERO valeurs hardcodées (#00915A, 16px, green) - TOUS les tokens
- [ ] Nesting avec &: .ps-component { &__element { } &--modifier { } }
- [ ] Ordre cascade: Base → Elements → Modifiers → States
- [ ] Couleurs sémantiques UNIQUEMENT (--primary, --secondary, --success, --warning, --danger, --info, --dark, --light)
- [ ] Focus-visible sur tous les interactifs
- [ ] Variables CSS en 3 couches (voir section VARIABLES CSS ci-dessous)

### 4. Storybook
- [ ] tags: ['autodocs'] obligatoire dans export default
- [ ] Import: import componentTwig from './component.twig';
- [ ] Render: render: (args) => componentTwig(args)
- [ ] ❌ PAS de React/JSX
- [ ] ArgTypes catégorisés: Content | Appearance | Behavior | Link | Accessibility | Layout
- [ ] Stories: Default + Showcases (AllColors, AllSizes, UseCases)
- [ ] ❌ PAS de stories individuelles (Primary, Secondary, Small...)

### 5. YAML Data
- [ ] Format YAML valide
- [ ] Contexte Real Estate réaliste
- [ ] Tous les props requis définis

### 6. README.md
- [ ] Section: Props (table avec nom | type | default | description)
- [ ] Section: BEM Structure (tree format)
- [ ] Section: Usage (exemple Twig)
- [ ] Section: Design Tokens (tokens utilisés)
- [ ] Section: Accessibility (checklist WCAG AA)
- [ ] Section: Examples (2-3 cas d'usage)

### 7. BEM Naming
- [ ] Préfixe 'ps-' OBLIGATOIRE: .ps-badge, .ps-badge__text, .ps-badge--primary
- [ ] Format: .ps-{block}__{element}--{modifier}
- [ ] ❌ PAS d'éléments imbriqués: .ps-badge__container__icon

### 8. Accessibility (WCAG 2.2 AA)
- [ ] Contraste: 4.5:1 texte, 3:1 UI components
- [ ] Focus-visible sur tous les interactifs
- [ ] ARIA: aria-label, aria-hidden, roles si nécessaire
- [ ] Sémantique HTML: <button>, <a>, <input>, NOT <div>

---

## 🎨 HARMONISATION DES COULEURS

Vérifier et harmoniser TOUS les modifiers de couleur du composant.
Si la couleur existe, elle DOIT utiliser les tokens sémantiques.

Couleurs disponibles (si le prop 'color' existe):
- default (couleur du texte par défaut)
- primary (vert #00915A)
- secondary (rose #A12B66)
- info (bleu #2563EB)
- warning (jaune #FBBF24)
- success (teal #198754)
- danger (rouge #EB3636)
- dark (gris foncé)
- light (gris clair)

### Twig: Vérifier
- [ ] Prop 'color' défini avec default correct
- [ ] Tous les modifiers color: color != 'default' ? 'ps-{component}--' ~ color : null

### CSS: Vérifier
- [ ] .ps-{component}--primary { color: var(--primary); }
- [ ] .ps-{component}--secondary { color: var(--secondary); }
- [ ] .ps-{component}--info { color: var(--info); }
- [ ] .ps-{component}--warning { color: var(--warning); }
- [ ] .ps-{component}--success { color: var(--success); }
- [ ] .ps-{component}--danger { color: var(--danger); }
- [ ] .ps-{component}--dark { color: var(--dark); }
- [ ] .ps-{component}--light { color: var(--light); }

### Stories: Vérifier
- [ ] ArgType 'color' avec options: ['default', 'primary', 'secondary', 'info', 'warning', 'success', 'danger', 'dark', 'light']
- [ ] Story AllColors montrant toutes les variantes

### README: Vérifier
- [ ] Props table avec toutes les couleurs listées

---

## 📏 HARMONISATION DES TAILLES

Vérifier et harmoniser TOUS les modifiers de taille du composant.
Si la taille existe, elle DOIT utiliser les tailles standards.

Tailles disponibles (si le prop 'size' existe):
- xs (extra small)
- sm (small)
- md (medium) - DEFAULT
- lg (large)
- xl (extra large)
- xxl (extra extra large)

### Twig: Vérifier
- [ ] Prop 'size' défini avec default: md
- [ ] Tous les modifiers size: size != 'md' ? 'ps-{component}--' ~ size : null

### CSS: Vérifier
- [ ] .ps-{component}--xs { font-size: var(--font-size--2); }
- [ ] .ps-{component}--sm { font-size: var(--font-size--1); }
- [ ] .ps-{component}--lg { font-size: var(--font-size-2); }
- [ ] .ps-{component}--xl { font-size: var(--font-size-3); }
- [ ] .ps-{component}--xxl { font-size: var(--font-size-4); }

### Stories: Vérifier
- [ ] ArgType 'size' avec options: ['xs', 'sm', 'md', 'lg', 'xl', 'xxl']
- [ ] Story AllSizes montrant toutes les variantes

### README: Vérifier
- [ ] Props table avec toutes les tailles listées

---

## 🎭 SYSTÈME DES ICONES (NEW)

Migration obligatoire vers: @elements/icon/icon.twig

### Vérifier existence des icones
Avant d'utiliser une icone, vérifier qu'elle existe dans:
- Fichier registre (auto-généré): `source/patterns/documentation/icons-registry.json`
- OU: Storybook → Elements/Icon → voir liste complète dans les argTypes du select 'icon'

### Utilisation correcte
```twig
{# ✅ CORRECT #}
{% include '@elements/icon/icon.twig' with {
  name: 'check',      {# PAS icon-check, juste 'check' #}
  size: 'md',
  color: 'default',
  attributes: create_attribute().addClass('ps-{component}__icon')
} only %}

{# ❌ INCORRECT #}
{# - name: 'icon-check' (avec préfixe) #}
{# - baseClass: 'ps-{component}__icon' (SUPPRIMÉ en v4.0.0) #}
{# - data-icon="{{ icon }}" (ancien système) #}
```

### Twig: Vérifier & Migrer
- [ ] Toutes les icones utilisent @elements/icon/icon.twig
- [ ] Noms d'icones SANS préfixe 'icon-'
- [ ] Pas de baseClass (utiliser attributes.addClass())
- [ ] Pas d'ancien système data-icon

### CSS: Vérifier
- [ ] Pas de propriétés [data-icon] custom (c'est dans source/props/icons.css)
- [ ] Classes .ps-{component}__icon définies si needed

---

## 📚 SYSTÈME DE VARIABLES CSS (3 COUCHES)

Migration obligatoire vers le système 3-couches.

### Structure 3-couches:
```
Layer 1: Tokens de base (source/props/*.css)
  --primary, --secondary, --size-2, --font-size-1, etc.

Layer 2: Variables du composant (dans le .css du composant)
  --ps-{component}-size: var(--size-4);
  --ps-{component}-color: var(--primary);

Layer 3: Utilisation dans les styles
  padding: var(--ps-{component}-size);
  color: var(--ps-{component}-color);
```

### CSS: Vérifier & Migrer
- [ ] Aucune valeur hardcodée (--primary est OK, #00915A est INTERDIT)
- [ ] Aucun direct Layer 1 dans les propriétés CSS (utiliser Layer 2)

Exemple CORRECT:
```css
.ps-badge {
  --ps-badge-padding: var(--size-2);
  --ps-badge-font-size: var(--font-size-0);
  
  padding: var(--ps-badge-padding);
  font-size: var(--ps-badge-font-size);
}

.ps-badge--primary {
  --ps-badge-color: var(--primary);
  color: var(--ps-badge-color);
}
```

Exemple INCORRECT:
```css
.ps-badge {
  padding: var(--size-2);        {# Direct Layer 1 #}
  color: #00915A;                {# Hardcoded! #}
}
```

---

## ✅ À LA FIN: RAPPORT COMPLET

Liste TOUS les problèmes trouvés avec:
- Fichier affecté
- Ligne(s) concernée(s)
- Problème exact
- Action corrective

Puis, CORRIGE AUTOMATIQUEMENT chaque problème et montre le code corrigé.

Format du rapport:
```
## Problèmes de Conformité: [{COMPONENT_NAME}]

### ❌ Problèmes trouvés (N)

1. **[Catégorie] - Description**
   - Fichier: source/patterns/.../{file}
   - Lignes: X-Y
   - Problème: Explication détaillée
   - Action: Comment corriger

### ✅ Corrections automatiques appliquées

1. **[Fichier]** - [Description du changement]
   ```
   Avant:
   [code incorrect]
   
   Après:
   [code corrigé]
   ```

2. ... (pour chaque correction)

### 📊 Résumé final

✅ Conformité: X/X checks
✅ Couleurs: Harmonisées (X variantes)
✅ Tailles: Harmonisées (X variantes)
✅ Icones: Migrées (X icones)
✅ Variables CSS: Système 3-couches appliqué
```

---

## 🚀 À exécuter dans cet ordre:

1. **AUDIT** - Lister tous les problèmes
2. **MIGRATIONS** - Appliquer couleurs, tailles, icones, variables CSS
3. **CORRECTIONS** - Fixer tous les problèmes trouvés
4. **RAPPORT** - Montrer ce qui a été fait

N'oublie pas: Un composant est conforme seulement si TOUS les problèmes sont corrigés!
```

---

## 📝 Comment utiliser ce prompt

### Pour Badge :
```
Vérifie la cohérence du composant [Badge] en respectant STRICTEMENT toutes les règles du projet.

[COLLER LE PROMPT COMPLET CI-DESSUS]
```

### Pour Button :
```
Vérifie la cohérence du composant [Button] en respectant STRICTEMENT toutes les règles du projet.

[COLLER LE PROMPT COMPLET CI-DESSUS]
```

### Pour n'importe quel composant :
```
Vérifie la cohérence du composant [{NOM_COMPOSANT}] en respectant STRICTEMENT toutes les règles du projet.

[COLLER LE PROMPT COMPLET CI-DESSUS]
```

---

## 💡 Ce que ce prompt fait

✅ **Audit complet** - 8 catégories de vérification  
✅ **Harmonisation** - Couleurs et tailles standardisées  
✅ **Migration icones** - Conversion vers nouveau système  
✅ **Migration CSS** - Système 3-couches appliqué  
✅ **Corrections automatiques** - Tous les bugs fixés  
✅ **Rapport détaillé** - Avant/après avec code  

**Résultat**: Un composant conforme à 100% et prêt pour production! 🚀
