# Bannière calculateur loyer (Responsive)

Bannière utilitaire permettant d'estimer le loyer d'un bien selon critères (surface, localisation, type).

---

## Modèle de contenu

```yaml
calculator:
  title: 'Calculate the rental price of your property'
  description: 'Get an estimate based on location, surface area, and property type.'
  
  fields:
    location:
      type: 'autocomplete'         # avec API adresses
      placeholder: 'Enter city or postal code'
      required: true
      icon: 'pin'
    
    surface:
      type: 'number'
      placeholder: 'Surface area (m²)'
      required: true
      min: 10
      max: 500
      icon: 'area'
    
    propertyType:
      type: 'select'
      placeholder: 'Property type'
      required: true
      options:
        - value: 'apartment'
          label: 'Apartment'
        - value: 'house'
          label: 'House'
        - value: 'studio'
          label: 'Studio'
        - value: 'loft'
          label: 'Loft'
      icon: 'building'
  
  cta:
    label: 'Calculate'
    href: '/tools/calculator'      # ou action inline
    variant: 'primary'
  
  disclaimer: 'Estimates are indicative only and may vary.'
```

---

## UX par breakpoint

### Desktop (≥768px)
**Bannière horizontale** (page recherche ou footer) :
- Layout : 1 ligne avec titre + 3 champs inline + CTA
- Fond couleur subtle : `--primary-bg-subtle` ou `--gray-50`
- Padding large : `--size-8`
- Rayon : `--radius-lg`

**Structure** :
```
┌────────────────────────────────────────────────────────────┐
│ [Icon] Calculate rental price     [Location] [Surface] [...│
│ Get an estimate...                 [Type]    [CTA]         │
└────────────────────────────────────────────────────────────┘
```

**Disposition** :
- **Gauche (30%)** : Icône + titre + description
- **Droite (70%)** : Champs formulaire + CTA (flexbox inline)

### Mobile (<768px)
**Bannière verticale** :
- Layout empilé : titre + description + champs (1 colonne) + CTA
- Padding réduit : `--size-6`
- Champs pleine largeur

**Structure** :
```
┌─────────────────────────┐
│ [Icon] Calculate rental │
│ Get an estimate...      │
│ [Location field]        │
│ [Surface field]         │
│ [Type select]           │
│ [CTA Calculate]         │
└─────────────────────────┘
```

---

## Champs formulaire

### Location (Autocomplete)
**Desktop** :
- Input texte avec icône pin
- Autocomplete dropdown avec suggestions API
- Largeur : 200px

**Mobile** :
- Pleine largeur
- Autocomplete en overlay si besoin

**Fonctionnalité** :
- API Adresse (gouvernement FR) ou Google Places
- Suggestions en temps réel (debounce 300ms)
- Sélection → capture ville + code postal

### Surface (Number)
**Desktop** :
- Input number avec icône area
- Min : 10, Max : 500
- Largeur : 120px
- Suffix : "m²" (intégré ou label)

**Mobile** :
- Pleine largeur
- Clavier numérique natif (input type="number")

**Validation** :
- Valeur hors limites : message erreur
- Champ vide : erreur "Required field"

### Property Type (Select)
**Desktop** :
- Select dropdown custom avec icône building
- Options : Apartment, House, Studio, Loft
- Largeur : 150px

**Mobile** :
- Pleine largeur
- Native select ou custom dropdown adapté

---

## CTA "Calculate"

**Desktop** :
- Bouton primaire standard
- Largeur auto (padding `--size-5`)
- Aligné à droite des champs

**Mobile** :
- Pleine largeur
- Sticky en bas de la bannière

**Action** :
- **Inline** : affiche résultat dans modal ou toast
- **Redirect** : navigation vers `/tools/calculator?location=...&surface=...&type=...`

**Validation** :
- Tous champs requis : CTA désactivé si incomplet
- Tooltip si hover et formulaire invalide : "Fill all fields"

---

## Résultat (si action inline)

**Modal estimée** :
- Titre : "Estimated rental price"
- Montant : format large "€2,300 - €2,800 / month"
- Détails : localisation, surface, type
- Disclaimer : texte légal
- CTA secondaire : "Refine estimate" (retour formulaire)
- CTA primaire : "Contact an expert"

**Mobile** :
- Bottom sheet ou modal plein écran
- Mêmes éléments mais layout vertical

---

## Accessibilité

**Bannière** :
- `role="region"`, `aria-labelledby="calculator-title"`
- Ordre tab logique : titre → champs → CTA

**Champs** :
- Labels visibles ou `aria-label` si placeholders seuls
- Autocomplete : `aria-autocomplete="list"`, `aria-controls="suggestions-list"`
- Select : native accessible ou custom avec ARIA (combobox pattern)
- Erreurs : `aria-describedby="field-error"`, `aria-invalid="true"`

**CTA** :
- Focus visible : `--border-focus` + outline
- Désactivé : `aria-disabled="true"` + style grisé

**Modal résultat** :
- `role="dialog"`, `aria-modal="true"`
- Focus trap, ESC ferme

---

## Tokens (Design)

**Bannière** :
- Fond : `--primary-bg-subtle` (accent léger) ou `--gray-50` (neutre)
- Bordure : `--border-light` (optionnel)
- Rayon : `--radius-lg`
- Padding : `--size-8` (desktop), `--size-6` (mobile)
- Ombre : `--shadow-md` (optionnel, léger lift)

**Titre** :
- Font : `--font-heading-md`
- Couleur : `--text-primary`
- Icon : taille `--size-6`, couleur `--primary`

**Description** :
- Font : `--font-body-sm`
- Couleur : `--text-secondary`

**Champs** :
- Fond : `--white`
- Bordure : `--border-default`, rayon `--radius-md`
- Focus : bordure `--border-focus`, ombre `--shadow-focus`
- Erreur : bordure `--border-error`, texte `--danger`
- Padding : `--size-4`
- Gap entre champs : `--size-4` (desktop), `--size-5` (mobile)

**Icônes champs** :
- Taille : `--size-5` (20px)
- Couleur : `--neutral` (inactif), `--primary` (focus)

**CTA** :
- Variante : `--primary` (fond `--primary`, texte blanc)
- Hover : `--primary-hover`
- Disabled : fond `--gray-300`, texte `--text-disabled`

**Modal résultat** :
- Fond : `--white`
- Overlay : `--overlay-dark-medium`
- Montant : `--font-heading-xl`, couleur `--primary` ou `--success`
- Disclaimer : `--font-body-xs`, couleur `--text-secondary`, italic

---

## États & interactions

### Formulaire
**Focus champs** :
- Bordure change couleur → `--border-focus`
- Icône change couleur → `--primary`

**Erreur validation** :
- Champ bordure rouge + shake animation (optionnel)
- Message erreur sous champ : "Please enter a valid surface area"

**Autocomplete location** :
- Input → dropdown suggestions (max 5)
- Arrow down/up : navigation clavier
- Enter : sélection
- ESC : ferme dropdown

**CTA désactivé** :
- Grisé, cursor not-allowed
- Tooltip hover : "Fill all required fields"

### Action Calculate

**Inline (modal)** :
- Click CTA → Loader spinner 1-2s
- Modal slide in avec résultat
- Backdrop overlay fade in

**Redirect** :
- Navigation vers page `/tools/calculator` avec query params
- Champs pré-remplis si retour

**Erreur API** :
- Toast ou message inline : "Unable to calculate. Please try again."
- Retry button

---

## Performance

**Optimisations** :
- Autocomplete : debounce 300ms requêtes API
- Cache suggestions récentes (session storage)
- Calcul estimation : API rapide (<500ms)

**Lazy load** :
- Script autocomplete chargé au focus champ location

---

## API & Intégration

**Endpoints** :
- GET `/api/address/autocomplete?q={query}` : suggestions adresses
- POST `/api/calculator/estimate` : calcul estimation
  - Body : `{ location, surface, propertyType }`
  - Response : `{ minPrice, maxPrice, averagePrice }`

**Données** :
- Base prix : table Drupal ou API externe (ex: SeLoger, LeBonCoin)
- Facteurs : localisation (arrondissement), surface, type, marché

---

## Données d'entrée (exemple)

```twig
{% set calculator = {
  title: 'Calculate the rental price of your property',
  description: 'Get an estimate based on location, surface area, and property type.',
  fields: {
    location: { type: 'autocomplete', placeholder: 'Enter city or postal code', required: true, icon: 'pin' },
    surface: { type: 'number', placeholder: 'Surface area (m²)', required: true, min: 10, max: 500, icon: 'area' },
    propertyType: {
      type: 'select',
      placeholder: 'Property type',
      required: true,
      options: [
        { value: 'apartment', label: 'Apartment' },
        { value: 'house', label: 'House' }
      ],
      icon: 'building'
    }
  },
  cta: { label: 'Calculate', href: '/tools/calculator', variant: 'primary' },
  disclaimer: 'Estimates are indicative only and may vary.'
} %}
```

---

## Variantes

**Placement** :
- **Bannière page recherche** : au-dessus résultats ou entre filtres et résultats
- **Bannière footer** : section utilitaire bas de page (toutes pages)
- **Widget sidebar** : version compacte dans sidebar (1 colonne)

**Action** :
- Inline modal (résultat immédiat) vs redirect page outils

**Style** :
- Fond accent (`--primary-bg-subtle`) vs neutre (`--gray-50`)
- Avec/sans icône illustrative

---

## Messages utilisateur

**Validation** :
- Location vide : "Please enter a location"
- Surface invalide : "Surface must be between 10 and 500 m²"
- Type non sélectionné : "Please select a property type"

**Résultat modal** :
- Montant : "Estimated rental price: €2,300 - €2,800 / month"
- Disclaimer : "This estimate is based on market data and may vary. Contact an expert for a precise valuation."

**Erreur API** :
- "Unable to calculate rental price. Please try again later."

---

## Notes d'implémentation

**Composants** :
- `CalculatorBanner` (organism, responsive)
- `CalculatorForm` (molecule, formulaire)
- `CalculatorResult` (molecule, modal résultat)
- Inputs : réutilisation atoms `Input`, `Select`, `Autocomplete`

**Drupal** :
- Custom module "Calculator"
- Config : API keys (si API externe)
- Cache estimations : 1h (économise API calls)
- Form API : validation backend + frontend

**JavaScript** :
- Autocomplete : library Autocomplete.js ou custom
- Validation : HTML5 + custom JS
- Modal : library Modal.js ou native `<dialog>`
- State : React/Vue ou vanilla JS
