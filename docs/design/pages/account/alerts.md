# Alertes (Responsive)

Système d'alertes immobilières permettant à l'utilisateur de recevoir des notifications pour les nouveaux biens correspondant à ses critères.

---

## Modèle de contenu

```yaml
alerts:
  items:
    - id: 'alert-456'
      title: 'Office spaces in Paris 8th'
      criteria:
        budgetMax: '€500,000'
        surfaceMin: '100m²'
        surfaceMax: '200m²'
        location: 'Paris 8th arrondissement'
      frequency: 'Daily'              # Daily, Weekly, Monthly
      newAdsCount: 3                  # Nouveaux biens depuis dernière visite
      createdDate: '2025-11-15'
      links:
        seeAds: '/search?alert=456'
        delete: '/account/alerts/456/delete'
  totalCount: 5
```

---

## UX par breakpoint

### Desktop (≥768px)
**Liste verticale** :
- Cartes empilées (1 colonne large)
- Chaque carte : titre alerte bold + critères résumés + fréquence
- Badge orange "3 new ads" si nouveaux biens
- 2 CTAs : "See ads" (primaire vert) + "Delete alert" (lien/icône)
- Séparateurs ou espacement vertical entre cartes

**Layout carte** :
- Contenu principal à gauche (titre, critères, fréquence)
- Badge + CTAs à droite (alignés verticalement ou horizontalement)

### Mobile (<768px)
**Liste pile** :
- App bar : Back "Menu" + titre "My alerts"
- Cartes empilées pleine largeur avec marges latérales
- Layout vertical : titre → critères → fréquence → badge → CTAs empilés
- Espacements généreux (`--size-6`)

---

## Structure carte alerte

**Contenu** :
- **Titre** : "Office spaces in Paris 8th" (bold, 1-2 lignes)
- **Critères** : Liste compacte ou texte résumé
  - Budget : "Up to €500,000"
  - Surface : "100-200m²"
  - Localisation : "Paris 8th arrondissement"
- **Fréquence** : "Notifications : Daily" (texte secondaire)
- **Badge nouveaux biens** : "3 new ads" (orange, conditionnel si > 0)

**Actions** :
- **CTA primaire** : "See ads" (bouton vert) → recherche filtrée
- **CTA secondaire** : "Delete alert" (lien texte + icône poubelle) → modale confirmation

---

## Accessibilité

**Liste** :
- Titre page `<h1>` : "My alerts"
- Structure : `<ul>` + `<li>` pour la liste
- Cartes : `<article>` avec heading `<h2>` (titre alerte)

**Badge** :
- `<span>` avec `aria-label="3 new advertisements"` (nombre prononcé)
- Couleur orange avec contraste AA minimum

**CTAs** :
- "See ads" : bouton avec texte explicite
- "Delete alert" : `aria-label="Delete alert: [alert title]"` (contexte clair)
- Focus visible sur tous boutons/liens

**Modale confirmation** :
- `role="dialog"`, `aria-modal="true"`
- Titre : "Delete this alert?"
- Message : "You will no longer receive notifications for [alert title]"
- Boutons : "Cancel" + "Delete" (danger)

---

## Tokens (Design)

**Cartes** :
- Fond : `--white`
- Bordure : `--border-light`
- Ombre : `--shadow-sm`
- Rayon : `--radius-md`
- Padding : `--size-6` desktop, `--size-4` mobile

**Typo** :
- Titre : bold, `--font-size-7`, couleur `--text-primary`
- Critères : normale, `--font-size-5`, couleur `--text-secondary`
- Fréquence : italique optionnel, `--font-size-4`, couleur `--text-secondary`

**Badge nouveaux biens** :
- Fond : `--warning-bg-subtle` (orange clair)
- Texte : `--warning-text-emphasis` (orange foncé)
- Bordure : `--warning-border`
- Padding : `--size-2` horizontal, `--size-1` vertical
- Rayon : `--radius-full` (pill)

**CTAs** :
- Primaire : fond `--primary`, hover `--primary-hover`
- Secondaire : couleur `--danger`, hover souligné
- Icône poubelle : `data-icon="trash"`, couleur `--danger`

**Espacements** :
- Desktop : gap vertical entre cartes `--size-6`
- Mobile : gap vertical `--size-6`, marges latérales `--size-6`

---

## États & interactions

**Desktop** :
- Hover carte : ombre accentuée `--shadow-md`
- Click "See ads" : navigation vers recherche avec filtres alerte pré-remplis
- Click "Delete alert" : modale confirmation → suppression → toast "Alert deleted" → retrait carte avec animation fade

**Mobile** :
- Tap "See ads" : navigation
- Tap "Delete alert" : modale confirmation → suppression → animation fade out + collapse → toast
- Animation suppression : 300ms fade + 200ms collapse height

---

## Modale confirmation suppression

**Contenu** :
- Titre : "Delete this alert?"
- Message : "You will no longer receive notifications for: [alert title]. This action cannot be undone."
- Boutons :
  - "Cancel" (secondaire, outline)
  - "Delete" (danger, fond rouge)

**Comportement** :
- Close sur Escape
- Focus trap
- Focus retour sur carte après fermeture (si Cancel)
- Si Delete : API call → success → close modale → remove carte → toast

---

## Performance & technique

**API** :
- GET `/api/user/alerts` → liste alertes
- DELETE `/api/user/alerts/{id}` → suppression
- Badge compteur : calculé backend (nouveaux biens depuis `lastSeenDate`)

**Notifications** :
- Email cron job (Drupal) : envoi selon fréquence (daily/weekly/monthly)
- Badge reset après click "See ads" (update `lastSeenDate`)

**Animation** :
- Suppression carte : CSS transition + JS remove DOM après animation

---

## États vides

**Aucune alerte** :
- Message centré : "You haven't created any alerts yet"
- Description : "Set up alerts to be notified when new properties matching your criteria are published"
- CTA : "Create alert" → navigation vers recherche avancée
- Illustration optionnelle (empty state)

---

## Variantes

**Desktop** :
- Layout CTAs : horizontale (côte à côte) vs verticale (empilés)
- Badge position : haut droite vs inline avec titre

**Mobile** :
- CTAs empilés (recommandé) vs côte à côte (si espace suffisant)

---

## Données d'entrée (exemple Storybook)

```twig
{% set alerts = {
  items: [
    {
      id: 'alert-456',
      title: 'Office spaces in Paris 8th',
      criteria: {
        budgetMax: '€500,000',
        surfaceMin: '100m²',
        surfaceMax: '200m²',
        location: 'Paris 8th arrondissement'
      },
      frequency: 'Daily',
      newAdsCount: 3,
      links: {
        seeAds: '/search?alert=456',
        delete: '/account/alerts/456/delete'
      }
    }
  ],
  totalCount: 5
} %}
```

---

## Notes d'implémentation

**Composants** :
- `AlertsList` (organism, responsive avec variantes)
- `AlertCard` (molecule)
- `AlertDeleteModal` (molecule)

**Drupal** :
- Custom module "Property Alerts"
- Entity "Alert" avec champs : user_id, criteria (JSON), frequency, last_seen_date
- Cron pour envoi emails
- Views pour afficher liste utilisateur
- Ajax pour suppression sans rechargement
