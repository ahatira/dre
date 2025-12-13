# Compte utilisateur (Responsive)

Page de gestion du compte utilisateur avec navigation entre sections (My account, My favorites, My alerts), consultation et édition des informations personnelles, gestion des favoris et alertes, et accès au support.

---

## Vue d'ensemble

**3 sections principales** :
1. **My account** - Profil utilisateur (nom, email, mot de passe)
2. **My favorites** - Biens immobiliers sauvegardés
3. **My alerts** - Alertes de recherche configurées

**Navigation** :
- Desktop : Sidebar latérale fixe
- Mobile : Hub de navigation avec liste d'items

**Support & Logout** :
- Desktop : Bloc support en bas de page, Logout dans header
- Mobile : Intégrés dans hub navigation

---

## Objectifs utilisateur

1. **Consulter et modifier** ses informations personnelles
2. **Gérer ses favoris** : voir, supprimer, accéder aux fiches biens
3. **Gérer ses alertes** : voir nouveaux biens, supprimer alertes
4. **Obtenir de l'aide** : contacter le support
5. **Se déconnecter** en sécurité

---

## Architecture responsive

### Desktop (≥768px)
**Layout** : Sidebar gauche + Contenu principal

**Navigation** :
- Sidebar verticale fixe/sticky (largeur ~200-250px)
- 3 items : My account, My favorites, My alerts
- Item actif avec bordure gauche colorée

**Contenu** :
- Zone large (reste de l'espace)
- Profil : formulaires en 1-2 colonnes
- Favoris : grille 3 colonnes
- Alertes : liste verticale cartes

**Support** :
- Bloc bandeau en bas de page (après contenu)
- Logout géré dans header navigation

### Mobile (<768px)
**Layout** : Pages empilées

**Navigation** :
- Hub pleine page (liste items + logout + support)
- App bar standard : burger + logo
- Ligne Back "Menu" au-dessus du titre section

**Contenu** :
- Vue lecture seule : champs empilés avec Edit à droite
- Édition : écran dédié par champ avec helper text
- Favoris : liste pile 1 colonne
- Alertes : cartes empilées 1 colonne

**Support & Logout** :
- Intégrés dans hub navigation
- Logout en bouton outline après liste items
- Support en carte claire sous Logout

---

## Composants par section

### Navigation
- Desktop : `AccountSidebarNav` (molecule)
- Mobile : `AccountMobileHub` (organism)
- Specs : [`navigation.md`](account/navigation.md)

### Profil (My account)
- Desktop : `ProfileView` + `ProfileEditInline` (molecules)
- Mobile : `ProfileOverviewMobile` + `ProfileFieldEditMobile` (molecules)
- Mot de passe : `PasswordChangeForm` (variantes desktop/mobile)
- Specs : [`profile.md`](account/profile.md)

### Favoris (My favorites)
- Desktop : `FavoritesGrid` (organism, 3 colonnes)
- Mobile : `FavoritesList` (organism, liste pile)
- Specs : [`favorites.md`](account/favorites.md)

### Alertes (My alerts)
- Desktop/Mobile : `AlertsList` (organism, responsive)
- Specs : [`alerts.md`](account/alerts.md)

### Support & Logout
- Desktop : `SupportBanner` (molecule, bas de page)
- Mobile : `LogoutButton` + `SupportBlock` (intégrés hub)
- Specs : [`support.md`](account/support.md)

---

## Accessibilité

**Navigation** :
- Structure `<nav>` avec `<ul>` + `<li>`
- Item actif : `aria-current="page"`
- Focus visible sur tous liens/boutons

**Formulaires** :
- Labels associés (`<label for="">` ou `aria-labelledby`)
- Helper text : `aria-describedby`
- Messages erreur : `aria-live="polite"`
- Validation inline avec feedback visuel + textuel

**Modales** :
- `role="dialog"`, `aria-modal="true"`
- Focus trap
- Close sur Escape
- Titre avec `aria-labelledby`

**Interactions** :
- Touch targets ≥48px (mobile)
- États hover/focus/active distincts
- Animations respectueuses `prefers-reduced-motion`

---

## Tokens (Design)

**Espacements** :
- Section padding : `--size-8` vertical, `--size-6` horizontal
- Gap grilles/listes : `--size-6`
- Marges mobiles : `--size-6` latérales

**Couleurs** :
- Navigation active : `--primary` (bordure, fond subtle)
- CTAs primaires : `--primary`
- CTAs secondaires : `--neutral` ou `--danger` (suppression)
- Badges : `--warning` (nouveaux biens)

**Typo** :
- Titres : bold, `--font-size-5` ou `--font-size-6`
- Labels : bold, `--font-size-3`
- Corps : `--font-size-3`
- Helper text : `--font-size-2`, couleur `--text-secondary`

**Bordures & Ombres** :
- Cartes : `--border-light`, `--shadow-sm`
- Inputs : `--border-default`, focus `--border-focus`
- Séparateurs : `--border-light`

---

## États & interactions

**Navigation** :
- Click item : charge section correspondante (client-side ou page refresh)
- Mobile : tap item → navigation vers section

**Profil** :
- Edit → champ actif → Save/Cancel
- Success → toast "Profile updated" + retour lecture seule
- Error → message sous champ + reste éditable

**Favoris** :
- Click cœur → modale confirmation → suppression → toast + animation
- Click carte/CTA → navigation vers fiche bien

**Alertes** :
- Click "See ads" → recherche filtrée
- Click "Delete" → modale → suppression → toast

**Logout** :
- Click → modale confirmation (optionnel) → POST logout → redirect → toast

---

## Performance

**Lazy loading** :
- Images favoris : IntersectionObserver
- Liste alertes : pagination ou infinite scroll (mobile)

**API calls** :
- GET `/api/user/profile` (profil)
- PATCH `/api/user/profile` (mise à jour)
- GET `/api/user/favorites` (liste favoris)
- DELETE `/api/user/favorites/{id}` (retrait)
- GET `/api/user/alerts` (liste alertes)
- DELETE `/api/user/alerts/{id}` (suppression)

**Optimisations** :
- Cache profil (session storage)
- Debounce validation (500ms)
- Toast pooling (1 seul toast à la fois)

---

## Données d'entrée (exemple global)

```yaml
account:
  profile:
    firstName: 'Enzo'
    lastName: 'Lecompte'
    email: 'enzo.lecompte@gmail.com'
    passwordMasked: '******************'
    canEditEmail: false
  
  favorites:
    items: [...]
    totalCount: 6
  
  alerts:
    items: [...]
    totalCount: 5
  
  navigation:
    activeSection: 'account'  # 'account' | 'favorites' | 'alerts'
  
  support:
    message: 'Need help managing your account?'
    ctaLabel: 'Contact us'
    ctaHref: '/contact'
    schedule: 'Monday to Friday, 9:00 to 18:00'
```

---

## Liens vers sous-spécifications

- **Navigation** : [`navigation.md`](account/navigation.md) - Sidebar desktop + Hub mobile
- **Profil** : [`profile.md`](account/profile.md) - Vue/édition infos personnelles + mot de passe
- **Favoris** : [`favorites.md`](account/favorites.md) - Grille desktop + Liste mobile
- **Alertes** : [`alerts.md`](account/alerts.md) - Liste cartes responsive
- **Support** : [`support.md`](account/support.md) - Bloc aide + Logout (mobile)

---

## Notes d'implémentation

**Drupal** :
- User entity pour profil
- Flag module pour favoris
- Custom module "Property Alerts" pour alertes
- Permissions : `edit own account`, `flag/unflag favorites`, `manage own alerts`
- Routes : `/user/account`, `/user/favorites`, `/user/alerts`

**Frontend** :
- Comportements Drupal avec `once()` pour idempotence
- Ajax pour éditions inline (pas de rechargement page)
- Toast system : Drupal messages ou JS custom
- Responsive breakpoint : 768px (variable `--breakpoint-md`)

**Storybook** :
- Stories par composant individuel
- Story page complète "Account Full Page" avec toutes sections
- Mocks réalistes avec Faker.js
