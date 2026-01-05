# Support & Déconnexion (Responsive)

Bloc d'aide et action de déconnexion pour l'utilisateur.

---

## Modèle de contenu

```yaml
support:
  message: 'Need help managing your account?'
  ctaLabel: 'Contact us'
  ctaHref: '/contact'
  schedule: 'Monday to Friday, 9:00 to 18:00'

logout:
  label: 'Logout'
  icon: 'logout'
  href: '/user/logout'
  confirmationRequired: true
```

---

## UX par breakpoint

### Desktop (≥768px)
**Bloc support uniquement** (bas de page) :
- Bandeau pleine largeur ou carte centrée
- Layout : texte centré ou aligné gauche
- Message en bold ou normal
- CTA bouton outline/secondary
- Horaires en texte secondaire plus petit
- Pas de bouton Logout (géré dans header/menu utilisateur)

**Placement** :
- Fin de page, après toutes les sections principales
- Séparé visuellement (espacement généreux ou bordure supérieure)

### Mobile (<768px)
**Hub navigation intégré** :
- Bouton Logout outline pleine largeur (après liste navigation)
- Icône sortie à gauche + texte "Logout"
- Espacement généreux au-dessus pour séparer de la navigation

**Bloc support** (sous Logout) :
- Carte claire avec bordure légère
- Message en bold centré ou gauche
- CTA outline pleine largeur ou largeur auto centré
- Horaires en texte secondaire sous le CTA
- Padding généreux

---

## Accessibilité

**Bloc support** :
- Titre implicite via message bold ou `<h2>` invisible
- CTA bouton avec label texte (pas seulement icône)
- Contraste texte/fond conforme WCAG 2.2 AA
- Focus visible sur CTA

**Logout** (mobile) :
- Bouton `<button>` ou lien `<a>` avec `aria-label="Logout from your account"`
- Icône `aria-hidden="true"` (texte suffit)
- Si confirmation modale : `role="dialog"`, `aria-modal="true"`, focus trap

---

## Tokens (Design)

**Bloc support** (desktop + mobile) :
- Fond : `--light-bg-subtle` ou `--gray-50`
- Bordure : `--border-light` (optionnel)
- Ombre : `--shadow-sm` (optionnel, si carte)
- Rayon : `--radius-md`
- Padding : `--size-8` vertical, `--size-6` horizontal

**Typo** :
- Message : bold optionnel, `--font-size-6`, couleur `--text-primary`
- Horaires : `--font-size-4`, couleur `--text-secondary`

**CTA support** :
- Bordure : `--primary-border`
- Texte : `--primary`
- Hover : fond `--primary-bg-subtle`
- Padding : `--size-3` vertical, `--size-5` horizontal

**Logout** (mobile uniquement) :
- Bouton outline pleine largeur
- Bordure : `--primary-border`
- Texte : `--primary`
- Icône : taille `--size-5`, couleur `--primary`
- Hover : fond `--primary-bg-subtle`
- Padding : `--size-3` vertical

---

## États & interactions

**Desktop** :
- Hover CTA support : fond accent léger + bordure accentuée
- Click CTA : navigation vers page contact ou ouvre modale contact
- Logout non présent dans cette section (header/menu)

**Mobile** :
- Tap Logout :
  - Si confirmation activée : modale "Are you sure you want to logout?"
  - Si pas confirmation : POST `/user/logout` direct → redirection homepage + toast "You have been logged out"
- Tap CTA support : navigation contact ou ouvre modale

---

## Modale confirmation Logout

**Contenu** :
- Titre : "Logout from your account?"
- Message : "You will be redirected to the homepage."
- Boutons :
  - "Cancel" (secondaire, outline)
  - "Logout" (primaire, vert ou neutre)

**Comportement** :
- Close sur Escape
- Focus trap
- Confirm → POST `/user/logout` → redirect homepage → toast succès

---

## Variantes

**Desktop** :
- Support en bandeau pleine largeur vs carte centrée
- Texte centré vs aligné gauche
- Avec/sans horaires affichés

**Mobile** :
- Logout avec/sans confirmation modale
- Support intégré dans hub nav vs section séparée bas de page

---

## Performance & technique

**Logout** :
- POST sécurisé avec CSRF token (Drupal)
- Invalidation session côté serveur
- Redirection vers homepage après succès
- Toast : "You have been logged out successfully"

**Support** :
- Lien static vers page contact (pas de JS nécessaire)
- Ou modal contact avec form Ajax (optionnel)

---

## Données d'entrée (exemple)

```twig
{% set support = {
  message: 'Need help managing your account?',
  ctaLabel: 'Contact us',
  ctaHref: '/contact',
  schedule: 'Monday to Friday, 9:00 to 18:00'
} %}

{% set logout = {
  label: 'Logout',
  icon: 'logout',
  href: '/user/logout',
  confirmationRequired: true
} %}
```

---

## Placement contextuel

**Desktop** :
- Bloc support : fin de page compte (après favoris/alertes)
- Logout : header navigation ou menu user dropdown (pas dans cette section)

**Mobile** :
- Logout + Support : intégrés dans hub navigation mobile
- Ordre : Navigation items → Logout → Support
- Alternative : Support en footer global (pas spécifique page compte)

---

## Notes d'implémentation

**Composants** :
- Desktop : `SupportBanner` (molecule)
- Mobile : `LogoutButton` + `SupportBlock` (molecules, ou intégrés dans `AccountMobileHub`)

**Drupal** :
- Support block : block content type ou hardcoded
- Logout : Drupal user logout route
- Confirmation modale : JS custom avec Drupal behaviors
- Toast messages : Drupal messages system ou JS toast library
