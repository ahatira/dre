# Instructions Atomic Design

## Méthodologie Brad Frost

Système de design en 5 niveaux hiérarchiques strictement respecté.

## Hiérarchie

```
Atoms (Éléments) 
  ↓ composent
Molecules (Composants)
  ↓ composent  
Organisms (Collections)
  ↓ composent
Templates (Layouts)
  ↓ implémentent
Pages
```

**Règle d'or** : Un composant ne peut inclure QUE des composants de niveau inférieur ou égal.

## 1. ATOMS (Éléments)

**Définition** : Éléments HTML de base, indivisibles, autonomes.

**Exemples** :
- `button` : Bouton cliquable
- `input` : Champ texte
- `badge` : Étiquette colorée
- `icon` : Icône SVG
- `avatar` : Photo profil
- `link` : Lien hypertexte
- `heading` : Titre (h1-h6)
- `divider` : Séparateur visuel

**Caractéristiques** :
- ✅ Un seul élément HTML root (ou wrapper minimal)
- ✅ Peut inclure système de rendu propre (`data-icon`, avatar fallback)
- ✅ Props simples : `variant`, `size`, `color`, `text`
- ❌ Pas de composition d'autres atoms (sauf rendering)
- ❌ Pas de logique métier

```twig
{# ✅ CORRECT - Atom autonome #}
<button class="ps-button ps-button--{{ props.variant }}">
  {{ props.text }}
</button>

{# ✅ CORRECT - Icon atom avec rendering #}
<span class="ps-icon" data-icon="{{ props.icon }}"></span>
```

## 2. MOLECULES (Composants)

**Définition** : Combinaisons d'atoms formant une unité fonctionnelle simple.

**Exemples** :
- `form-field` : Label + Input + Error message
- `card` : Image + Title + Text + Button
- `dropdown` : Button + Menu list
- `search-bar` : Input + Icon + Button
- `tag-list` : Multiple badges horizontalement

**Caractéristiques** :
- ✅ Compose 2-5 atoms
- ✅ Fonction simple et claire
- ✅ Props combinés : `label`, `value`, `error`, `onClick`
- ❌ Pas d'autres molecules (sauf cas exceptionnels justifiés)
- ❌ Pas de layout complexe (grid, multi-colonnes)

```twig
{# ✅ CORRECT - Molecule composant atoms #}
<div class="ps-form-field">
  {% include '@atoms/label/label.twig' with { text: props.label } only %}
  {% include '@atoms/input/input.twig' with { value: props.value } only %}
  {% if props.error %}
    {% include '@atoms/text/text.twig' with { 
      text: props.error,
      variant: 'error'
    } only %}
  {% endif %}
</div>
```

## 3. ORGANISMS (Collections)

**Définition** : Sections complètes et autonomes d'interface, composées d'atoms et molecules.

**Exemples** :
- `header` : Logo + Navigation + Search + User menu
- `property-card` : Image carousel + Badges + Price + Metadata + CTAs
- `contact-form` : Multiple form-fields + Checkboxes + Submit button
- `listing-grid` : Filter bar + Property cards grid + Pagination
- `map-view` : Interactive map + Markers + Controls

**Caractéristiques** :
- ✅ Compose atoms + molecules
- ✅ Peut inclure d'autres organisms (rare, justifié)
- ✅ Layout complexe permis (grid, flex, multi-colonnes)
- ✅ Logique métier légère
- ✅ Props avancés : `items[]`, `onSubmit`, `filters{}`

```twig
{# ✅ CORRECT - Organism composant molecules et atoms #}
<article class="ps-property-card">
  <div class="ps-property-card__gallery">
    {# Carousel photos #}
  </div>
  
  <div class="ps-property-card__badges">
    {% for badge in props.badges %}
      {% include '@molecules/property-badge/property-badge.twig' with {
        type: badge.type,
        text: badge.text
      } only %}
    {% endfor %}
  </div>
  
  <div class="ps-property-card__actions">
    {% include '@molecules/favorite-toggle/favorite-toggle.twig' %}
    {% include '@molecules/share-menu/share-menu.twig' %}
  </div>
  
  <div class="ps-property-card__content">
    {% include '@molecules/price-tag/price-tag.twig' with { 
      amount: props.price 
    } only %}
    {% include '@atoms/heading/heading.twig' with { 
      text: props.title 
    } only %}
    {% include '@atoms/button/button.twig' with { 
      text: 'View property' 
    } only %}
  </div>
</article>
```

## 4. TEMPLATES (Layouts)

**Définition** : Structures de page sans contenu réel, définissant zones et disposition.

**Exemples** :
- `one-column` : Header + Main + Footer
- `two-column` : Header + Sidebar left + Content + Footer
- `listing-with-map` : Header + Filters + Split (List | Map) + Footer
- `property-detail` : Header + Hero + Content (2-col) + Footer

**Caractéristiques** :
- ✅ Structure HTML pure (slots)
- ✅ Layout global (grid, regions)
- ✅ Props structurels : `hasSidebar`, `mapPosition`, `layout`
- ❌ Pas de contenu réel (utiliser placeholders ou slots)
- ❌ Minimal styling (layout uniquement)

```twig
{# ✅ CORRECT - Template avec slots #}
<div class="ps-template-two-column">
  <header class="ps-template__header">
    {{ slots.header }}
  </header>
  
  <div class="ps-template__content">
    <aside class="ps-template__sidebar">
      {{ slots.sidebar }}
    </aside>
    
    <main class="ps-template__main">
      {{ slots.main }}
    </main>
  </div>
  
  <footer class="ps-template__footer">
    {{ slots.footer }}
  </footer>
</div>
```

## 5. PAGES

**Définition** : Implémentations concrètes des templates avec contenu réel.

**Exemples** :
- `homepage` : Hero search + Value props + Featured properties + News
- `listing-properties` : Listing-with-map template + Real data
- `property-detail` : Property-detail template + Single property
- `contact` : One-column template + Contact form

**Caractéristiques** :
- ✅ Utilise un template
- ✅ Données réelles (Faker.js en stories)
- ✅ Compose organisms dans slots
- ✅ Logique métier complète
- ✅ Props métier : `propertyId`, `userId`, `filters{}`

```jsx
// ✅ CORRECT - Page avec template + données
export const ListingPage = {
  render: () => {
    const properties = Array.from({ length: 12 }, () => generateProperty());
    
    return render({
      template: 'listing-with-map',
      slots: {
        header: renderHeader(),
        filters: renderFilterBar({ filters: activeFilters }),
        list: properties.map(p => renderPropertyCard(p)).join(''),
        map: renderMapView({ markers: properties })
      }
    });
  }
};
```

## Règles de composition

### ✅ AUTORISÉ
- Atom → inclut rendering system propre
- Molecule → inclut atoms
- Organism → inclut molecules + atoms
- Organism → inclut organisms (rare, justifié)
- Template → définit structure + slots
- Page → utilise template + organisms/molecules/atoms

### ❌ INTERDIT
- Atom → inclut autre atom (sauf rendering)
- Molecule → inclut molecule (sauf exception)
- Template → inclut organisms directement (slots uniquement)
- Page → implémente layout custom (doit utiliser template)

## Dépendances et validation

### Avant création
1. ✅ Identifier niveau atomic correct
2. ✅ Vérifier dépendances disponibles
3. ✅ Créer composants manquants du niveau inférieur AVANT

### Exemple : Property card (organism)
**Dépendances requises** :
- `@atoms/heading` ✅
- `@atoms/button` ✅
- `@molecules/price-tag` ❌ → Créer AVANT
- `@molecules/property-badge` ❌ → Créer AVANT
- `@molecules/favorite-toggle` ❌ → Créer AVANT

**Ordre de création** :
1. `price-tag` (molecule)
2. `property-badge` (molecule)
3. `favorite-toggle` (molecule)
4. `property-card` (organism)

## Nomenclature

### Fichiers et dossiers
- Kebab-case : `property-card/`, `form-field.twig`
- Pas de préfixe niveau : `button` (pas `atom-button`)

### CSS classes
- BEM avec préfixe `ps-` : `.ps-button`, `.ps-form-field__label`
- Niveau implicite par hiérarchie dossiers

### Storybook
- Catégories : `Atoms/`, `Molecules/`, `Organisms/`, `Templates/`, `Pages/`
- Titre : `title: 'Atoms/Button'`

## Validation atomic

### Checklist composant
- [ ] Niveau atomic justifié (atom/molecule/organism/template/page)
- [ ] Dépendances de niveau inférieur disponibles
- [ ] Composition respecte hiérarchie (pas de saut de niveau)
- [ ] Props adaptés au niveau (simples → complexes)
- [ ] Stories montrent composition clairement

### Audit
```bash
# Vérifier dépendances d'un composant
grep -r "include '@" components/organisms/property-card/property-card.twig

# Valider hiérarchie
# Atoms ne doivent inclure QUE rendering, pas d'autres atoms
# Molecules incluent uniquement atoms
# Organisms incluent atoms + molecules
```

## Références

- [Atomic Design - Brad Frost](https://atomicdesign.bradfrost.com/)
- [Pattern Lab](https://patternlab.io/)
- Composants référence : `button`, `form-field`, `property-card`
