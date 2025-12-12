# Composition de Composants

**Méthodologie Token-First** : Composer des composants sans modifier les parents

---

## 🎯 Principe fondamental

**Token-First Composition Workflow** = Réutiliser des composants existants en **overridant leurs tokens CSS** (Layer 3) plutôt que de modifier leur CSS directement.

**3 niveaux de cascade** :
- **Layer 1** (Foundation) : Tokens globaux (`source/props/*.css`)
- **Layer 2** (Defaults) : Variables component-scoped (dans le CSS du composant)
- **Layer 3** (Overrides) : Overrides contextuels (CSS du composant parent)

---

## 📐 Token-First Workflow (3 STEPs)

### STEP 1 : Identifier le besoin

**Exemple** : Card (molecule) veut inclure Button (atom) avec **padding réduit**

**❌ Anti-pattern** : Modifier `button.css` directement
```css
/* ❌ NE JAMAIS FAIRE */
.ps-button {
  padding: var(--size-1) var(--size-2); /* Modification globale ! */
}
```

**✅ Pattern** : Override dans le contexte de Card

---

### STEP 2 : Vérifier les tokens exposés

**Consulter le CSS du composant enfant** (`button.css`) :

```css
.ps-button {
  /* ═══ Component-scoped variables (Layer 2) ═══ */
  --ps-button-padding-x: var(--size-6);
  --ps-button-padding-y: var(--size-3);
  
  /* ═══ Base styles ═══ */
  padding: var(--ps-button-padding-y) var(--ps-button-padding-x);
}
```

**Tokens disponibles** : `--ps-button-padding-x`, `--ps-button-padding-y`

---

### STEP 3 : Override dans le parent (Preferred)

**Dans `card.css` (composant parent)** :

```css
.ps-card {
  /* ═══ Override des tokens enfants (Layer 3) ═══ */
  --ps-button-padding-x: var(--size-4); /* Au lieu de --size-6 */
  --ps-button-padding-y: var(--size-2); /* Au lieu de --size-3 */
}
```

**Résultat** : Tous les boutons **dans** Card auront padding réduit, **sans modifier button.css**.

---

### STEP 3 (Alternative) : Classe contextuelle

**Si override global trop large, utiliser classe BEM** :

```css
.ps-card {
  /* Override pour boutons dans CTA zone seulement */
  &__cta .ps-button {
    --ps-button-padding-x: var(--size-4);
    --ps-button-padding-y: var(--size-2);
  }
}
```

**Template Twig** :
```twig
<div class="ps-card__cta">
  {% include '@elements/button/button.twig' with {
    text: 'Voir le bien',
    variant: 'primary',
  } only %}
</div>
```

---

## 🧩 Exemples pratiques

### Exemple 1 : Badge + Icon (Atom dans Atom)

**Badge** inclut **Icon**, mais veut taille `xs` par défaut.

**badge.twig** :
```twig
{% if icon %}
  {% include '@elements/icon/icon.twig' with {
    icon: icon,
    size: 'xs', {# Taille fixée à xs #}
  } only %}
{% endif %}
```

**badge.css** (si besoin d'override tokens) :
```css
.ps-badge {
  /* Override size token pour icon */
  --ps-icon-size: var(--size-3); /* xs = 12px */
  
  /* OU colorer l'icon comme le badge */
  --ps-icon-color: currentColor; /* Hérite la couleur du badge */
}
```

**Résultat** : Icon dans Badge = 12px, couleur = couleur du badge.

---

### Exemple 2 : Card + Button (Molecule avec Atom)

**Card** veut Button avec :
- Padding réduit
- Largeur 100% (pleine largeur)
- Couleur primary par défaut

**card.css** :
```css
.ps-card {
  /* ═══ Component-scoped variables ═══ */
  --ps-card-padding: var(--size-6);
  --ps-card-gap: var(--size-4);
  
  /* ═══ Base styles ═══ */
  padding: var(--ps-card-padding);
  display: flex;
  flex-direction: column;
  gap: var(--ps-card-gap);
  
  /* ═══ Override Button dans CTA zone (Layer 3) ═══ */
  &__cta {
    display: flex;
    gap: var(--size-2);
    
    /* Button overrides */
    .ps-button {
      --ps-button-padding-x: var(--size-4); /* Réduit (vs --size-6) */
      --ps-button-padding-y: var(--size-2); /* Réduit (vs --size-3) */
      flex: 1; /* Pleine largeur */
    }
  }
}
```

**card.twig** :
```twig
<article class="ps-card">
  <div class="ps-card__content">
    <h3 class="ps-card__title">{{ title }}</h3>
    <p class="ps-card__description">{{ description }}</p>
  </div>
  
  {% if cta_text %}
    <div class="ps-card__cta">
      {% include '@elements/button/button.twig' with {
        text: cta_text,
        variant: 'primary',
      } only %}
    </div>
  {% endif %}
</article>
```

**Résultat** : Button dans Card = padding réduit + pleine largeur, **sans modifier button.css**.

---

### Exemple 3 : Alert + Icon + Button (Organism)

**Alert** compose Icon + Button avec styles spécifiques.

**alert.css** :
```css
.ps-alert {
  /* ═══ Component-scoped variables ═══ */
  --ps-alert-padding: var(--size-4);
  --ps-alert-gap: var(--size-3);
  --ps-alert-bg: var(--info-bg-subtle);
  --ps-alert-color: var(--info-text-emphasis);
  --ps-alert-border: var(--info-border);
  
  /* ═══ Base styles ═══ */
  display: flex;
  align-items: flex-start;
  gap: var(--ps-alert-gap);
  padding: var(--ps-alert-padding);
  border: var(--border-size-1) solid var(--ps-alert-border);
  border-radius: var(--radius-4);
  background: var(--ps-alert-bg);
  color: var(--ps-alert-color);
  
  /* ═══ Override Icon (Layer 3) ═══ */
  .ps-icon {
    --ps-icon-size: var(--size-5); /* 20px (vs 16px défaut) */
    --ps-icon-color: var(--ps-alert-color); /* Couleur alert */
    flex-shrink: 0; /* Ne pas réduire */
  }
  
  /* ═══ Override Button (Layer 3) ═══ */
  &__actions .ps-button {
    --ps-button-padding-x: var(--size-3);
    --ps-button-padding-y: var(--size-105);
    --ps-button-font-size: var(--font-size-0); /* Plus petit */
  }
  
  /* ═══ Variants (semantic colors) ═══ */
  &--success {
    --ps-alert-bg: var(--success-bg-subtle);
    --ps-alert-color: var(--success-text-emphasis);
    --ps-alert-border: var(--success-border);
  }
  
  &--danger {
    --ps-alert-bg: var(--danger-bg-subtle);
    --ps-alert-color: var(--danger-text-emphasis);
    --ps-alert-border: var(--danger-border);
  }
}
```

**alert.twig** :
```twig
<div class="ps-alert ps-alert--{{ variant }}">
  {% if icon %}
    {% include '@elements/icon/icon.twig' with {
      icon: icon,
    } only %}
  {% endif %}
  
  <div class="ps-alert__content">
    {% if title %}
      <h4 class="ps-alert__title">{{ title }}</h4>
    {% endif %}
    <p class="ps-alert__message">{{ message }}</p>
  </div>
  
  {% if dismiss or actions %}
    <div class="ps-alert__actions">
      {% if dismiss %}
        {% include '@elements/button/button.twig' with {
          text: 'Fermer',
          variant: 'ghost',
          size: 'sm',
          icon: 'x',
        } only %}
      {% endif %}
    </div>
  {% endif %}
</div>
```

**Résultat** : Icon 20px + Button petit, couleurs cohérentes avec variant.

---

## 🚫 Anti-patterns à éviter

### ❌ 1. Modifier le composant parent directement

```css
/* ❌ MAUVAIS : Modifier button.css pour Card */
.ps-button {
  /* Si dans Card, padding réduit */
  .ps-card & {
    padding: var(--size-2) var(--size-4);
  }
}
```

**Pourquoi** : Button ne doit pas connaître ses parents (couplage fort).

**✅ Solution** : Override dans `card.css` (parent connaît enfant, pas l'inverse).

---

### ❌ 2. Utiliser `baseClass` parameter

```twig
{# ❌ MAUVAIS : Paramètre baseClass #}
{% include '@elements/button/button.twig' with {
  text: 'Action',
  baseClass: 'ps-card__button', {# INTERDIT #}
} only %}
```

**Pourquoi** : Casse le principe Token-First, force modifications CSS externes.

**✅ Solution** : Utiliser `attributes.addClass()` + override tokens.

---

### ❌ 3. Hardcoder styles dans parent

```css
/* ❌ MAUVAIS : Hardcoder dans card.css */
.ps-card__cta .ps-button {
  padding: 8px 16px; /* Hardcodé ! */
  font-size: 14px;
}
```

**Pourquoi** : Viole Token-First, non maintenable.

**✅ Solution** : Override tokens seulement.

```css
/* ✅ CORRECT */
.ps-card__cta .ps-button {
  --ps-button-padding-x: var(--size-4);
  --ps-button-padding-y: var(--size-2);
  --ps-button-font-size: var(--font-size-0);
}
```

---

### ❌ 4. Classes utilitaires ad-hoc

```twig
{# ❌ MAUVAIS : Classes utilitaires inline #}
<div class="flex gap-4 p-6">
  {% include '@elements/button/button.twig' ... %}
</div>
```

**Pourquoi** : PS Theme n'utilise pas Tailwind/utilitaires, tout doit être token-based.

**✅ Solution** : Créer classes BEM + tokens.

```css
.ps-card__cta {
  display: flex;
  gap: var(--size-4);
  padding: var(--size-6);
}
```

---

## 🎯 Patterns avancés

### Pattern 1 : Composition conditionnelle

**Card avec Button optionnel** :

```twig
{% if cta_text %}
  <div class="ps-card__cta">
    {% include '@elements/button/button.twig' with {
      text: cta_text,
      variant: cta_variant|default('primary'),
      href: cta_href,
    } only %}
  </div>
{% endif %}
```

---

### Pattern 2 : Multiple enfants

**Alert avec Icon + Button** (2 atoms dans 1 organism) :

```css
.ps-alert {
  /* Icon overrides */
  .ps-icon {
    --ps-icon-size: var(--size-5);
    --ps-icon-color: currentColor;
  }
  
  /* Button overrides */
  &__actions .ps-button {
    --ps-button-padding-x: var(--size-3);
    --ps-button-padding-y: var(--size-1);
  }
}
```

---

### Pattern 3 : Cascade responsive

**Overrides différents selon viewport** :

```css
.ps-card {
  /* Mobile : Button compact */
  &__cta .ps-button {
    --ps-button-padding-x: var(--size-3);
    --ps-button-padding-y: var(--size-2);
  }
  
  /* Desktop : Button généreux */
  @media (--tablet) {
    &__cta .ps-button {
      --ps-button-padding-x: var(--size-6);
      --ps-button-padding-y: var(--size-3);
    }
  }
}
```

---

## ✅ Checklist composition

- [ ] Vérifier tokens exposés dans composant enfant (Layer 2)
- [ ] Override tokens dans CSS du parent (Layer 3)
- [ ] Ne jamais modifier CSS de l'enfant directement
- [ ] Utiliser `{% include %}` avec `only`
- [ ] Tester responsive (mobile → desktop)
- [ ] Vérifier accessibilité (contraste si override couleurs)
- [ ] Documenter overrides dans README du parent

---

## 🔍 Debugging composition

### Token non appliqué ?

```bash
# Vérifier cascade CSS dans DevTools
# Inspect element → Computed → Voir d'où vient la valeur

# Si override pas appliqué :
# 1. Vérifier sélecteur CSS (spécificité)
# 2. Vérifier que token existe dans enfant (Layer 2)
# 3. Vérifier ordre cascade (parent après enfant dans build)
```

### Composant enfant ne change pas ?

```bash
# Vérifier que tokens sont bien utilisés dans enfant
grep -r "var(--ps-button-" source/patterns/elements/button/button.css

# Si hardcodé, refactor enfant d'abord
```

---

## 📚 Ressources

### Documentation

- **Instructions** : [.github/instructions/02-component-development.md](../../.github/instructions/02-component-development.md) (section 2)
- **Standards CSS** : [.github/instructions/03-technical-implementation.md](../../.github/instructions/03-technical-implementation.md) (section 1.3)

### Exemples de référence

| Parent | Enfant(s) | Fichier |
|--------|-----------|---------|
| Card | Button | `source/patterns/components/card/card.css` |
| Alert | Icon, Button | `source/patterns/components/alert/alert.css` |
| Badge | Icon | `source/patterns/elements/badge/badge.css` |

---

**Navigation** : [← Créer composant](./creer-composant.md) | [Tests qualité →](./tests-qualite.md)
