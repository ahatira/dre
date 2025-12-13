# Profil utilisateur (Responsive)

Vue et édition des informations personnelles du compte (prénom, nom, email, mot de passe).

---

## Modèle de contenu

```yaml
profile:
  firstName: 'Enzo'
  lastName: 'Lecompte'
  email: 'enzo.lecompte@gmail.com'
  passwordMasked: '******************'
  
permissions:
  canEditFirstName: true
  canEditLastName: true
  canEditEmail: false       # Email souvent verrouillé
  canEditPassword: true

states:
  mode: 'view'              # 'view' | 'edit-field' | 'edit-password'
  editingField: null        # 'firstName' | 'lastName' | 'email' | null
  
helperTexts:
  firstName: 'This is the first name that appears on your travel document, such as your driver's license or passport.'
```

---

## UX par breakpoint

### Desktop (≥768px)
**Vue lecture seule** :
- Champs en pile verticale (label + valeur grise)
- Lien/bouton "Edit" aligné à droite de chaque champ éditable
- Séparateurs fins entre sections
- Mot de passe masqué avec lien "Edit" → ouvre section dédiée

**Édition inline** :
- Click "Edit" → champ devient éditable
- Autres champs restent en lecture seule
- Boutons "Save" + "Cancel" apparaissent
- Layout peut basculer en 2 colonnes pour certains champs

**Mot de passe** (section séparée) :
- 2 champs côte à côte ou empilés : Current + New password
- Toggle visibilité (icône œil) dans chaque champ
- Boutons "Save" + "Cancel"

### Mobile (<768px)
**Vue lecture seule** :
- App bar : Back "Menu" + titre "My account"
- Champs empilés pleine largeur
- Label + valeur grise + bouton "Edit" à droite (icône + texte)
- Séparateurs fins entre champs
- Marges latérales généreuses (`--size-6`)

**Édition d'un champ** :
- Titre du champ en haut avec lien "Cancel" à droite
- Helper text sous le titre (si applicable)
- Champ texte pleine largeur activé
- CTA primaire "Save the modifications" pleine largeur sous le champ
- Autres champs visibles plus bas en lecture seule (scrolling)

**Mot de passe** (écran dédié) :
- Titre "Password" + lien "Cancel" à droite
- 2 champs empilés : Current + New password
- Toggle visibilité dans chaque champ (icône œil)
- CTA primaire pleine largeur "Save the modifications"

---

## Accessibilité

**Vue lecture seule** :
- Titre page `<h1>` : "My account"
- Labels de champs en `<dt>`, valeurs en `<dd>` (liste de définition) ou structure similaire
- Boutons "Edit" avec texte visible (pas seulement icône)
- Champs non éditables : retirer bouton Edit ou indiquer état verrouillé

**Édition** :
- Focus automatique sur le champ édité (desktop)
- Label associé via `<label for="">` ou `aria-labelledby`
- Helper text associé via `aria-describedby`
- Messages d'erreur annoncés via `aria-live="polite"`
- Cancel : `aria-label` explicite ("Cancel editing first name")

**Mot de passe** :
- Champs `type="password"` avec `autocomplete="current-password"` / `"new-password"`
- Toggle visibilité : bouton avec `aria-pressed` + `aria-label` dynamique
- Exigences sécurité (si affichées) : `aria-describedby` pointant vers liste

---

## Tokens (Design)

**Vue lecture seule** :
- Typo labels : `--font-weight-bold`, couleur `--text-primary`
- Typo valeurs : couleur `--text-secondary`
- Séparateurs : `--border-light`, 1px
- Espacements verticaux : `--size-6` entre champs
- Padding latéral (mobile) : `--size-6`

**Édition** :
- Champs input : bordure `--border-default`, focus `--border-focus` + `--focus-ring`
- Rayon : `--radius-md` (formulaires globaux)
- CTA primaire : fond `--primary`, hover `--primary-hover`, active `--primary-active`
- Lien Cancel : couleur `--primary`, hover souligné
- Helper text : couleur `--text-secondary`, taille `--font-size-2`

**Mot de passe** :
- Icône œil : couleur `--text-secondary`, position absolute droite
- Messages erreur : couleur `--danger`, taille `--font-size-2`

---

## États & interactions

**Desktop** :
- Click "Edit" → champ devient input, focus auto, Save + Cancel apparaissent
- Click "Cancel" → revert sans sauvegarder, retour lecture seule
- Click "Save" → validation, POST API, succès → lecture seule + toast
- Erreur validation → message sous champ, reste en édition

**Mobile** :
- Tap "Edit" → ouvre écran édition champ avec helper text
- Tap "Cancel" (haut droite) → retour vue lecture seule
- Tap "Save the modifications" → validation, succès → retour + toast
- Mot de passe : tap toggle → bascule `type="password"` ↔ `type="text"`

---

## Validation

**Champs texte** (firstName, lastName) :
- Requis, min 2 caractères
- Pas de chiffres/caractères spéciaux (règles métier)

**Email** :
- Format email valide
- Souvent verrouillé après création compte

**Mot de passe** :
- Current password : requis, vérification serveur
- New password : min 8 caractères, 1 maj, 1 min, 1 chiffre (exemple)
- Confirmation new password (optionnel) : doit correspondre

---

## Performance & technique

- Édition inline (desktop) : pas de rechargement page
- API PATCH `/api/user/{id}` pour champs individuels
- Mot de passe : endpoint séparé `/api/user/{id}/password` (sécurité)
- Toast success : "Profile updated successfully"
- Toast error : "An error occurred. Please try again."
- Debounce validation (optionnel) : 500ms après dernière saisie

---

## Données d'entrée (exemple)

```twig
{% set profile = {
  firstName: 'Enzo',
  lastName: 'Lecompte',
  email: 'enzo.lecompte@gmail.com',
  passwordMasked: '******************',
  canEditEmail: false
} %}
```

---

## Notes d'implémentation

**Composants** :
- Desktop : `ProfileView` + `ProfileEditInline` (molecules)
- Mobile : `ProfileOverviewMobile` + `ProfileFieldEditMobile` (molecules)
- Mot de passe : `PasswordChangeForm` (molecule, variantes desktop/mobile)

**Drupal** :
- User entity edit form
- Custom form handlers pour validation métier
- Permissions : `edit own account` + custom permissions
- Ajax callbacks pour édition inline (desktop)
