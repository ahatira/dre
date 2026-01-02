# Header Component Analysis - Maquette Compliance

## 📊 État Actuel vs Maquette

### Maquette Requirements
D'après les images fournies, le header desktop doit contenir :
```
[Logo] [Menu: Find a property | About us | Solutions | Latest News] [Language selector]
                                                                     [Search button] [Find property] [Log in] [Contact us] [Favorites icon]
```

### Composition Actuelle
- **header_branding**: Logo (✓ correct)
- **header_navigation**: Menu primaire avec dropdowns (✓ correct)
- **header_top**: Language selector (✓ correct)
- **header_bottom**: Pile d'actions
  - ❌ "What are you looking for ?" button → DOIT être remplacé par **Search Block**
  - ✓ "Log in / Sign up" button
  - ✓ User menu (My profile/properties/messages)
  - ✓ "Contact us" button
  - ❌ Simple heart button → DOIT être remplacé par **Favorites Block**
  - ❌ Search button + form → À SUPPRIMER (remplacé par Search Block)

---

## 🔄 Changements Requis

### 1. **Intégrer Favorites Block** (créé)
**Fichier**: `source/patterns/layouts/header/header.twig`

**Avant** (lignes correspondantes dans header_bottom):
```twig
<div class="ps-header-bottom__block">
  <button class="ps-button ps-button--secondary" data-icon="heart" aria-label="Favorites"></button>
</div>
```

**Après**:
```twig
<div class="ps-header-bottom__block ps-header-bottom__block--favorites">
  {% include '@blocks/favorites/favorites.twig' with {
    count: 0,
    url: '/user/favorites',
    label: 'Mes favoris'
  } only %}
</div>
```

### 2. **Intégrer Search Block** (créé)
**Fichier**: `source/patterns/layouts/header/header.twig`

**Avant** (lignes correspondantes dans header_bottom):
```twig
<div class="ps-header-bottom__block">
  <button class="ps-button ps-button--outline ps-button--primary ps-button--full-width">What are you looking for ?</button>
</div>
<!-- ... + search button + form ci-dessous -->
<div class="ps-header-bottom__block">
  <button class="ps-button ps-button--secondary" data-icon="search" aria-label="Open search"></button>
  <form class="ps-search-form" action="/search" method="get">
    <!-- ... -->
  </form>
</div>
```

**Après**:
```twig
<div class="ps-header-bottom__block ps-header-bottom__block--search">
  {% include '@blocks/search/block-search.twig' with {
    form_config: {
      placeholder: 'What are you looking for ?',
      search_label: 'What are you looking for ?'
    }
  } only %}
</div>
```

### 3. **Mettre à jour header.yml** (données de démo)

Remplacer la section `header_bottom` pour ajouter des commentaires indiquant où les blocs seront inclus:

```yaml
header_bottom: |
  <div class="ps-header-bottom">
    <!-- Search Block inserted here -->
    <!-- Log in / Sign up button -->
    <!-- User menu -->
    <!-- Contact us button -->
    <!-- Favorites Block inserted here -->
  </div>
```

### 4. **Ajuster CSS si nécessaire**

Sections à vérifier dans `header.css`:
- `.ps-header-bottom__block--search` : Peut nécessiter des ajustements de largeur
- `.ps-header-bottom__block--favorites` : Vérifier l'alignement
- Flex layout pour les blocs inline
- Responsive behavior sur mobile

---

## 📋 Checklist

- [ ] Modifier `header.twig` : Inclure Favorites Block
- [ ] Modifier `header.twig` : Inclure Search Block
- [ ] Supprimer l'ancien code Search (button + form)
- [ ] Mettre à jour `header.yml` pour la démo
- [ ] Ajuster `header.css` pour l'alignement des blocs
- [ ] Tester Desktop layout
- [ ] Tester Mobile layout (responsive)
- [ ] Vérifier les transitions/interactions
- [ ] Build + validation

---

## 🎯 Implémentation Step-by-Step

### Étape 1: header.twig
1. Chercher la section `page.header_bottom` dans le template
2. Remplacer le button "What are you looking for ?" par Search Block
3. Remplacer le heart button par Favorites Block
4. Supprimer le code old search form
5. Garder Log in / Sign up, Contact us, User menu inchangés

### Étape 2: header.yml
Mettre à jour la démo pour retirer les éléments anciens

### Étape 3: header.css
Vérifier/ajuster:
- Spacing entre blocs
- Flex alignment
- Mobile responsiveness

### Étape 4: Test
- npm run build
- npm run watch
- Vérifier Storybook (Header stories)
- Vérifier responsive sur mobile

---

## 🔧 Technical Notes

**Favorites Block**:
- Location: `source/patterns/layouts/blocks/favorites/`
- Include path: `@blocks/favorites/favorites.twig`
- Variables: `count` (int), `url` (string), `label` (string)
- Output: Link 36px with data-icon

**Search Block**:
- Location: `source/patterns/layouts/blocks/search/`
- Include path: `@blocks/search/block-search.twig`
- Variables: `form_config` (object with placeholder, search_label)
- Output: Button 36px + expandable form

---

## ⚠️ Considerations

1. **Drupal Integration**: Header.twig is used in actual Drupal, so changes must maintain compatibility with `page` region variables
2. **Responsiveness**: Mobile layout must handle both blocks properly (likely stacked on small screens)
3. **Search Form**: Search Block handles its own toggle logic, no additional JS needed
4. **Favorites Count**: Need to ensure `count` variable can be passed dynamically from Drupal context
