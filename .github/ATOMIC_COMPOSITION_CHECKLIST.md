# Checklist Rapide - Composition Atomique

## ✅ Avant de créer/modifier un Component (Molecule)

### 1. Identifier les Atoms nécessaires
```
Mon Component a besoin de :
□ Image ?          → @elements/image/image.twig
□ Texte ?          → @elements/text/text.twig
□ Titre ?          → @elements/heading/heading.twig
□ Badge/Label ?    → @elements/badge/badge.twig
□ Bouton ?         → @elements/button/button.twig
□ Lien ?           → @elements/link/link.twig
□ Icône ?          → @elements/icon/icon.twig
□ Séparateur ?     → @elements/divider/divider.twig
□ Autre : _______
```

### 2. Vérifier disponibilité et conformité
```bash
# Checker si l'Atom existe
ls source/patterns/elements/[atom-name]/

# Vérifier support attributes
grep "attributes" source/patterns/elements/[atom-name]/[atom-name].twig
```

**Checklist Atom** :
- [ ] Fichier existe
- [ ] Supporte paramètre `attributes`
- [ ] Utilise ternaire + `null` (pas `.merge()`)
- [ ] Props documentés en commentaire

### 3. Template de composition
```twig
{# mon-component.twig (Molecule) #}

<div class="ps-mon-component {{ classes|join(' ')|trim }}">
  
  {# Atom 1 #}
  {% if condition_atom_1 %}
    {% include '@elements/[atom]/[atom].twig' with {
      prop1: value1,
      prop2: value2,
      attributes: create_attribute().addClass('ps-mon-component__[atom]')
    } only %}
  {% endif %}
  
  {# Atom 2 #}
  {% if condition_atom_2 %}
    {% include '@elements/[atom]/[atom].twig' with {
      prop1: value1,
      attributes: create_attribute().addClass('ps-mon-component__[atom]')
    } only %}
  {% endif %}
  
</div>
```

### 4. CSS du Component
```css
.ps-mon-component {
  /* Layer 2 Variables */
  --ps-mon-component-gap: var(--size-4);
  
  /* Layout/Position UNIQUEMENT */
  display: flex;
  gap: var(--ps-mon-component-gap);
  
  /* Positioning des Atoms */
  &__[atom-1] {
    /* Position/Layout uniquement */
    flex: 1;
  }
  
  &__[atom-2] {
    /* Position/Layout uniquement */
    position: absolute;
    top: var(--size-2);
  }
}

/* ❌ NE PAS faire de style d'Atom ici */
/* Les styles sont dans elements/[atom]/[atom].css */
```

---

## 🚫 Anti-Patterns

### ❌ HTML inline d'Atom dans Molecule
```twig
{# MAUVAIS #}
<button class="ps-button ps-button--primary">
  {{ text }}
</button>
```

### ✅ Include de l'Atom
```twig
{# BON #}
{% include '@elements/button/button.twig' with {
  text: text,
  color: 'primary'
} only %}
```

---

### ❌ Styles d'Atom dans CSS de Molecule
```css
/* MAUVAIS */
.ps-card__button {
  background: var(--primary);
  padding: var(--size-3) var(--size-5);
  border-radius: var(--radius-2);
}
```

### ✅ Position/Layout uniquement
```css
/* BON */
.ps-card__button {
  margin-top: auto; /* Layout */
  align-self: flex-start; /* Position */
}
```

---

## 📝 Ordre de travail

1. **Auditer les Atoms** (voir `ATOMS_INVENTORY.md`)
2. **Adapter les Atoms** (ajouter `attributes` si manquant)
3. **Refactoriser le Twig** du Component
4. **Nettoyer le CSS** du Component
5. **Tester Storybook**
6. **Mettre à jour README**

---

## 🔗 Ressources

- `.github/ATOMIC_COMPOSITION_GUIDE.md` - Guide complet
- `.github/ATOMS_INVENTORY.md` - Inventaire et audits
- `.github/COMPLETE_RULES.md` - Toutes les règles projet
