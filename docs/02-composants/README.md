# Spécifications des composants

**87 composants organisés selon Atomic Design**

---

## 📊 Vue d'ensemble

### Progression globale : 51/87 (59%)

| Niveau | Specs | Implémentés | Restants | Progression |
|--------|-------|-------------|----------|-------------|
| **Atomes** | 19 | 21 | -2 ⚠️ | ███████████ 110% |
| **Molécules** | 21 | 26 | -5 ⚠️ | ███████████+ 124% |
| **Organismes** | 13 | 2 | 11 | ██░░░░░░░░ 15% |
| **Templates** | 8 | 1 | 7 | █░░░░░░░░░ 13% |
| **Pages** | 8 | 1 | 7 | █░░░░░░░░░ 13% |
| **TOTAL** | **69** | **51** | **18** | ██████░░░░ 74% |

⚠️ **Note** : Certains composants implémentés n'ont pas encore de spec (voir `../AUDIT_COHERENCE.md`).

**Composants sans spec** : input, select, textarea (atoms) | form, card-offer-search, card-offer-slide (molecules)

---

## 🧩 [01. Atomes](./01-atomes/) (19 composants)

**Éléments de base autonomes, non décomposables**

### ✅ Specs complètes (9/19)

| Composant | Fichier | Statut | Score audit | Storybook |
|-----------|---------|--------|-------------|-----------|
| **Badge** | [badge.md](./01-atomes/badge.md) | ✅ Production | 98/100 | [View](http://localhost:6006/?path=/docs/elements-badge--docs) |
| **Button** | [button.md](./01-atomes/button.md) | ✅ Production | 100/100 | [View](http://localhost:6006/?path=/docs/elements-button--docs) |
| **Icon** | [icon.md](./01-atomes/icon.md) | ✅ Production | 100/100 | [View](http://localhost:6006/?path=/docs/elements-icon--docs) |
| **Avatar** | [avatar.md](./01-atomes/avatar.md) | ✅ Production | 100/100 | [View](http://localhost:6006/?path=/docs/elements-avatar--docs) |
| **Divider** | [divider.md](./01-atomes/divider.md) | ✅ Production | 100/100 | [View](http://localhost:6006/?path=/docs/elements-divider--docs) |
| **Link** | [link.md](./01-atomes/link.md) | ✅ Production | 100/100 | [View](http://localhost:6006/?path=/docs/elements-link--docs) |
| **Input** | [input.md](./01-atomes/input.md) | ✅ Spec créée | - | [View](http://localhost:6006/?path=/docs/elements-input--docs) |
| **Select** | [select.md](./01-atomes/select.md) | ✅ Spec créée | - | [View](http://localhost:6006/?path=/docs/elements-select--docs) |
| **Textarea** | [textarea.md](./01-atomes/textarea.md) | ✅ Spec créée | - | [View](http://localhost:6006/?path=/docs/elements-textarea--docs) |

### 📋 À implémenter (10/19)

| Composant | Fichier | Priorité | Dépendances |
|-----------|---------|----------|-------------|
| **Checkbox** | [checkbox.md](./01-atomes/checkbox.md) | Haute | Aucune |
| **Label** | [label.md](./01-atomes/label.md) | Haute | Aucune |
| **Radio** | [radio.md](./01-atomes/radio.md) | Haute | Aucune |
| **Heading** | [heading.md](./01-atomes/heading.md) | Moyenne | Aucune |
| **Text** | [text.md](./01-atomes/text.md) | Moyenne | Aucune |
| **Image** | [image.md](./01-atomes/image.md) | Moyenne | Aucune |
| **Flag** | [flag.md](./01-atomes/flag.md) | Moyenne | icon |
| **Spinner** | [spinner.md](./01-atomes/spinner.md) | Moyenne | Aucune |
| **Eyebrow** | [eyebrow.md](./01-atomes/eyebrow.md) | Basse | Aucune |
| **Collapse** | [collapse.md](./01-atomes/collapse.md) | Basse | icon |

---

## 🔬 [02. Molécules](./02-molecules/) (21 composants)

**Combinaisons simples d'atomes avec Token-First**

### ✅ Specs complètes (4/21)

| Composant | Fichier | Statut | Dépendances | Utilise Token-First |
|-----------|---------|--------|-------------|---------------------|
| **Form** | [form.md](./02-molecules/form.md) | ✅ Spec créée | Aucune | ❌ Non (wrapper) |
| **Form-field** | [form-field.md](./02-molecules/form-field.md) | ✅ Spec créée | input, select, textarea, label | ✅ Oui |
| **Checkboxes** | [checkboxes.md](./02-molecules/checkboxes.md) | ✅ Spec créée | checkbox | ❌ Non (wrapper) |
| **Radios** | [radios.md](./02-molecules/radios.md) | ✅ Spec créée | radio | ❌ Non (wrapper) |

### 📋 À implémenter (17/21)

| Composant | Fichier | Priorité | Dépendances | Utilise Token-First |
|-----------|---------|----------|-------------|---------------------|
| **Card** | [card.md](./02-molecules/card.md) | Haute | button, badge, image | ✅ Oui |
| **Alert** | [alert.md](./02-molecules/alert.md) | Haute | icon, button | ✅ Oui |
| **Breadcrumb** | [breadcrumb.md](./02-molecules/breadcrumb.md) | Haute | link, icon | ✅ Oui |
| **Pagination** | [pagination.md](./02-molecules/pagination.md) | Haute | button, link | ✅ Oui |
| **Tabs** | [tabs.md](./02-molecules/tabs.md) | Moyenne | button | ✅ Oui |
| **Accordion** | [accordion.md](./02-molecules/accordion.md) | Moyenne | collapse, icon | ✅ Oui |
| **Modal** | [modal.md](./02-molecules/modal.md) | Moyenne | button, icon | ✅ Oui |
| **Dropdown** | [dropdown.md](./02-molecules/dropdown.md) | Moyenne | button, link | ✅ Oui |
| **Tooltip** | [tooltip.md](./02-molecules/tooltip.md) | Moyenne | icon | ✅ Oui |
| *(+8 autres)* | Voir dossier | Basse | Variées | ✅ Oui |

---

## 🧬 [03. Organismes](./03-organismes/) (12 composants)

**Sections complexes avec layout responsive**

### 📋 Tous à implémenter (12/12)

| Composant | Fichier | Priorité | Dépendances | Complexité |
|-----------|---------|----------|-------------|------------|
| **Header** | [header.md](./03-organismes/header.md) | Haute | navigation, button, logo | Haute |
| **Footer** | [footer.md](./03-organismes/footer.md) | Haute | link, icon, divider | Haute |
| **Navigation** | [navigation.md](./03-organismes/navigation.md) | Haute | link, dropdown | Haute |
| **Property Grid** | [property-grid.md](./03-organismes/property-grid.md) | Haute | card, pagination, filter | Haute |
| **Hero** | [hero.md](./03-organismes/hero.md) | Moyenne | heading, button, image | Moyenne |
| **Search Bar** | [search-bar.md](./03-organismes/search-bar.md) | Moyenne | input, button, dropdown | Moyenne |
| *(+6 autres)* | Voir dossier | Variée | Variées | Variée |

---

## 📐 [04. Templates](./04-templates/) (8 templates)

**Structures de page réutilisables**

### 📋 Tous à implémenter (8/8)

| Template | Fichier | Type de page | Priorité |
|----------|---------|--------------|----------|
| **Homepage** | [homepage.md](./04-templates/homepage.md) | Accueil | Haute |
| **Property Listing** | [property-listing.md](./04-templates/property-listing.md) | Liste de biens | Haute |
| **Property Detail** | [property-detail.md](./04-templates/property-detail.md) | Fiche bien | Haute |
| **Contact** | [contact.md](./04-templates/contact.md) | Formulaire contact | Moyenne |
| **About** | [about.md](./04-templates/about.md) | Page à propos | Basse |
| *(+3 autres)* | Voir dossier | Variées | Variée |

---

## 📄 [05. Pages](./05-pages/) (8 pages)

**Implémentations complètes avec contenu réel**

### 📋 Toutes à implémenter (8/8)

| Page | Fichier | Template utilisé | Priorité |
|------|---------|------------------|----------|
| **Accueil** | [home.md](./05-pages/home.md) | homepage | Haute |
| **Liste biens** | [properties.md](./05-pages/properties.md) | property-listing | Haute |
| **Détail bien** | [property.md](./05-pages/property.md) | property-detail | Haute |
| *(+5 autres)* | Voir dossier | Variés | Variée |

---

## 📖 Format des spécifications

Chaque fichier de spécification contient :

### Structure standardisée
```markdown
# Nom du composant

**Type** : Atome | Molécule | Organisme | Template | Page
**Priorité** : Haute | Moyenne | Basse
**Statut** : À implémenter | En cours | Implémenté

## Description
[Description détaillée du composant]

## Cas d'usage
[3+ exemples d'utilisation Real Estate]

## Props / Paramètres
[Table complète des props avec types]

## Variantes
[Toutes les variantes visuelles]

## États
[Tous les états interactifs]

## Composition (si molécule/organisme)
[Liste des composants enfants]

## Accessibilité
[Exigences WCAG 2.2 AA]

## Design Tokens
[Tokens requis ou à créer]

## Responsive
[Comportement mobile/tablet/desktop]

## Exemples Twig
[Code d'utilisation]
```

---

## 🎯 Priorités d'implémentation

### Phase 4 : Molécules de base (Haute priorité)
1. Card
2. Alert
3. Form Field
4. Breadcrumb
5. Pagination

### Phase 5 : Organismes essentiels (Haute priorité)
1. Header
2. Footer
3. Navigation
4. Property Grid
5. Search Bar

### Phase 6 : Templates clés (Haute priorité)
1. Homepage
2. Property Listing
3. Property Detail

---

## 🔧 Workflow de développement

Pour implémenter un nouveau composant :

1. **Lire la spec** → `docs/02-composants/{niveau}/{composant}.md`
2. **Consulter le guide** → `docs/04-guide-developpement/creer-composant.md`
3. **Utiliser le prompt AI** → `.github/prompts/create-{atom|molecule|organism}.md`
4. **Créer les 5 fichiers** → `.twig`, `.css`, `.yml`, `.stories.jsx`, `README.md`
5. **Valider** → `.github/prompts/audit-component.md` (score ≥90/100)
6. **Commiter** → Format standardisé avec score audit

---

## 📚 Ressources

- **Composants de référence** : button, badge, avatar (100/100 au score audit)
- **Guide Token-First** : `.github/instructions/02-component-development.md`
- **Prompts AI** : `.github/prompts/`
- **Storybook** : http://localhost:6006

---

**Navigation** : [← Présentation](../01-presentation/) | [Design Tokens →](../03-tokens/)
