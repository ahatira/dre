# Analyse Figma → PS Design System
## Inventaire exhaustif des composants détectés

---

## 📊 Synthèse Statistique

**Total de nodes analysés** : 8 480 nodes  
**Types de nodes** :
- INSTANCE : 4 907 (composants Figma réutilisables)
- FRAME : 3 573 (conteneurs et structures)

**Niveau atomique actuel** :
- `unknown` : 8 472 (99.9% - à classifier)
- `page` : 8 (pages complètes identifiées)

---

## 🎯 Alignement avec Surface

### Convention de nommage Surface
- **Elements** = Atoms (éléments de base)
- **Components** = Molecules (compositions simples)
- **Collections** = Organisms (structures riches)
- **Layouts** = Templates (gabarits de page)
- **Pages** = Pages complètes

### Méthodologie BEM appliquée
- Format : `ps-{component}__element--modifier`
- Préfixe : `ps-` (pour distinguer des autres systèmes)

---

# 1️⃣ ELEMENTS (Atoms)

## Element: Button (Atom)

### Variantes détectées
- **Primary button - Green** : 120 occurrences
- **Secondary button - Green** : 151 occurrences  
- **Primary button - Purple** : 23 occurrences
- **Secondary button - White** : 4 occurrences

### BEM Structure
```
ps-button                          // Block
  ps-button__label                // Element texte
  ps-button__icon                 // Element icône

Modificateurs:
  ps-button--primary              // Style primaire
  ps-button--secondary            // Style secondaire
  ps-button--green                // Couleur verte
  ps-button--purple               // Couleur violette
  ps-button--white                // Couleur blanche
```

### Props Storybook/Drupal
```yaml
label: string                     # Texte du bouton
variant: 'primary' | 'secondary' # Type de bouton
color: 'green' | 'purple' | 'white' # Couleur
size: 'small' | 'medium' | 'large' # Taille (36px, 40px)
icon: string                      # Nom de l'icône (optionnel)
iconPosition: 'left' | 'right'   # Position de l'icône
url: string                       # Lien de destination
disabled: boolean                 # État désactivé
```

### Caractéristiques communes
- **Layout** : Horizontal auto-layout, center aligned
- **Spacing** : padding 0-16px (top-bottom), 16px (left-right)
- **Typography** : BNPP Sans Regular, 16px, line-height 24px
- **Colors** :
  - Primary Green : `rgba(0, 145, 90, 1)`
  - Primary Purple : `rgba(186, 48, 117, 1)`
  - Secondary : border 2px
- **States** : default, hover (à définir), active, disabled

### Nodes Figma associés
IDs clés : `I48:7864;31:5029`, `I48:7864;31:5035`, `I48:7864;31:5047`

---

## Element: Icon (Atom)

### Catégories détectées

#### Generic Icons
- **Arrow down** : 434 occurrences
- **Close** : 408 occurrences
- **Arrow right** : 142 occurrences
- **Arrow left** : 98 occurrences
- **Arrow top** : 86 occurrences
- **Big arrow right** : 242 occurrences
- **Checkbox unchecked** : 220 occurrences
- **Pin map** : 198 occurrences
- **Plus small** : 160 occurrences
- **Search** : 60 occurrences
- **Edit** : 48 occurrences
- **Information** : 13 occurrences

#### Ad/Annonce Icons
- **Fav filled** : 72 occurrences
- **Fav stroke** : 51 occurrences
- **Eye** : 57 occurrences
- **Eye closed** : 17 occurrences
- **Phone** : 49 occurrences
- **Burger menu** : 156 occurrences
- **People number** : (détecté)

#### Website Categories Icons
- **Account** : 50 occurrences
- **Entrusting a property** : (détecté)

#### Universe/Social Icons
- **Shops** : 46 occurrences
- **Mail Outline** : 46 occurrences
- **LinkedIn** : 23 occurrences
- **Twitter** : 13 occurrences
- **Search/Comparateur** : 56 occurrences

### BEM Structure
```
ps-icon                           // Block
  ps-icon__svg                   // Element SVG

Modificateurs (par catégorie):
  ps-icon--generic              // Icônes génériques
  ps-icon--arrow                // Flèches
  ps-icon--social               // Réseaux sociaux
  ps-icon--ad                   // Annonces
  ps-icon--category             // Catégories
  
Modificateurs (par taille):
  ps-icon--small                // 16x16px
  ps-icon--medium               // 20x20px
  ps-icon--large                // 24x24px
  ps-icon--xlarge               // 32x32px
```

### Props Storybook/Drupal
```yaml
name: string                      # Nom de l'icône
category: string                  # Catégorie (generic, ad, social, etc.)
size: 16 | 20 | 24 | 32          # Taille en pixels
color: string                     # Couleur (inherit par défaut)
ariaLabel: string                 # Label pour accessibilité
```

### Caractéristiques communes
- **Layout** : NONE (positioned absolutely ou inline)
- **Sizes** : 16px, 20px, 24px, 32px
- **Colors** : Hérite généralement du parent ou couleur primaire
- **Format** : SVG vectoriel, stroke ou fill

---

## Element: Field (Atom)

### Occurrences
- **Field** : 147 occurrences

### BEM Structure
```
ps-field                          // Block
  ps-field__label                // Label du champ
  ps-field__input                // Input ou select
  ps-field__icon                 // Icône (flèche, close, etc.)
  ps-field__helper               // Texte d'aide
  ps-field__error                // Message d'erreur

Modificateurs:
  ps-field--text                 // Input texte
  ps-field--select               // Select/dropdown
  ps-field--dropdown             // Avec dropdown
  ps-field--error                // État erreur
  ps-field--disabled             // État désactivé
  ps-field--filled               // Avec valeur
```

### Props Storybook/Drupal
```yaml
label: string                     # Label du champ
type: 'text' | 'select' | 'number' # Type de champ
placeholder: string               # Texte placeholder
value: string                     # Valeur
required: boolean                 # Champ obligatoire
disabled: boolean                 # Champ désactivé
error: string                     # Message d'erreur
helperText: string                # Texte d'aide
icon: string                      # Icône à afficher
iconPosition: 'left' | 'right'   # Position de l'icône
```

### Caractéristiques communes
- **Layout** : Horizontal, space-between
- **Spacing** : padding 8px-16px
- **Dimensions** : height 40px, width variable (256px-536px)
- **Typography** : 
  - Label : BNPP Sans Bold, 14px, line-height 24px
  - Input : BNPP Sans Regular, 14px, line-height 24px
- **Colors** :
  - Background : `rgba(255, 255, 255, 1)`
  - Border : `rgba(214, 219, 222, 1)`, width 2px
  - Border radius : 0 (carré)

### Nodes Figma associés
Pattern récurrent : `Field` + `Frame 2` + `Icon`

---

## Element: Link (Atom)

### Occurrences
- **Link Green** : 262 occurrences

### BEM Structure
```
ps-link                           // Block
  ps-link__text                  // Texte du lien
  ps-link__icon                  // Icône (optionnelle)

Modificateurs:
  ps-link--green                 // Lien vert (primaire)
  ps-link--underline             // Souligné
  ps-link--with-icon             // Avec icône
  ps-link--external              // Lien externe
```

### Props Storybook/Drupal
```yaml
text: string                      # Texte du lien
url: string                       # URL de destination
color: 'green' | 'default'       # Couleur du lien
underline: boolean                # Souligné ou non
icon: string                      # Icône (optionnelle)
target: '_self' | '_blank'       # Cible du lien
rel: string                       # Attribut rel
```

### Caractéristiques communes
- **Typography** : BNPP Sans Regular, taille variable
- **Color** : Green primary `rgba(0, 145, 90, 1)`
- **States** : default, hover, active, visited

---

## Element: Checkbox (Atom)

### Occurrences
- **Checkbox** : 52 occurrences
- **Checkbox unchecked icon** : 220 occurrences

### BEM Structure
```
ps-checkbox                       // Block
  ps-checkbox__input             // Input natif (hidden)
  ps-checkbox__box               // Boîte visuelle
  ps-checkbox__checkmark         // Icône check
  ps-checkbox__label             // Label texte

Modificateurs:
  ps-checkbox--checked           // État coché
  ps-checkbox--unchecked         // État décoché
  ps-checkbox--disabled          // État désactivé
  ps-checkbox--indeterminate     // État indéterminé
```

### Props Storybook/Drupal
```yaml
label: string                     # Label du checkbox
name: string                      # Attribut name
checked: boolean                  # État coché
disabled: boolean                 # État désactivé
value: string                     # Valeur
onChange: function                # Callback changement
```

---

## Element: Flag (Atom)

### Variantes détectées
- **Flag/UK** : détecté dans sélecteur de langue

### BEM Structure
```
ps-flag                           // Block
  ps-flag__image                 // Image du drapeau

Modificateurs:
  ps-flag--uk                    // Drapeau UK
  ps-flag--fr                    // Drapeau France
  ps-flag--small                 // Petite taille (16px)
  ps-flag--medium                // Taille moyenne (20px)
```

### Props Storybook/Drupal
```yaml
country: string                   # Code pays (UK, FR, etc.)
size: 16 | 20 | 24               # Taille
alt: string                       # Texte alternatif
```

### Caractéristiques communes
- **Dimensions** : 20x20px typiquement
- **Format** : Image ou SVG
- **Usage** : Sélecteur de langue, indicateurs régionaux

---

## Element: Badge/Tag (Atom)

### Occurrences
Pattern détecté dans contexte "date badge", "eyebrow"

### BEM Structure
```
ps-badge                          // Block
  ps-badge__text                 // Texte du badge

Modificateurs:
  ps-badge--date                 // Badge de date
  ps-badge--status               // Badge de statut
  ps-badge--small                // Petite taille
  ps-badge--medium               // Taille moyenne
  ps-badge--primary              // Couleur primaire
  ps-badge--success              // Couleur succès
  ps-badge--warning              // Couleur avertissement
```

### Props Storybook/Drupal
```yaml
text: string                      # Texte du badge
variant: 'date' | 'status' | 'label' # Type de badge
color: string                     # Couleur
size: 'small' | 'medium'         # Taille
```

---

# 2️⃣ COMPONENTS (Molecules)

## Component: Card (Molecule)

### Occurrences
- **Product Card** : 47 occurrences
- **Cards** : 5 occurrences (groupes)

### BEM Structure
```
ps-card                           // Block
  ps-card__image                 // Image de la carte
  ps-card__image-wrapper         // Conteneur de l'image
  ps-card__badge                 // Badge sur l'image
  ps-card__content               // Contenu de la carte
  ps-card__eyebrow               // Surtitre/catégorie
  ps-card__title                 // Titre principal
  ps-card__description           // Description
  ps-card__meta                  // Métadonnées (date, auteur, etc.)
  ps-card__meta-item             // Item de métadonnée
  ps-card__price                 // Prix
  ps-card__surface               // Surface (m²)
  ps-card__location              // Localisation
  ps-card__actions               // Conteneur d'actions
  ps-card__cta                   // Call-to-action (bouton)
  ps-card__footer                // Pied de carte
  ps-card__icon                  // Icône (fav, phone, eye)

Modificateurs:
  ps-card--product               // Carte produit immobilier
  ps-card--featured              // Carte mise en avant
  ps-card--compact               // Version compacte
  ps-card--horizontal            // Layout horizontal
  ps-card--vertical              // Layout vertical (défaut)
  ps-card--with-image            // Avec image
  ps-card--without-image         // Sans image
```

### Props Storybook/Drupal
```yaml
image: object                     # Image (url, alt)
  url: string
  alt: string
badge: string                     # Badge sur l'image
eyebrow: string                   # Surtitre/catégorie
title: string                     # Titre de la carte
description: string               # Description
price: string | number            # Prix
surface: string | number          # Surface en m²
location: string                  # Localisation
meta: array                       # Métadonnées
  - icon: string
    text: string
cta: object                       # Call-to-action
  text: string
  url: string
  variant: string
actions: array                    # Actions (favoris, partage)
  - icon: string
    label: string
    onClick: function
variant: 'default' | 'featured' | 'compact' # Variante
layout: 'vertical' | 'horizontal' # Disposition
```

### Caractéristiques communes
- **Structure typique** :
  1. Image avec badges/actions superposés
  2. Contenu avec eyebrow + titre + description
  3. Métadonnées (icônes + textes)
  4. Prix / Surface / Localisation
  5. CTA principal
- **Layout** : Vertical auto-layout, spacing 16-24px
- **Colors** : Background blanc, borders gris clair
- **Typography** :
  - Eyebrow : 12-14px, Bold
  - Title : 20-24px, Bold
  - Description : 14-16px, Regular
  - Meta : 12-14px, Regular
- **Dimensions** : Variables, typiquement 278-380px width

### Nodes Figma associés
ID pattern : `product-card` (47 occurrences)

---

## Component: Dropdown (Molecule)

### Occurrences
- **Dropdown item** : 262 occurrences

### BEM Structure
```
ps-dropdown                       // Block
  ps-dropdown__trigger           // Élément déclencheur
  ps-dropdown__trigger-text      // Texte du déclencheur
  ps-dropdown__trigger-icon      // Icône du déclencheur (arrow)
  ps-dropdown__menu              // Menu déroulant
  ps-dropdown__item              // Item du menu
  ps-dropdown__item-text         // Texte de l'item
  ps-dropdown__item-icon         // Icône de l'item
  ps-dropdown__divider           // Séparateur d'items

Modificateurs:
  ps-dropdown--open              // État ouvert
  ps-dropdown--closed            // État fermé
  ps-dropdown--disabled          // État désactivé
  ps-dropdown--right-aligned     // Aligné à droite
  ps-dropdown--full-width        // Pleine largeur
```

### Props Storybook/Drupal
```yaml
trigger: string                   # Texte du déclencheur
items: array                      # Liste des items
  - text: string
    value: string
    icon: string
    disabled: boolean
    divider: boolean              # Séparateur après cet item
value: string                     # Valeur sélectionnée
placeholder: string               # Placeholder
disabled: boolean                 # État désactivé
onChange: function                # Callback changement
alignment: 'left' | 'right'      # Alignement du menu
fullWidth: boolean                # Pleine largeur
```

### Caractéristiques communes
- **Layout** : Vertical pour menu, items stacked
- **Spacing** : padding 8-16px par item
- **Typography** : BNPP Sans Regular, 14-16px
- **Interaction** : Clic pour ouvrir/fermer, keyboard navigation
- **States** : closed, open, hover, selected, disabled

---

## Component: SearchBar (Molecule)

### BEM Structure
```
ps-search-bar                     // Block
  ps-search-bar__input           // Input de recherche
  ps-search-bar__icon            // Icône de recherche
  ps-search-bar__clear           // Bouton clear
  ps-search-bar__suggestions     // Liste de suggestions

Modificateurs:
  ps-search-bar--with-suggestions // Avec suggestions
  ps-search-bar--active          // État actif (focus)
  ps-search-bar--large           // Grande taille
  ps-search-bar--medium          // Taille moyenne
```

### Props Storybook/Drupal
```yaml
placeholder: string               # Placeholder
value: string                     # Valeur actuelle
suggestions: array                # Suggestions
  - text: string
    url: string
showIcon: boolean                 # Afficher icône search
showClear: boolean                # Afficher bouton clear
size: 'medium' | 'large'         # Taille
onSearch: function                # Callback recherche
onChange: function                # Callback changement
```

### Caractéristiques communes
- **Icône** : Icon/Generic/Search (60 occurrences)
- **Layout** : Horizontal, icon + input + clear button
- **Spacing** : padding 8-16px
- **Typography** : 14-16px Regular

---

## Component: FormField (Molecule)

### Composé de
- Label (Element)
- Field/Input (Element)
- Helper text (optionnel)
- Error message (optionnel)
- Icon (optionnel)

### BEM Structure
```
ps-form-field                     // Block (wrapper global)
  ps-form-field__label           // Label (réutilise ps-field__label)
  ps-form-field__input-wrapper   // Wrapper de l'input
  ps-form-field__input           // Input (réutilise ps-field)
  ps-form-field__helper          // Texte d'aide
  ps-form-field__error           // Message d'erreur
  ps-form-field__icon            // Icône

Modificateurs:
  ps-form-field--required        // Champ obligatoire
  ps-form-field--error           // État erreur
  ps-form-field--disabled        // État désactivé
  ps-form-field--inline          // Layout inline (label + input)
  ps-form-field--stacked         // Layout stacked (défaut)
```

### Props Storybook/Drupal
```yaml
# Hérite des props de Field + ajouts
label: string
type: string
placeholder: string
value: string
required: boolean
disabled: boolean
error: string
helperText: string
icon: string
```

### Pattern Figma détecté
Pattern récurrent : `Numérique` (92 occurrences) = Label + Field

---

## Component: HeaderMenuItem (Molecule)

### Occurrences
- **Header / Item menu** : 139 occurrences

### BEM Structure
```
ps-header-menu-item               // Block
  ps-header-menu-item__text      // Texte du menu
  ps-header-menu-item__icon      // Icône (arrow down, burger menu)
  ps-header-menu-item__submenu   // Sous-menu

Modificateurs:
  ps-header-menu-item--active    // Item actif
  ps-header-menu-item--with-submenu // Avec sous-menu
  ps-header-menu-item--expanded  // Sous-menu ouvert
```

### Props Storybook/Drupal
```yaml
text: string                      # Texte du menu
url: string                       # URL (optionnel si submenu)
icon: string                      # Icône (optionnel)
active: boolean                   # Item actif
submenu: array                    # Sous-menu
  - text: string
    url: string
```

### Caractéristiques communes
- **Layout** : Horizontal, center aligned
- **Spacing** : padding 0, item-spacing 24px entre items
- **Typography** : BNPP Sans Regular, 16px, line-height 24px
- **Icon** : Burger menu (156 occ), Arrow down (434 occ)
- **Height** : 64px ou 88px

---

## Component: LanguageSelector (Molecule)

### Composé de
- Flag (Element)
- Text (Element: "En", "Fr", etc.)
- Arrow dropdown icon (Element)

### BEM Structure
```
ps-language-selector              // Block
  ps-language-selector__trigger  // Bouton déclencheur
  ps-language-selector__flag     // Drapeau
  ps-language-selector__text     // Code langue (En, Fr)
  ps-language-selector__icon     // Icône arrow down
  ps-language-selector__dropdown // Liste des langues

Modificateurs:
  ps-language-selector--open     // Dropdown ouvert
```

### Props Storybook/Drupal
```yaml
currentLanguage: string           # Langue actuelle (En, Fr, etc.)
languages: array                  # Langues disponibles
  - code: string                  # Code (En, Fr)
    flag: string                  # Icône drapeau
    label: string                 # Label complet (English, Français)
onChange: function                # Callback changement
```

### Caractéristiques communes
- **Dimensions** : 104x36px
- **Layout** : Horizontal, padding 8-16px
- **Border** : 2px, `rgba(214, 219, 222, 1)`
- **Background** : blanc

---

# 3️⃣ COLLECTIONS (Organisms)

## Collection: Header (Organism)

### Occurrences
- **Header / Header** : 43 occurrences

### BEM Structure
```
ps-header                         // Block
  ps-header__top                 // Barre supérieure
  ps-header__top-left            // Partie gauche top
  ps-header__top-right           // Partie droite top
  ps-header__logo                // Logo
  ps-header__menu                // Menu principal
  ps-header__actions             // Actions (boutons, search, account)
  ps-header__user                // Section utilisateur
  ps-header__language            // Sélecteur langue
  ps-header__search              // Barre de recherche
  ps-header__favorites           // Favoris
  ps-header__contact             // Contact

Modificateurs:
  ps-header--sticky              // Header fixe en scroll
  ps-header--transparent         // Background transparent
  ps-header--with-submenu        // Avec sous-menu visible
  ps-header--logged-in           // Utilisateur connecté
  ps-header--logged-out          // Utilisateur déconnecté
```

### Props Storybook/Drupal
```yaml
logo: object
  url: string                     # URL du logo
  alt: string                     # Alt text
  link: string                    # Lien du logo
menuItems: array                  # Items du menu principal
  - text: string
    url: string
    submenu: array
actions: array                    # Actions (search, account, etc.)
  - type: 'button' | 'icon' | 'dropdown'
    label: string
    icon: string
    onClick: function
user: object                      # Info utilisateur (si connecté)
  name: string
  avatar: string
languages: array                  # Langues disponibles
currentLanguage: string           # Langue actuelle
sticky: boolean                   # Header fixe
transparent: boolean              # Background transparent
```

### Structure détectée
1. **Top bar** (134px height)
   - Logo (left)
   - Menu items (center) : 5 items avec dropdowns
   - Actions (right) :
     - Buttons (Primary Green "Sell", Secondary Green "Find/sell", etc.)
     - Search field
     - Dropdown menu "Find a property"
     - User info (icon + name + arrow)
     - Contact button (Primary Purple with icon)
     - Icons (favorites, search)
     - Language selector

### Nodes Figma associés
Pattern : `Header / Header` (43 occurrences)

---

## Collection: Hero (Organism)

### Structure typique détectée
- **Background image** avec overlay sombre
- **Contenu centré** :
  - Titre principal (32-40px Bold)
  - Sous-titre ou description
  - CTA(s) : Button(s)

### BEM Structure
```
ps-hero                           // Block
  ps-hero__background            // Image de fond
  ps-hero__overlay               // Overlay (gradient/couleur)
  ps-hero__content               // Contenu principal
  ps-hero__eyebrow               // Surtitre
  ps-hero__title                 // Titre principal
  ps-hero__subtitle              // Sous-titre
  ps-hero__description           // Description
  ps-hero__actions               // Conteneur d'actions
  ps-hero__cta                   // Call-to-action (bouton)

Modificateurs:
  ps-hero--full-height           // Pleine hauteur viewport
  ps-hero--with-image            // Avec image de fond
  ps-hero--centered              // Contenu centré
  ps-hero--left-aligned          // Contenu aligné gauche
  ps-hero--dark-overlay          // Overlay sombre (0.6)
  ps-hero--light-overlay         // Overlay clair
```

### Props Storybook/Drupal
```yaml
backgroundImage: string           # URL image de fond
overlay: number                   # Opacité overlay (0-1)
eyebrow: string                   # Surtitre
title: string                     # Titre principal
subtitle: string                  # Sous-titre
description: string               # Description
cta: array                        # Boutons CTA
  - text: string
    url: string
    variant: string
alignment: 'center' | 'left'     # Alignement du contenu
height: 'auto' | 'full'          # Hauteur
```

### Caractéristiques communes
- **Background** : Image + overlay `rgba(0, 0, 0, 0.6)`
- **Padding** : 60px vertical, 32px horizontal
- **Typography** :
  - Title : 32px Regular, line-height 40px
  - CTA : Secondary button - White
- **Dimensions** : 612x508px (exemple détecté)

### Pattern détecté
Frame 26 + Frame 25 + Frame 24 + titre + CTA

---

## Collection: SearchForm (Organism)

### Structure détectée
Formulaire de recherche complexe avec :
- **Transaction type selector** : Boutons (Buy/Rent/Flexible)
- **Multiple form fields** :
  - Location(s) - Field avec autocomplete
  - Property type - Select dropdown
  - Min surface (m²) - Numeric field
  - Max price - Numeric field
- **Actions** :
  - Primary button "Search"
  - Section avec info + Secondary button "Delegate my search"

### BEM Structure
```
ps-search-form                    // Block
  ps-search-form__header         // En-tête du formulaire
  ps-search-form__title          // Titre
  ps-search-form__transaction    // Sélecteur de transaction
  ps-search-form__fields         // Conteneur de champs
  ps-search-form__field-group    // Groupe de champs (inline)
  ps-search-form__actions        // Actions principales
  ps-search-form__secondary      // Section secondaire (delegate)
  ps-search-form__info           // Info avec icône

Modificateurs:
  ps-search-form--simple         // Version simplifiée
  ps-search-form--advanced       // Version avancée (plus de filtres)
  ps-search-form--horizontal     // Layout horizontal
  ps-search-form--vertical       // Layout vertical
```

### Props Storybook/Drupal
```yaml
title: string                     # Titre du formulaire
transactionTypes: array           # Types de transaction
  - value: string
    label: string
fields: array                     # Champs du formulaire
  - name: string
    label: string
    type: string
    placeholder: string
    required: boolean
onSubmit: function                # Callback soumission
secondaryAction: object           # Action secondaire
  text: string
  info: string
  onClick: function
layout: 'horizontal' | 'vertical' # Disposition
```

### Caractéristiques communes
- **Layout** : Vertical auto-layout, spacing 24px
- **Background** : Blanc, avec section grise `rgba(249, 249, 251, 1)` pour action secondaire
- **Padding** : 16-24px
- **Dimensions** : 584x508px (exemple détecté)

### Nodes Figma associés
Frame 20 + Frame 19 + Frame 14 + fields + actions

---

## Collection: CardGrid (Organism)

### Structure
Grille de cartes (Product Cards)

### BEM Structure
```
ps-card-grid                      // Block
  ps-card-grid__header           // En-tête de la grille
  ps-card-grid__title            // Titre
  ps-card-grid__filters          // Filtres/tri
  ps-card-grid__grid             // Conteneur grille
  ps-card-grid__item             // Item de grille (wrapper de card)
  ps-card-grid__footer           // Pied (pagination, load more)

Modificateurs:
  ps-card-grid--2-cols           // 2 colonnes
  ps-card-grid--3-cols           // 3 colonnes
  ps-card-grid--4-cols           // 4 colonnes
  ps-card-grid--with-filters     // Avec filtres visibles
```

### Props Storybook/Drupal
```yaml
title: string                     # Titre de la section
cards: array                      # Liste des cartes
  - (props de Card)
columns: 2 | 3 | 4               # Nombre de colonnes
gap: number                       # Espacement entre cartes (28px)
filters: array                    # Filtres disponibles
  - name: string
    options: array
pagination: object                # Pagination
  currentPage: number
  totalPages: number
loadMore: boolean                 # Bouton "Load more"
```

### Caractéristiques communes
- **Layout** : Grid ou Flexbox, gap 28px
- **Responsive** : 4 cols desktop, 3 cols tablet, 1-2 cols mobile
- **Spacing** : Entre cartes 28px

### Pattern détecté
Frame 38 contenant 4 x Frame (34, 35, 36, 37) avec product cards

---

## Collection: FeatureSection (Organism)

### Structure détectée
Section avec :
- Titre + sous-titre + description (centrés)
- Grille de "features" : icône + titre + texte + CTA

### BEM Structure
```
ps-feature-section                // Block
  ps-feature-section__header     // En-tête
  ps-feature-section__eyebrow    // Surtitre
  ps-feature-section__title      // Titre principal
  ps-feature-section__subtitle   // Sous-titre
  ps-feature-section__description // Description
  ps-feature-section__grid       // Grille de features
  ps-feature-section__item       // Item feature
  ps-feature-section__item-icon  // Icône
  ps-feature-section__item-title // Titre feature
  ps-feature-section__item-text  // Texte feature
  ps-feature-section__item-cta   // CTA feature

Modificateurs:
  ps-feature-section--centered   // Contenu centré
  ps-feature-section--2-cols     // 2 colonnes
  ps-feature-section--3-cols     // 3 colonnes
  ps-feature-section--4-cols     // 4 colonnes
```

### Props Storybook/Drupal
```yaml
eyebrow: string                   # Surtitre
title: string                     # Titre principal
subtitle: string                  # Sous-titre
description: string               # Description
features: array                   # Liste des features
  - icon: string                  # Nom de l'icône (32x32)
    title: string
    text: string
    cta: object
      text: string
      url: string
columns: 2 | 3 | 4               # Nombre de colonnes
centered: boolean                 # Contenu centré
```

### Caractéristiques communes
- **Layout** : Vertical + Grid
- **Spacing** : 80px entre header et grid, 32px entre items
- **Icons** : 32x32px
- **Typography** :
  - Title section : 32px Bold, line-height 40px, centered
  - Title feature : 20px Regular, line-height 24px, centered
- **Grid** : SPACE_BETWEEN, gap 28px

### Pattern détecté
Frame 39 : titre + Frame 38 : grille de 4 features (Entrust/Delegate/Advice/Compare)

---

## Collection: ArticleList (Organism)

### Structure
Liste d'articles/news avec filtres

### BEM Structure
```
ps-article-list                   // Block
  ps-article-list__header        // En-tête
  ps-article-list__filters       // Filtres
  ps-article-list__grid          // Grille d'articles
  ps-article-list__item          // Item (card article)
  ps-article-list__pagination    // Pagination

Modificateurs:
  ps-article-list--with-sidebar  // Avec sidebar
  ps-article-list--grid          // Affichage grille
  ps-article-list--list          // Affichage liste
```

### Props Storybook/Drupal
```yaml
title: string
articles: array
  - image: string
    category: string
    title: string
    excerpt: string
    date: string
    author: string
    url: string
filters: array
layout: 'grid' | 'list'
itemsPerPage: number
```

---

# 4️⃣ LAYOUTS (Templates)

## Layout: PageContainer (Template)

### BEM Structure
```
ps-page-container                 // Block
  ps-page-container__wrapper     // Wrapper principal
  ps-page-container__content     // Contenu principal

Modificateurs:
  ps-page-container--fluid       // Pleine largeur
  ps-page-container--boxed       // Largeur fixe (1196px)
```

### Props Storybook/Drupal
```yaml
maxWidth: number                  # Largeur max (1196px, 1440px)
fluid: boolean                    # Pleine largeur
padding: number                   # Padding horizontal
```

### Caractéristiques communes
- **Max-width** : 1196px ou 1440px
- **Padding** : Responsive (122px desktop)
- **Centering** : margin auto

---

## Layout: TwoColumn (Template)

### Structure détectée
2 colonnes : formulaire (left) + hero/image (right)

### BEM Structure
```
ps-two-column                     // Block
  ps-two-column__left            // Colonne gauche
  ps-two-column__right           // Colonne droite

Modificateurs:
  ps-two-column--50-50           // 50/50
  ps-two-column--60-40           // 60/40
  ps-two-column--40-60           // 40/60
  ps-two-column--reverse         // Ordre inversé mobile
```

### Props Storybook/Drupal
```yaml
leftContent: html                 # Contenu gauche
rightContent: html                # Contenu droite
ratio: '50-50' | '60-40' | '40-60' # Ratio des colonnes
reverse: boolean                  # Inverser sur mobile
gap: number                       # Espacement entre colonnes
```

### Pattern détecté
Frame 27 : Frame 20 (left 584px) + Frame 26 (right 612px)

---

## Layout: ContentSidebar (Template)

### BEM Structure
```
ps-content-sidebar                // Block
  ps-content-sidebar__main       // Contenu principal
  ps-content-sidebar__sidebar    // Sidebar
  ps-content-sidebar__filters    // Filtres (dans sidebar)

Modificateurs:
  ps-content-sidebar--left       // Sidebar à gauche
  ps-content-sidebar--right      // Sidebar à droite
  ps-content-sidebar--sticky     // Sidebar sticky
```

### Props Storybook/Drupal
```yaml
mainContent: html                 # Contenu principal
sidebarContent: html              # Contenu sidebar
sidebarPosition: 'left' | 'right' # Position sidebar
sidebarWidth: number              # Largeur sidebar
sticky: boolean                   # Sidebar sticky
```

---

## Layout: Block (Template)

### Structure générique de section

### BEM Structure
```
ps-block                          // Block
  ps-block__inner                // Wrapper intérieur
  ps-block__header               // En-tête
  ps-block__content              // Contenu
  ps-block__footer               // Pied

Modificateurs:
  ps-block--white                // Background blanc
  ps-block--gray                 // Background gris
  ps-block--full-width           // Pleine largeur
  ps-block--centered             // Contenu centré
  ps-block--padded               // Avec padding
```

### Props Storybook/Drupal
```yaml
backgroundColor: string           # Couleur de fond
fullWidth: boolean                # Pleine largeur
centered: boolean                 # Contenu centré
padding: 'none' | 'small' | 'medium' | 'large' # Padding
```

### Caractéristiques communes
- **Spacing vertical** : 80-104px entre sections
- **Background** : Blanc ou gris `rgba(249, 249, 251, 1)`

---

# 5️⃣ PAGES

## Page: Home Page (Page)

### Structure détectée
- **Header** (Collection)
- **Hero Section** avec SearchForm (Collection)
- **Feature Section** : Services (Entrust, Delegate, Advice, Compare)
- **Content sections** : Multiples
- **Footer** (Collection - à détecter)

### Pages identifiées
- **Home page - Profile** : 8 occurrences (atomicLevel: "page")
- **Property search** pages

### BEM Structure
```
ps-page                           // Block global
  ps-page__header                // Header
  ps-page__hero                  // Hero/bannière
  ps-page__main                  // Contenu principal
  ps-page__section               // Section de contenu
  ps-page__footer                // Footer

Modificateurs:
  ps-page--home                  // Page d'accueil
  ps-page--listing               // Page listing
  ps-page--detail                // Page détail
  ps-page--search                // Page recherche
```

---

# 📋 COMPOSANTS ADDITIONNELS DÉTECTÉS

## Element: Eyebrow (Atom)
Surtitre au-dessus d'un titre principal

### BEM Structure
```
ps-eyebrow                        // Block
  ps-eyebrow__text               // Texte

Modificateurs:
  ps-eyebrow--small              // Petite taille
  ps-eyebrow--medium             // Taille moyenne
  ps-eyebrow--uppercase          // Texte en majuscules
```

---

## Element: Heading (Atom)
Titres hiérarchiques

### BEM Structure
```
ps-heading                        // Block
  ps-heading__text               // Texte

Modificateurs:
  ps-heading--h1                 // 32-40px Bold
  ps-heading--h2                 // 24-32px Bold
  ps-heading--h3                 // 20-24px Bold
  ps-heading--h4                 // 16-20px Bold
  ps-heading--centered           // Centré
  ps-heading--left               // Aligné gauche
```

### Caractéristiques communes
- **Font** : BNPP Sans Bold ou Regular
- **Sizes** :
  - H1 : 32-40px, line-height 40px
  - H2 : 24-32px, line-height 32-40px
  - H3 : 20-24px, line-height 24-32px
  - H4 : 16-20px, line-height 24px

---

## Element: Text (Atom)
Texte corps

### BEM Structure
```
ps-text                           // Block
  ps-text__content               // Contenu

Modificateurs:
  ps-text--small                 // 12px
  ps-text--regular               // 14px (défaut)
  ps-text--large                 // 16px
  ps-text--bold                  // Bold
  ps-text--centered              // Centré
```

### Caractéristiques communes
- **Font** : BNPP Sans Regular
- **Sizes** : 12px, 14px, 16px
- **Line-height** : 24px

---

## Component: Breadcrumb (Molecule)
Fil d'ariane (à détecter dans analyse détaillée)

### BEM Structure
```
ps-breadcrumb                     // Block
  ps-breadcrumb__list            // Liste
  ps-breadcrumb__item            // Item
  ps-breadcrumb__link            // Lien
  ps-breadcrumb__separator       // Séparateur
  ps-breadcrumb__current         // Item actuel

Modificateurs:
  ps-breadcrumb--with-icons      // Avec icônes
```

---

## Component: Pagination (Molecule)
Navigation de pages

### BEM Structure
```
ps-pagination                     // Block
  ps-pagination__list            // Liste de pages
  ps-pagination__item            // Item
  ps-pagination__link            // Lien
  ps-pagination__prev            // Précédent
  ps-pagination__next            // Suivant
  ps-pagination__dots            // Points de suspension

Modificateurs:
  ps-pagination--simple          // Version simple (prev/next)
  ps-pagination--full            // Version complète
```

---

## Component: Modal (Molecule)
Fenêtre modale

### BEM Structure
```
ps-modal                          // Block
  ps-modal__overlay              // Overlay de fond
  ps-modal__dialog               // Dialog
  ps-modal__header               // En-tête
  ps-modal__title                // Titre
  ps-modal__close                // Bouton fermer
  ps-modal__body                 // Corps
  ps-modal__footer               // Pied (actions)

Modificateurs:
  ps-modal--small                // Petite taille
  ps-modal--medium               // Taille moyenne
  ps-modal--large                // Grande taille
  ps-modal--full                 // Plein écran
```

---

## Collection: Footer (Organism)
Pied de page (à analyser en détail)

### BEM Structure
```
ps-footer                         // Block
  ps-footer__top                 // Section supérieure
  ps-footer__columns             // Colonnes de liens
  ps-footer__column              // Colonne
  ps-footer__title               // Titre de colonne
  ps-footer__list                // Liste de liens
  ps-footer__link                // Lien
  ps-footer__bottom              // Section inférieure
  ps-footer__legal               // Liens légaux
  ps-footer__social              // Réseaux sociaux
  ps-footer__copyright           // Copyright

Modificateurs:
  ps-footer--dark                // Fond sombre
  ps-footer--light               // Fond clair
```

---

# 🎨 DESIGN TOKENS

## Colors

### Primary Colors
```scss
$ps-color-green-primary: rgba(0, 145, 90, 1);      // #00915A
$ps-color-purple-primary: rgba(186, 48, 117, 1);  // #BA3075
$ps-color-gray-dark: rgba(67, 79, 87, 1);         // #434F57
```

### Secondary Colors
```scss
$ps-color-white: rgba(255, 255, 255, 1);          // #FFFFFF
$ps-color-gray-light: rgba(249, 249, 251, 1);     // #F9F9FB
$ps-color-gray-border: rgba(214, 219, 222, 1);    // #D6DBDE
```

### Overlay
```scss
$ps-color-overlay-dark: rgba(0, 0, 0, 0.6);       // Overlay 60%
```

## Typography

### Font Family
```scss
$ps-font-family: "BNPP Sans", sans-serif;
```

### Font Weights
```scss
$ps-font-regular: 400;
$ps-font-bold: 700;
```

### Font Sizes
```scss
$ps-font-size-xs: 12px;
$ps-font-size-sm: 14px;
$ps-font-size-base: 16px;
$ps-font-size-lg: 20px;
$ps-font-size-xl: 24px;
$ps-font-size-2xl: 32px;
$ps-font-size-3xl: 40px;
```

### Line Heights
```scss
$ps-line-height-base: 24px;
$ps-line-height-lg: 32px;
$ps-line-height-xl: 40px;
```

## Spacing

### Padding/Margin
```scss
$ps-spacing-xs: 4px;
$ps-spacing-sm: 8px;
$ps-spacing-base: 16px;
$ps-spacing-lg: 24px;
$ps-spacing-xl: 32px;
$ps-spacing-2xl: 40px;
$ps-spacing-3xl: 60px;
$ps-spacing-4xl: 80px;
$ps-spacing-5xl: 104px;
```

### Gap (Grids)
```scss
$ps-gap-grid: 28px;
```

## Borders

### Border Width
```scss
$ps-border-width: 2px;
```

### Border Radius
```scss
$ps-border-radius: 0;  // Design carré, sans arrondi
```

## Dimensions

### Icon Sizes
```scss
$ps-icon-xs: 16px;
$ps-icon-sm: 20px;
$ps-icon-base: 24px;
$ps-icon-lg: 32px;
```

### Button Heights
```scss
$ps-button-height-sm: 33.98px;
$ps-button-height-base: 36px;
$ps-button-height-lg: 40px;
```

### Field Heights
```scss
$ps-field-height: 40px;
```

### Container Widths
```scss
$ps-container-base: 1196px;
$ps-container-full: 1440px;
```

---

# 📊 STATISTIQUES FINALES

## Distribution par niveau Surface

### Elements (Atoms) : ~30 composants identifiés
- Button (4 variantes)
- Icon (50+ icônes, 8 catégories)
- Field (1 composant, multiples states)
- Link (1 composant)
- Checkbox (1 composant)
- Flag (2+ variantes)
- Badge/Tag (multiples variantes)
- Eyebrow
- Heading (4 niveaux)
- Text (3 tailles)

### Components (Molecules) : ~15 composants identifiés
- Card (3+ variantes)
- Dropdown (1 composant)
- SearchBar
- FormField (Numérique)
- HeaderMenuItem
- LanguageSelector
- Breadcrumb
- Pagination
- Modal

### Collections (Organisms) : ~8 composants identifiés
- Header
- Hero
- SearchForm
- CardGrid
- FeatureSection
- ArticleList
- Footer

### Layouts (Templates) : ~4 templates identifiés
- PageContainer
- TwoColumn
- ContentSidebar
- Block

### Pages : 8+ pages identifiées
- Home page - Profile
- Property search pages

---

# 🚀 PROCHAINES ÉTAPES

## 1. Validation & Priorisation
- Valider cette liste avec l'équipe design
- Prioriser les composants selon l'usage (fréquence d'utilisation)
- Identifier les dépendances entre composants

## 2. Création dans Storybook
- Créer les stories pour chaque composant
- Définir les variants/args
- Documenter les props

## 3. Intégration Drupal
- Créer les templates Twig correspondants
- Mapper les fields Drupal aux props des composants
- Créer les paragraphs/blocks Drupal

## 4. Documentation
- Créer la documentation pour chaque composant
- Ajouter des exemples d'usage
- Définir les guidelines (quand utiliser quoi)

## 5. Testing
- Tests d'accessibilité (a11y)
- Tests de responsive
- Tests de compatibilité navigateurs
- Tests de performance

---

# 📝 NOTES & OBSERVATIONS

## Points d'attention
1. **Border radius = 0** : Design très carré, à confirmer si c'est voulu partout
2. **Couleurs primaires** : Vert et Violet bien établis, mais peu de variations
3. **Typographie** : Une seule font family (BNPP Sans), 2 weights (Regular, Bold)
4. **Spacing** : Système cohérent mais pourrait être simplifié (trop de valeurs)
5. **Icons** : Très nombreuses icônes (50+), à organiser en catégories claires

## Opportunités d'amélioration
1. **Tokens** : Créer un système de design tokens complet (CSS variables)
2. **Variants** : Rationaliser les variantes de composants (éviter la multiplication)
3. **Accessibility** : Ajouter les props ARIA manquantes
4. **States** : Définir tous les states (hover, active, focus, disabled, error)
5. **Responsive** : Définir les breakpoints et les comportements mobiles

## Composants DÉTECTÉS à implémenter

### ✅ Footer (Collection - Organism)
**Occurrences** : 23 Footer + 23 Pre Footer

**Structure détectée** :
- Pre Footer : section avant le footer principal
- Footer : pied de page complet

**BEM Structure**
```
ps-footer                         // Block
  ps-footer__pre                 // Pre-footer (section au-dessus)
  ps-footer__main                // Footer principal
  ps-footer__columns             // Colonnes de liens
  ps-footer__column              // Colonne
  ps-footer__title               // Titre de colonne
  ps-footer__list                // Liste de liens
  ps-footer__link                // Lien
  ps-footer__bottom              // Section inférieure
  ps-footer__legal               // Liens légaux
  ps-footer__social              // Réseaux sociaux
  ps-footer__copyright           // Copyright

Modificateurs:
  ps-footer--dark                // Fond sombre
  ps-footer--light               // Fond clair
  ps-footer--with-pre            // Avec pre-footer
```

**Props Storybook/Drupal**
```yaml
preFooter: object                 # Contenu pre-footer
  enabled: boolean
  content: html
columns: array                    # Colonnes de liens
  - title: string
    links: array
      - text: string
        url: string
socialLinks: array                # Liens réseaux sociaux
  - platform: string
    url: string
    icon: string
legalLinks: array                 # Liens légaux
  - text: string
    url: string
copyright: string                 # Texte copyright
theme: 'dark' | 'light'          # Thème
```

**Priorité** : 🔴 HAUTE (présent sur toutes les pages)

---

### ✅ Tag + Date (Component - Molecule)
**Occurrences** : 27

**Composant combinant un tag/badge avec une date**

**BEM Structure**
```
ps-tag-date                       // Block
  ps-tag-date__tag               // Badge/tag
  ps-tag-date__date              // Date

Modificateurs:
  ps-tag-date--horizontal        // Layout horizontal
  ps-tag-date--vertical          // Layout vertical
  ps-tag-date--with-icon         // Avec icône
```

**Props Storybook/Drupal**
```yaml
tag: string                       # Texte du tag
date: string                      # Date (format)
tagColor: string                  # Couleur du tag
layout: 'horizontal' | 'vertical' # Disposition
icon: string                      # Icône (optionnelle)
```

**Priorité** : 🟡 MOYENNE (utile pour articles/actualités)

---

### ✅ Alert/Notification (Component - Molecule)
**Occurrences détectées** :
- Icon / Search / Create alert : 7
- Icon / Ad / Notification : 4
- Connected environment - My alerts : 2

**Usage** : Système d'alertes de recherche + notifications utilisateur

**BEM Structure**
```
ps-alert                          // Block
  ps-alert__icon                 // Icône
  ps-alert__content              // Contenu
  ps-alert__title                // Titre
  ps-alert__message              // Message
  ps-alert__close                // Bouton fermer
  ps-alert__actions              // Actions (boutons)

Modificateurs:
  ps-alert--info                 // Type information
  ps-alert--success              // Type succès
  ps-alert--warning              // Type avertissement
  ps-alert--error                // Type erreur
  ps-alert--notification         // Notification utilisateur
  ps-alert--banner               // Format bannière
  ps-alert--inline               // Format inline
```

**Props Storybook/Drupal**
```yaml
type: 'info' | 'success' | 'warning' | 'error' | 'notification'
title: string                     # Titre (optionnel)
message: string                   # Message principal
icon: string                      # Icône custom
closable: boolean                 # Peut être fermé
actions: array                    # Boutons d'action
  - text: string
    onClick: function
duration: number                  # Durée auto-close (ms)
```

**Priorité** : 🟡 MOYENNE (important pour UX)

---

### ✅ Tooltip (Component - Molecule)
**Occurrences** : 2 (Calculator - Tooltip)

**BEM Structure**
```
ps-tooltip                        // Block
  ps-tooltip__trigger            // Élément déclencheur
  ps-tooltip__content            // Contenu du tooltip
  ps-tooltip__arrow              // Flèche pointer

Modificateurs:
  ps-tooltip--top                // Position haut
  ps-tooltip--bottom             // Position bas
  ps-tooltip--left               // Position gauche
  ps-tooltip--right              // Position droite
  ps-tooltip--dark               // Thème sombre
  ps-tooltip--light              // Thème clair
```

**Props Storybook/Drupal**
```yaml
content: string | html            # Contenu du tooltip
position: 'top' | 'bottom' | 'left' | 'right'
theme: 'dark' | 'light'          # Thème
trigger: 'hover' | 'click' | 'focus' # Type de déclenchement
delay: number                     # Délai d'apparition (ms)
arrow: boolean                    # Afficher la flèche
```

**Priorité** : 🟢 BASSE (usage limité)

---

### ✅ Filter Panel (Component - Molecule)
**Occurrences détectées** :
- Icon / Ad / Filter : 6
- Filters : 1
- Property search - Sort by : 2

**Usage** : Panneau de filtrage pour recherche de propriétés

**BEM Structure**
```
ps-filter-panel                   // Block
  ps-filter-panel__header        // En-tête
  ps-filter-panel__title         // Titre
  ps-filter-panel__toggle        // Bouton toggle (mobile)
  ps-filter-panel__content       // Contenu des filtres
  ps-filter-panel__group         // Groupe de filtres
  ps-filter-panel__group-title   // Titre du groupe
  ps-filter-panel__filter        // Filtre individuel
  ps-filter-panel__actions       // Actions (apply, reset)
  ps-filter-panel__sort          // Section tri

Modificateurs:
  ps-filter-panel--open          // Panel ouvert (mobile)
  ps-filter-panel--sidebar       // Format sidebar (desktop)
  ps-filter-panel--drawer        // Format drawer (mobile)
```

**Props Storybook/Drupal**
```yaml
title: string                     # Titre du panel
filters: array                    # Groupes de filtres
  - groupTitle: string
    filters: array
      - type: 'checkbox' | 'range' | 'select'
        label: string
        options: array
        value: any
sortOptions: array                # Options de tri
  - label: string
    value: string
onApply: function                 # Callback appliquer
onReset: function                 # Callback reset
defaultOpen: boolean              # Ouvert par défaut (mobile)
```

**Priorité** : 🔴 HAUTE (essentiel pour recherche)

---

### ✅ Map View (Component - Molecule)
**Occurrences détectées** :
- Icon / Generic / Pin map : 198
- Icon / Generic / Map : 2
- Property search - Map view : 1

**Usage** : Affichage carte interactive avec pins de propriétés

**BEM Structure**
```
ps-map-view                       // Block
  ps-map-view__container         // Conteneur carte
  ps-map-view__map               // Carte (Google Maps, Leaflet, etc.)
  ps-map-view__controls          // Contrôles (zoom, center, etc.)
  ps-map-view__markers           // Conteneur markers
  ps-map-view__marker            // Marker individuel
  ps-map-view__popup             // Popup info marker
  ps-map-view__toggle            // Bouton toggle map/list

Modificateurs:
  ps-map-view--fullscreen        // Plein écran
  ps-map-view--split             // Vue split (map + liste)
  ps-map-view--with-clusters     // Avec clustering
```

**Props Storybook/Drupal**
```yaml
markers: array                    # Liste des markers
  - id: string
    lat: number
    lng: number
    popup: html | object          # Contenu popup
center: object                    # Centre de la carte
  lat: number
  lng: number
zoom: number                      # Niveau de zoom initial
clustering: boolean               # Activer clustering
fullscreen: boolean               # Mode plein écran
controls: object                  # Configuration contrôles
  zoom: boolean
  fullscreen: boolean
```

**Priorité** : 🔴 HAUTE (feature clé pour recherche immobilière)

---

### ✅ Calculator (Component - Molecule)
**Occurrences** : 2 Calculator + 2 Calculator - Tooltip

**Usage** : Calculateur (probablement calculateur de prêt/financement)

**BEM Structure**
```
ps-calculator                     // Block
  ps-calculator__header          // En-tête
  ps-calculator__title           // Titre
  ps-calculator__tooltip         // Tooltip d'aide
  ps-calculator__fields          // Champs de saisie
  ps-calculator__field           // Champ individuel
  ps-calculator__slider          // Slider (range)
  ps-calculator__result          // Résultat
  ps-calculator__result-label    // Label résultat
  ps-calculator__result-value    // Valeur résultat
  ps-calculator__details         // Détails/breakdown
  ps-calculator__actions         // Actions (reset, save)

Modificateurs:
  ps-calculator--loan            // Type prêt
  ps-calculator--investment      // Type investissement
  ps-calculator--compact         // Version compacte
  ps-calculator--expanded        // Version étendue
```

**Props Storybook/Drupal**
```yaml
type: 'loan' | 'investment'      # Type de calculateur
fields: array                     # Champs de calcul
  - name: string
    label: string
    type: 'number' | 'slider' | 'select'
    min: number
    max: number
    step: number
    value: number
    tooltip: string
result: object                    # Configuration résultat
  label: string
  format: string                  # Format d'affichage
details: array                    # Détails du calcul
  - label: string
    value: number
onCalculate: function             # Callback calcul
```

**Priorité** : 🟡 MOYENNE (feature utile mais non critique)

---

### ⚠️ COMPOSANTS NON DÉTECTÉS (recommandés pour design system complet)

Les composants suivants n'ont **pas été détectés** dans le JSON Figma mais sont **recommandés** pour compléter le design system PS. Ils suivent les mêmes conventions BEM et structures que les composants existants.

---

### 🔶 Tabs (Component - Molecule)
**Statut** : ❌ Non détecté  
**Justification** : Nécessaire pour organiser du contenu dense (détails propriété, compte utilisateur, dashboard)

**BEM Structure**
```
ps-tabs                           // Block
  ps-tabs__nav                   // Navigation des tabs
  ps-tabs__list                  // Liste des onglets
  ps-tabs__item                  // Item d'onglet
  ps-tabs__link                  // Lien d'onglet
  ps-tabs__indicator             // Indicateur visuel (underline)
  ps-tabs__panels                // Conteneur des panels
  ps-tabs__panel                 // Panel de contenu

Modificateurs:
  ps-tabs--horizontal            // Onglets horizontaux (défaut)
  ps-tabs--vertical              // Onglets verticaux
  ps-tabs--underline             // Style underline
  ps-tabs--pills                 // Style pills/boutons
  ps-tabs--centered              // Onglets centrés
```

**Props Storybook/Drupal**
```yaml
tabs: array                       # Liste des onglets
  - id: string
    label: string
    content: html
    icon: string                  # Icône (optionnel)
    disabled: boolean
defaultActive: string             # ID de l'onglet actif par défaut
variant: 'underline' | 'pills'   # Style des onglets
orientation: 'horizontal' | 'vertical'
centered: boolean                 # Centrer les onglets
onChange: function                # Callback changement d'onglet
```

**Cas d'usage** :
- Détails de propriété (Overview, Features, Location, Documents)
- Compte utilisateur (Profile, My properties, My alerts, Settings)
- Dashboard agent (Active listings, Sold, Archived)

**Priorité** : 🟡 MOYENNE (utile pour expérience utilisateur riche)

---

### 🔶 Accordion (Component - Molecule)
**Statut** : ❌ Non détecté  
**Justification** : Essentiel pour FAQ, filtres avancés repliables, sections d'information

**BEM Structure**
```
ps-accordion                      // Block
  ps-accordion__item             // Item d'accordion
  ps-accordion__header           // En-tête cliquable
  ps-accordion__trigger          // Bouton trigger
  ps-accordion__icon             // Icône (expand/collapse)
  ps-accordion__title            // Titre
  ps-accordion__panel            // Panel de contenu
  ps-accordion__content          // Contenu

Modificateurs:
  ps-accordion--simple           // Style simple
  ps-accordion--bordered         // Avec bordures
  ps-accordion--flush            // Sans bordures externes
  ps-accordion--single           // Un seul item ouvert à la fois
  ps-accordion--multiple         // Multiple items ouverts
```

**Props Storybook/Drupal**
```yaml
items: array                      # Liste des items
  - id: string
    title: string
    content: html
    icon: string                  # Icône custom (optionnel)
    defaultOpen: boolean
allowMultiple: boolean            # Autoriser plusieurs ouverts
bordered: boolean                 # Afficher bordures
expandIcon: string                # Icône expand (arrow-down par défaut)
collapseIcon: string              # Icône collapse (arrow-up par défaut)
onChange: function                # Callback changement
```

**Cas d'usage** :
- FAQ (Questions fréquentes)
- Filtres avancés repliables (Property search)
- Sections d'information (Property details - amenities, legal info, etc.)
- Mobile navigation (menu replié)

**Priorité** : 🟡 MOYENNE (améliore UX sur mobile et contenu dense)

---

### 🔶 Breadcrumb (Component - Molecule)
**Statut** : ❌ Non détecté  
**Justification** : **Crucial pour SEO et navigation** dans un site immobilier avec hiérarchie de pages

**BEM Structure**
```
ps-breadcrumb                     // Block
  ps-breadcrumb__list            // Liste (ol/ul)
  ps-breadcrumb__item            // Item (li)
  ps-breadcrumb__link            // Lien
  ps-breadcrumb__separator       // Séparateur (/)
  ps-breadcrumb__current         // Item actuel (non cliquable)

Modificateurs:
  ps-breadcrumb--with-icons      // Avec icônes
  ps-breadcrumb--collapsed       // Collapsed sur mobile
```

**Props Storybook/Drupal**
```yaml
items: array                      # Liste du fil d'ariane
  - text: string
    url: string
    icon: string                  # Icône (optionnel)
separator: string                 # Séparateur (défaut: '/')
currentItem: string               # Texte de la page actuelle
showIcons: boolean                # Afficher icônes
collapseMobile: boolean           # Replier sur mobile
maxItems: number                  # Nombre max d'items avant collapse
```

**Cas d'usage** :
- Navigation : Home > Properties > Paris > Apartments > Property Detail
- Catégories : Home > Commercial > Offices > Location
- SEO : Rich snippets pour Google

**Priorité** : 🔴 HAUTE (important pour SEO et UX)

---

### 🔶 Radio Button (Element - Atom)
**Statut** : ⚠️ Partiellement implémenté via buttons  
**Justification** : Composant form standalone nécessaire pour choix uniques

**BEM Structure**
```
ps-radio                          // Block
  ps-radio__input                // Input natif (hidden)
  ps-radio__circle               // Cercle visuel
  ps-radio__dot                  // Point intérieur
  ps-radio__label                // Label texte

Modificateurs:
  ps-radio--checked              // État coché
  ps-radio--disabled             // État désactivé
  ps-radio--inline               // Affichage inline
  ps-radio--stacked              // Affichage vertical
```

**Props Storybook/Drupal**
```yaml
name: string                      # Attribut name (groupe)
value: string                     # Valeur
label: string                     # Label
checked: boolean                  # État coché
disabled: boolean                 # État désactivé
onChange: function                # Callback changement
```

**Radio Group** (wrapper)
```
ps-radio-group                    // Block
  ps-radio-group__label          // Label du groupe
  ps-radio-group__items          // Conteneur des radios
  ps-radio-group__error          // Message d'erreur

Modificateurs:
  ps-radio-group--inline         // Layout inline
  ps-radio-group--stacked        // Layout vertical
```

**Cas d'usage** :
- Choix unique dans formulaires
- Préférences utilisateur
- Options de configuration

**Priorité** : 🟡 MOYENNE (complète le système de formulaires)

---

### 🔶 Toggle/Switch (Element - Atom)
**Statut** : ❌ Non détecté  
**Justification** : Utile pour paramètres, notifications, préférences on/off

**BEM Structure**
```
ps-toggle                         // Block
  ps-toggle__input               // Input checkbox (hidden)
  ps-toggle__track               // Track/rail
  ps-toggle__thumb               // Bouton mobile
  ps-toggle__label               // Label
  ps-toggle__description         // Description (optionnel)

Modificateurs:
  ps-toggle--on                  // État activé
  ps-toggle--off                 // État désactivé
  ps-toggle--disabled            // État désactivé
  ps-toggle--small               // Petite taille
  ps-toggle--medium              // Taille moyenne (défaut)
  ps-toggle--large               // Grande taille
```

**Props Storybook/Drupal**
```yaml
name: string                      # Attribut name
checked: boolean                  # État activé
label: string                     # Label
description: string               # Description
disabled: boolean                 # État désactivé
size: 'small' | 'medium' | 'large'
onChange: function                # Callback changement
```

**Cas d'usage** :
- Préférences utilisateur (Receive email notifications)
- Paramètres de compte (Public profile, Newsletter)
- Filtres on/off (Show only available properties)

**Priorité** : 🟢 BASSE (nice-to-have pour paramètres)

---

### 🔶 Avatar (Element - Atom)
**Statut** : ⚠️ Partiellement détecté  
**Justification** : Composant réutilisable pour profils utilisateurs, agents, témoignages

**BEM Structure**
```
ps-avatar                         // Block
  ps-avatar__image               // Image
  ps-avatar__initials            // Initiales (fallback)
  ps-avatar__badge               // Badge de statut
  ps-avatar__icon                // Icône (si pas d'image)

Modificateurs:
  ps-avatar--small               // 24px
  ps-avatar--medium              // 32px (défaut)
  ps-avatar--large               // 48px
  ps-avatar--xlarge              // 64px
  ps-avatar--xxlarge             // 96px
  ps-avatar--square              // Forme carrée
  ps-avatar--rounded             // Forme arrondie
  ps-avatar--circle              // Forme circulaire (défaut)
  ps-avatar--online              // Statut en ligne
  ps-avatar--offline             // Statut hors ligne
```

**Props Storybook/Drupal**
```yaml
src: string                       # URL de l'image
alt: string                       # Texte alternatif
initials: string                  # Initiales (fallback)
name: string                      # Nom complet (génère initiales)
size: 'small' | 'medium' | 'large' | 'xlarge' | 'xxlarge'
shape: 'circle' | 'rounded' | 'square'
status: 'online' | 'offline' | 'busy' | 'away' # Statut
badge: string                     # Badge custom
icon: string                      # Icône si pas d'image
```

**Cas d'usage** :
- Header utilisateur connecté
- Liste d'agents immobiliers
- Témoignages clients
- Commentaires/reviews

**Priorité** : 🟡 MOYENNE (améliore personnalisation interface)

---

### 🔶 Progress Bar (Component - Molecule)
**Statut** : ❌ Non détecté  
**Justification** : Utile pour formulaires multi-étapes, upload de fichiers, loading

**BEM Structure**
```
ps-progress                       // Block
  ps-progress__track             // Barre de fond
  ps-progress__bar               // Barre de progression
  ps-progress__label             // Label/pourcentage

Modificateurs:
  ps-progress--linear            // Barre linéaire (défaut)
  ps-progress--circular          // Progress circulaire
  ps-progress--small             // Petite taille
  ps-progress--medium            // Taille moyenne
  ps-progress--large             // Grande taille
  ps-progress--striped           // Avec rayures animées
  ps-progress--indeterminate     // Progression indéterminée
```

**Props Storybook/Drupal**
```yaml
value: number                     # Valeur actuelle (0-100)
max: number                       # Valeur max (défaut: 100)
label: string                     # Label
showLabel: boolean                # Afficher le pourcentage
variant: 'linear' | 'circular'   # Type de progress
size: 'small' | 'medium' | 'large'
striped: boolean                  # Rayures animées
indeterminate: boolean            # Mode indéterminé
color: string                     # Couleur (green par défaut)
```

**Cas d'usage** :
- Upload de documents (Property listing)
- Formulaire multi-étapes (Registration, Property submission)
- Loading général

**Priorité** : 🟢 BASSE (améliore feedback utilisateur)

---

### 🔶 Stepper (Component - Molecule)
**Statut** : ❌ Non détecté  
**Justification** : Essentiel pour formulaires multi-étapes (création annonce, demande financement)

**BEM Structure**
```
ps-stepper                        // Block
  ps-stepper__steps              // Conteneur des steps
  ps-stepper__step               // Step individuel
  ps-stepper__step-marker        // Marker numérique/icône
  ps-stepper__step-label         // Label du step
  ps-stepper__step-description   // Description (optionnel)
  ps-stepper__connector          // Ligne de connexion
  ps-stepper__content            // Contenu du step actif
  ps-stepper__actions            // Actions (next, previous)

Modificateurs:
  ps-stepper--horizontal         // Layout horizontal (défaut)
  ps-stepper--vertical           // Layout vertical
  ps-stepper--linear             // Navigation séquentielle
  ps-stepper--non-linear         // Navigation libre
```

**Props Storybook/Drupal**
```yaml
steps: array                      # Liste des étapes
  - id: string
    label: string
    description: string
    icon: string                  # Icône (optionnel)
    content: html
    optional: boolean
activeStep: number                # Index de l'étape active (0-based)
linear: boolean                   # Navigation séquentielle
orientation: 'horizontal' | 'vertical'
onStepChange: function            # Callback changement d'étape
onComplete: function              # Callback fin du process
```

**Cas d'usage** :
- Création d'annonce (Info → Photos → Price → Publish)
- Demande de financement (Info → Revenue → Documents → Submit)
- Process d'achat (Selection → Viewing → Offer → Contract)
- Registration (Account → Profile → Preferences → Done)

**Priorité** : 🟡 MOYENNE (améliore UX formulaires complexes)

---

### 🔶 Skeleton Loader (Component - Molecule)
**Statut** : ❌ Non détecté  
**Justification** : **Améliore drastiquement la perception de performance** pendant les chargements

**BEM Structure**
```
ps-skeleton                       // Block
  ps-skeleton__line              // Ligne de texte
  ps-skeleton__rect              // Rectangle (image, card)
  ps-skeleton__circle            // Cercle (avatar)
  ps-skeleton__wave              // Animation wave

Modificateurs:
  ps-skeleton--text              // Type texte
  ps-skeleton--rect              // Type rectangle
  ps-skeleton--circle            // Type cercle
  ps-skeleton--animated          // Avec animation
  ps-skeleton--pulsing           // Animation pulsing
  ps-skeleton--wave              // Animation wave
```

**Props Storybook/Drupal**
```yaml
type: 'text' | 'rect' | 'circle' # Type de skeleton
width: string | number            # Largeur
height: string | number           # Hauteur
animation: 'pulse' | 'wave' | false # Type d'animation
count: number                     # Nombre de lignes (pour text)
```

**Skeleton Presets** (composants pré-configurés)
```
ps-skeleton-card                  // Skeleton de card
ps-skeleton-list                  // Skeleton de liste
ps-skeleton-header                // Skeleton de header
ps-skeleton-text                  // Skeleton de paragraphe
```

**Cas d'usage** :
- Loading de cards de propriétés
- Loading de liste de résultats
- Loading de détails de propriété
- Loading du profil utilisateur

**Priorité** : 🟡 MOYENNE (améliore perception de performance)

---

### 🔶 Spinner (Element - Atom)
**Statut** : ❌ Non détecté  
**Justification** : Loading indicator simple pour actions rapides

**BEM Structure**
```
ps-spinner                        // Block
  ps-spinner__circle             // Cercle animé

Modificateurs:
  ps-spinner--small              // 16px
  ps-spinner--medium             // 24px (défaut)
  ps-spinner--large              // 32px
  ps-spinner--xlarge             // 48px
  ps-spinner--green              // Couleur verte
  ps-spinner--purple             // Couleur violette
  ps-spinner--white              // Couleur blanche
```

**Props Storybook/Drupal**
```yaml
size: 'small' | 'medium' | 'large' | 'xlarge'
color: 'green' | 'purple' | 'white' | 'gray'
label: string                     # Label pour accessibilité
```

**Cas d'usage** :
- Loading dans boutons (Submit form, Search)
- Loading inline (Save changes...)
- Loading overlay global

**Priorité** : 🟡 MOYENNE (composant standard nécessaire)

---

### 🔶 Carousel/Slider (Component - Molecule)
**Statut** : ❌ Non détecté  
**Justification** : **Essentiel pour galeries photos de propriétés**

**BEM Structure**
```
ps-carousel                       // Block
  ps-carousel__viewport          // Zone visible
  ps-carousel__container         // Conteneur des slides
  ps-carousel__slide             // Slide individuel
  ps-carousel__controls          // Contrôles
  ps-carousel__prev              // Bouton précédent
  ps-carousel__next              // Bouton suivant
  ps-carousel__pagination        // Pagination (dots)
  ps-carousel__dot               // Dot de pagination
  ps-carousel__counter           // Compteur (1/10)
  ps-carousel__thumbnails        // Miniatures

Modificateurs:
  ps-carousel--fade              // Transition fade
  ps-carousel--slide             // Transition slide (défaut)
  ps-carousel--auto              // Auto-play
  ps-carousel--loop              // Loop infini
  ps-carousel--thumbnails        // Avec miniatures
```

**Props Storybook/Drupal**
```yaml
slides: array                     # Liste des slides
  - image: string
    alt: string
    caption: string
autoplay: boolean                 # Auto-play
interval: number                  # Intervalle auto-play (ms)
loop: boolean                     # Loop infini
transition: 'fade' | 'slide'     # Type de transition
showControls: boolean             # Afficher prev/next
showPagination: boolean           # Afficher dots
showThumbnails: boolean           # Afficher miniatures
showCounter: boolean              # Afficher compteur (1/10)
slidesPerView: number             # Nombre de slides visibles
spaceBetween: number              # Espace entre slides
```

**Cas d'usage** :
- **Galerie photos de propriété** (usage principal)
- Témoignages clients
- Partenaires/logos
- Actualités featured

**Priorité** : 🔴 HAUTE (essentiel pour site immobilier)

---

### 🔶 Table/Datatable (Component - Molecule)
**Statut** : ❌ Non détecté  
**Justification** : Utile pour tableaux de comparaison, listings admin, données complexes

**BEM Structure**
```
ps-table                          // Block
  ps-table__wrapper              // Wrapper scrollable
  ps-table__table                // Table HTML
  ps-table__head                 // Thead
  ps-table__header-cell          // Th
  ps-table__sort-icon            // Icône de tri
  ps-table__body                 // Tbody
  ps-table__row                  // Tr
  ps-table__cell                 // Td
  ps-table__footer               // Tfoot (optionnel)

Modificateurs:
  ps-table--striped              // Lignes alternées
  ps-table--bordered             // Avec bordures
  ps-table--hoverable            // Hover sur lignes
  ps-table--compact              // Padding réduit
  ps-table--responsive           // Responsive (scroll horizontal)
  ps-table--sortable             // Colonnes triables
  ps-table--selectable           // Lignes sélectionnables
```

**Props Storybook/Drupal**
```yaml
columns: array                    # Définition des colonnes
  - key: string
    label: string
    sortable: boolean
    width: string
    align: 'left' | 'center' | 'right'
    render: function              # Custom render
rows: array                       # Données
  - {key: value, ...}
striped: boolean                  # Lignes alternées
bordered: boolean                 # Bordures
hoverable: boolean                # Hover sur lignes
compact: boolean                  # Version compacte
selectable: boolean               # Sélection de lignes
onRowClick: function              # Callback clic ligne
onSort: function                  # Callback tri
```

**Cas d'usage** :
- Comparaison de propriétés (features, prix, surface)
- Dashboard admin (liste des annonces)
- Rapports/statistiques
- Historique de transactions

**Priorité** : 🟢 BASSE (usage limité côté front)

---

### 🔶 Video Player (Component - Molecule)
**Statut** : ❌ Non détecté  
**Justification** : Utile pour visites virtuelles, présentations vidéo de propriétés

**BEM Structure**
```
ps-video-player                   // Block
  ps-video-player__video         // Élément video
  ps-video-player__poster        // Image poster
  ps-video-player__controls      // Contrôles custom
  ps-video-player__play          // Bouton play/pause
  ps-video-player__timeline      // Timeline
  ps-video-player__progress      // Barre de progression
  ps-video-player__volume        // Contrôle volume
  ps-video-player__fullscreen    // Bouton plein écran
  ps-video-player__overlay       // Overlay (play center)

Modificateurs:
  ps-video-player--youtube       // Intégration YouTube
  ps-video-player--vimeo         // Intégration Vimeo
  ps-video-player--native        // Player natif HTML5
  ps-video-player--custom        // Contrôles custom
```

**Props Storybook/Drupal**
```yaml
src: string                       # URL de la vidéo
poster: string                    # Image poster
type: 'native' | 'youtube' | 'vimeo'
autoplay: boolean                 # Auto-play
loop: boolean                     # Loop
muted: boolean                    # Muet par défaut
controls: boolean                 # Afficher contrôles
customControls: boolean           # Contrôles custom
aspectRatio: '16:9' | '4:3' | '1:1' # Ratio
```

**Cas d'usage** :
- Visite virtuelle de propriété
- Présentation vidéo (quartier, projet immobilier)
- Témoignages vidéo
- Tutoriels

**Priorité** : 🟢 BASSE (dépend du contenu vidéo disponible)

---

### 🔶 Toast Notification (Component - Molecule)
**Statut** : ❌ Non détecté (différent de Alert)  
**Justification** : Feedback temporaire pour actions utilisateur (saved, deleted, error)

**BEM Structure**
```
ps-toast                          // Block
  ps-toast__container            // Conteneur global (fixed)
  ps-toast__item                 // Toast individuel
  ps-toast__icon                 // Icône de statut
  ps-toast__content              // Contenu
  ps-toast__title                // Titre
  ps-toast__message              // Message
  ps-toast__close                // Bouton fermer
  ps-toast__progress             // Barre de progression (auto-dismiss)

Modificateurs:
  ps-toast--top-right            // Position haut droite (défaut)
  ps-toast--top-left             // Position haut gauche
  ps-toast--top-center           // Position haut centre
  ps-toast--bottom-right         // Position bas droite
  ps-toast--bottom-left          // Position bas gauche
  ps-toast--bottom-center        // Position bas centre
  ps-toast--success              // Type succès
  ps-toast--error                // Type erreur
  ps-toast--warning              // Type avertissement
  ps-toast--info                 // Type information
```

**Props Storybook/Drupal**
```yaml
type: 'success' | 'error' | 'warning' | 'info'
title: string                     # Titre (optionnel)
message: string                   # Message
icon: string                      # Icône custom
duration: number                  # Durée affichage (ms, 0 = persistant)
position: 'top-right' | 'top-left' | 'bottom-right' | etc.
closable: boolean                 # Bouton fermer
action: object                    # Action personnalisée
  text: string
  onClick: function
```

**Cas d'usage** :
- Confirmation d'action (Saved, Deleted, Updated)
- Erreur (Failed to save, Network error)
- Information (New message received)
- Undo action (Deleted property - Undo)

**Priorité** : 🟡 MOYENNE (améliore feedback utilisateur)

---

---

## 📊 Résumé des composants à implémenter

### ✅ DÉTECTÉS dans le design Figma

#### Priorité HAUTE 🔴 (Must-have - Phase 1)
1. **Footer** (23 occurrences) - Présent sur toutes les pages
2. **Filter Panel** (6+ occurrences) - Essentiel pour recherche
3. **Map View** (200+ pins détectés) - Feature clé immobilier
4. **Breadcrumb** (NON détecté mais **crucial SEO**) - Navigation + référencement
5. **Carousel** (NON détecté mais **essentiel immobilier**) - Galeries photos propriétés

#### Priorité MOYENNE 🟡 (Should-have - Phase 2)
6. **Alert/Notification** (13 occurrences) - Important pour UX
7. **Tag + Date** (27 occurrences) - Utile pour contenu
8. **Calculator** (4 occurrences) - Feature différenciante
9. **Toast Notification** (NON détecté) - Feedback actions utilisateur
10. **Tabs** (NON détecté) - Organisation contenu riche
11. **Accordion** (NON détecté) - FAQ, filtres repliables
12. **Stepper** (NON détecté) - Formulaires multi-étapes
13. **Skeleton Loader** (NON détecté) - Performance perçue
14. **Spinner** (NON détecté) - Loading indicator
15. **Avatar** (Partiellement détecté) - Profils utilisateurs
16. **Radio Button** (Partiellement détecté) - Formulaires

#### Priorité BASSE 🟢 (Nice-to-have - Phase 3)
17. **Tooltip** (2 occurrences) - Usage limité
18. **Toggle/Switch** (NON détecté) - Paramètres
19. **Progress Bar** (NON détecté) - Feedback formulaires
20. **Table** (NON détecté) - Données tabulaires
21. **Video Player** (NON détecté) - Contenu vidéo

---

### 📈 Total Design System PS

**Elements (Atoms)** : 32 composants
- 30 détectés/confirmés
- 2 recommandés (Radio, Toggle, Spinner, Avatar complet)

**Components (Molecules)** : 27 composants
- 15 détectés
- 12 recommandés (Tabs, Accordion, Breadcrumb, Toast, Stepper, Skeleton, Carousel, Table, Video, Progress, Tooltip complet)

**Collections (Organisms)** : 8 composants
- 8 détectés (Header, Hero, SearchForm, CardGrid, FeatureSection, ArticleList, Footer, Map View)

**Layouts (Templates)** : 4 templates
- 4 détectés (PageContainer, TwoColumn, ContentSidebar, Block)

**Pages** : 8+ pages
- 8 identifiées

**TOTAL : 79+ composants** pour un design system PS complet

---

### 🎯 Roadmap d'implémentation recommandée

#### Phase 1 - Foundation (Sprint 1-2) - 15 composants
**Elements critiques**
- Button (4 variantes) ✅
- Icon (50+ icônes) ✅
- Field ✅
- Link ✅
- Checkbox ✅
- Heading ✅
- Text ✅
- Spinner 🆕

**Components essentiels**
- Card ✅
- Dropdown ✅
- SearchBar ✅
- FormField ✅
- HeaderMenuItem ✅
- Breadcrumb 🆕

**Collections clés**
- Header ✅

#### Phase 2 - Core Features (Sprint 3-4) - 20 composants
**Elements**
- Badge ✅
- Avatar 🆕
- Radio Button 🆕
- Flag ✅

**Components**
- Filter Panel ✅
- Map View ✅
- Calculator ✅
- Carousel 🆕
- Alert/Notification ✅
- Toast Notification 🆕
- Tag + Date ✅
- Tabs 🆕
- Accordion 🆕
- Skeleton Loader 🆕
- LanguageSelector ✅

**Collections**
- SearchForm ✅
- Hero ✅
- Footer ✅
- CardGrid ✅
- FeatureSection ✅

**Layouts**
- All 4 templates ✅

#### Phase 3 - Enhancement (Sprint 5-6) - 15 composants
**Elements**
- Toggle/Switch 🆕
- Eyebrow ✅

**Components**
- Modal ✅
- Pagination ✅
- Stepper 🆕
- Progress Bar 🆕
- Tooltip complet ✅
- Table 🆕
- Video Player 🆕

**Collections**
- ArticleList ✅

**Pages**
- All pages ✅

#### Phase 4 - Polish & Documentation (Sprint 7) - Documentation complète
- Documentation Storybook
- Tests a11y
- Tests responsive
- Guidelines d'usage
- Design tokens finalisés

---

---

# 📦 STRUCTURE FICHIERS RECOMMANDÉE

```
src/
  elements/
    button/
      button.twig
      button.stories.js
      button.scss
      button.yml
    icon/
    field/
    ...
  components/
    card/
    dropdown/
    search-bar/
    ...
  collections/
    header/
    hero/
    search-form/
    ...
  layouts/
    page-container/
    two-column/
    ...
  tokens/
    colors.scss
    typography.scss
    spacing.scss
    ...
```

---

# 🔗 RÉFÉRENCES

- **PS Theme** : Storybook Drupal Theme (UCLA Health Sciences)
- **Atomic Design** : Brad Frost methodology
- **BEM** : Block Element Modifier methodology
- **Figma JSON** : atomic-inventory-2025-11-28T00-20-26-045Z.json (325,600 lignes)

---

**Document généré le** : 28 novembre 2025  
**Basé sur** : Analyse automatisée du JSON Figma  
**Méthode** : Détection agressive de patterns + alignement Surface  
**Total composants** : 57+ candidats identifiés
