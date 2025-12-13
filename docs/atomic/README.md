# Atomic Design - Inventaire des composants

Documentation des composants par niveau d'Atomic Design (Brad Frost).

## 📚 Fichiers

- **[elements.md](elements.md)** - Atoms (25) - Éléments HTML de base autonomes
- **[components.md](components.md)** - Molecules (24) - Unités fonctionnelles composées d'atomes
- **[collections.md](collections.md)** - Organisms (15) - Sections complexes composées de molécules
- **[layouts.md](layouts.md)** - Templates (10) - Structures de page Drupal
- **[pages.md](pages.md)** - Pages (12) - Exemples de pages complètes

## 🎯 Usage

Ces inventaires servent de référence rapide pour :
- **Identifier** les composants disponibles par niveau
- **Naviguer** vers les spécifications détaillées dans `docs/design/`
- **Planifier** les dépendances de développement

⚠️ **Note** : Les inventaires ne remplacent pas les spécifications détaillées. Pour l'implémentation complète d'un composant, consultez `.github/instructions/02-component-development.md`.

## 🔗 Références croisées

### Vers les specs détaillées
Les composants de pages ont des spécifications complètes sous `docs/design/pages/` :
- **Offre** → `design/pages/property-detail/`
- **Recherche** → `design/pages/search-results/` et `search-results-mobile/`
- **Compte** → `design/pages/account/` et `account-mobile/`

### Vers les instructions de développement
- `.github/instructions/02-component-development.md` - Workflow complet
- `.github/instructions/03-technical-implementation.md` - Standards code
- `.github/instructions/04-quality-assurance.md` - Validation

## 📊 Statut actuel

**Total**: 86 composants documentés
- Elements (Atoms): 25
- Components (Molecules): 24  
- Collections (Organisms): 15
- Layouts (Templates): 10
- Pages: 12

**Implémentés**: 13/86 (15%)
- Voir `docs/ROADMAP.md` pour le plan de développement
