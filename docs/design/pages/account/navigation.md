# Navigation compte (Responsive)

Système de navigation pour accéder aux sections du compte (My account, My favorites, My alerts).

---

## Modèle de contenu

```yaml
navigation:
  items:
    - key: 'account'
      label: 'My account'
      href: '/account'
      icon: 'user'          # Mobile uniquement
    - key: 'favorites'
      label: 'My favorites'
      href: '/account/favorites'
      icon: 'heart'         # Mobile uniquement
      badge: '6'            # Optionnel (compteur)
    - key: 'alerts'
      label: 'My alerts'
      href: '/account/alerts'
      icon: 'bell'          # Mobile uniquement
  activeKey: 'account'

# Mobile uniquement
logout:
  label: 'Logout'
  icon: 'logout'
  href: '/logout'

support:
  message: 'Need help managing your account?'
  ctaLabel: 'Contact us'
  ctaHref: '/contact'
  schedule: 'Monday to Friday, 9:00 to 18:00'
```

---

## UX par breakpoint

### Desktop (≥768px)
**Sidebar verticale fixe** (gauche de la page) :
- Liste verticale avec items actifs en surbrillance
- Bordure gauche colorée sur l'item actif
- Peut rester sticky lors du scroll
- Séparateurs fins optionnels entre items
- Pas d'icônes (texte uniquement)
- Logout et support **non inclus** dans la sidebar (gérés ailleurs)

### Mobile (<768px)
**Hub de navigation pleine page** :
- App bar standard : burger + logo + ligne Back "Menu"
- Liste pleine largeur avec 3 items principaux
- Chaque item : icône gauche + label + caret droite
- Touch target 48px minimum (accessibilité)
- Séparateurs fins entre items
- **Bouton Logout** outline pleine largeur après la liste
- **Bloc support** en carte claire en bas (message + CTA + horaires)

---

## Accessibilité

**Commun** :
- Conteneur `<nav>` avec `aria-label="Account navigation"`
- Structure liste sémantique : `<ul>` + `<li>` + `<a>`
- Item actif marqué via `aria-current="page"`
- Focus visible sur tous les liens/boutons (`:focus-visible`)

**Desktop** :
- Liens avec états hover/active distincts
- Navigation clavier fluide (Tab, Shift+Tab)

**Mobile** :
- Touch targets ≥48px (WCAG 2.2 AA)
- Icônes décoratives `aria-hidden="true"`
- Logout : `aria-label="Logout from your account"` (action explicite)
- CTA support : label texte (pas seulement icône)

---

## Tokens (Design)

**Desktop sidebar** :
- Bordure active : `--primary-border` (gauche 3px)
- Fond actif : `--primary-bg-subtle` ou `--light-bg-subtle`
- Texte actif : `--primary-text-emphasis`
- Espacements verticaux : `--size-4` entre items
- Padding horizontal : `--size-6`

**Mobile hub** :
- Couleurs : `--text-primary`, `--text-secondary`, `--border-light`
- Icônes : taille `--size-6` (24px), couleur `--text-primary`
- Caret : `data-icon="chevron-right"`, couleur `--text-secondary`
- Bouton Logout : texte `--primary`, bordure `--primary-border`, outline style
- Carte support : fond `--light-bg-subtle`, bordure `--border-light`, CTA outline
- Espacements : `--size-6` vertical, `--size-6` horizontal

---

## États & interactions

**Desktop** :
- Hover : couleur accent légère sur fond
- Active : bordure gauche + fond accent + texte bold
- Click : navigation vers section

**Mobile** :
- Tap item : navigation vers section correspondante
- Tap Logout : confirmation modale ("Are you sure?") → déconnexion → toast succès
- Tap CTA support : ouvre canal contact (page ou email)

---

## Performance & technique

- Navigation statique (pas de lazy loading)
- Badge compteur : mise à jour dynamique via API (favoris/alertes)
- État actif : géré côté serveur (Drupal) ou via JS (SPA)
- Logout : POST sécurisé avec CSRF token (Drupal)

---

## Variantes

**Desktop** :
- Sidebar sticky vs fixed (décision UX)
- Avec/sans séparateurs entre items

**Mobile** :
- Hub avec/sans bloc support intégré
- Logout en bouton outline vs lien texte

---

## Notes d'implémentation

**Composants utilisés** :
- Desktop : `AccountSidebarNav` (molecule)
- Mobile : `AccountMobileHub` (organism)

**Pattern** :
- Utiliser même structure HTML de base avec classes variants (`.ps-account-nav--desktop`, `.ps-account-nav--mobile`)
- Ou composants séparés si logique trop divergente

**Drupal** :
- Menu custom "Account navigation" avec items dynamiques
- Badge compteur via Views field ou custom module
- Support block via block content ou custom
