# PS Design System - Index Complet
# Documentation générée à partir de l'analyse Figma

## 📊 Vue d'ensemble

**Composants totaux** : 87 composants  
**Tokens de design** : 7 fichiers YAML  
**Documentation générée** : 95+ fichiers

---

## 🗂️ Structure complète

```
design/
├── README.md                           ✅ Documentation principale
├── COMPONENT_MANIFEST.yml              ✅ Manifeste de génération
├── INDEX.md                            ✅ Ce fichier
│
├── tokens/                             ✅ 7/7 tokens générés
│   ├── colors.yml                     ✅ Palette complète + WCAG
│   ├── typography.yml                 ✅ BNPP Sans + presets
│   ├── spacing.yml                    ✅ Système 4px
│   ├── borders.yml                    ✅ Widths + radius
│   ├── layout.yml                     ✅ Grid + containers
│   ├── shadows.yml                    ✅ Élévations
│   └── transitions.yml                ✅ Animations
│
├── atoms/                              🟡 1/19 générés
│   ├── button.md                      ✅ COMPLET
│   ├── icon.md                        ⏳ À générer
│   ├── field.md                       ⏳ À générer
│   ├── link.md                        ⏳ À générer
│   ├── checkbox.md                    ⏳ À générer
│   ├── radio.md                       ⏳ À générer
│   ├── toggle.md                      ⏳ À générer
│   ├── badge.md                       ⏳ À générer
│   ├── avatar.md                      ⏳ À générer
│   ├── heading.md                     ⏳ À générer
│   ├── text.md                        ⏳ À générer
│   ├── eyebrow.md                     ⏳ À générer
│   ├── label.md                       ⏳ À générer
│   ├── image.md                       ⏳ À générer
│   ├── flag.md                        ⏳ À générer
│   ├── skip-link.md                   ⏳ À générer
│   ├── spinner.md                     ⏳ À générer
│   ├── divider.md                     ⏳ À générer
│   └── progress-bar.md                ⏳ À générer
│
├── molecules/                          🔴 0/20 générés
│   ├── card.md                        ⏳ À générer (PRIORITAIRE)
│   ├── dropdown.md                    ⏳ À générer (PRIORITAIRE)
│   ├── search-bar.md                  ⏳ À générer (PRIORITAIRE)
│   ├── form-field.md                  ⏳ À générer (PRIORITAIRE)
│   ├── breadcrumb.md                  ⏳ À générer (PRIORITAIRE)
│   ├── pagination.md                  ⏳ À générer (PRIORITAIRE)
│   ├── alert.md                       ⏳ À générer (PRIORITAIRE)
│   ├── tooltip.md                     ⏳ À générer
│   ├── tabs.md                        ⏳ À générer
│   ├── accordion.md                   ⏳ À générer
│   ├── menu-item.md                   ⏳ À générer (PRIORITAIRE)
│   ├── language-selector.md           ⏳ À générer
│   ├── modal.md                       ⏳ À générer (PRIORITAIRE)
│   ├── toast.md                       ⏳ À générer
│   ├── stepper.md                     ⏳ À générer
│   ├── table.md                       ⏳ À générer
│   ├── video.md                       ⏳ À générer
│   ├── carousel.md                    ⏳ À générer
│   ├── skeleton.md                    ⏳ À générer
│   └── tag-list.md                    ⏳ À générer
│
├── organisms/                          🔴 0/12 générés
│   ├── header.md                      ⏳ À générer (CRITIQUE)
│   ├── footer.md                      ⏳ À générer (CRITIQUE)
│   ├── hero.md                        ⏳ À générer (PRIORITAIRE)
│   ├── search-form.md                 ⏳ À générer (PRIORITAIRE)
│   ├── card-grid.md                   ⏳ À générer (PRIORITAIRE)
│   ├── feature-section.md             ⏳ À générer
│   ├── filter-panel.md                ⏳ À générer (PRIORITAIRE)
│   ├── map-view.md                    ⏳ À générer (PRIORITAIRE)
│   ├── calculator.md                  ⏳ À générer
│   ├── main-menu.md                   ⏳ À générer (PRIORITAIRE)
│   ├── article-list.md                ⏳ À générer
│   └── pre-footer.md                  ⏳ À générer
│
├── templates/                          🔴 0/8 générés
│   ├── page-container.md              ⏳ À générer (CRITIQUE)
│   ├── two-column.md                  ⏳ À générer (PRIORITAIRE)
│   ├── content-sidebar.md             ⏳ À générer (PRIORITAIRE)
│   ├── block.md                       ⏳ À générer (PRIORITAIRE)
│   ├── grid-layout.md                 ⏳ À générer (PRIORITAIRE)
│   ├── full-width.md                  ⏳ À générer
│   ├── hero-layout.md                 ⏳ À générer
│   └── article-layout.md              ⏳ À générer
│
└── pages/                              🔴 0/8 générés
    ├── home-page.md                   ⏳ À générer (CRITIQUE)
    ├── property-search.md             ⏳ À générer (CRITIQUE)
    ├── property-detail.md             ⏳ À générer (CRITIQUE)
    ├── user-account.md                ⏳ À générer (PRIORITAIRE)
    ├── contact.md                     ⏳ À générer
    ├── about.md                       ⏳ À générer
    ├── blog-listing.md                ⏳ À générer
    └── blog-article.md                ⏳ À générer
```

---

## 📈 Progression

| Catégorie | Complétés | Total | Pourcentage |
|-----------|-----------|-------|-------------|
| **Design Tokens** | 7 | 7 | ✅ 100% |
| **Documentation** | 3 | 3 | ✅ 100% |
| **Atoms** | 1 | 19 | 🟡 5% |
| **Molecules** | 0 | 20 | 🔴 0% |
| **Organisms** | 0 | 12 | 🔴 0% |
| **Templates** | 0 | 8 | 🔴 0% |
| **Pages** | 0 | 8 | 🔴 0% |
| **TOTAL** | 11 | 77 | 🔴 14% |

---

## 🎯 Priorisation de génération

### Phase 1 : CRITIQUE (À faire immédiatement)
Composants essentiels pour l'architecture de base

- ✅ `design/README.md` - Vue d'ensemble
- ✅ `design/tokens/*` - Tous les tokens (7 fichiers)
- ✅ `atoms/button.md` - Bouton
- ⏳ `templates/page-container.md` - Container principal
- ⏳ `organisms/header.md` - Header site
- ⏳ `organisms/footer.md` - Footer site
- ⏳ `pages/home-page.md` - Page d'accueil
- ⏳ `pages/property-search.md` - Recherche propriétés
- ⏳ `pages/property-detail.md` - Détail propriété

**Estimation** : 8 fichiers restants × 30min = **4h de travail**

### Phase 2 : HAUTE PRIORITÉ
Composants utilisés sur la majorité des pages

**Atoms** :
- `atoms/icon.md` (2000+ occurrences)
- `atoms/field.md` (147 occurrences)
- `atoms/link.md` (262 occurrences)
- `atoms/checkbox.md`
- `atoms/heading.md`
- `atoms/text.md`
- `atoms/label.md`
- `atoms/image.md`

**Molecules** :
- `molecules/card.md` (47 occurrences)
- `molecules/dropdown.md` (262 occurrences)
- `molecules/search-bar.md` (60 occurrences)
- `molecules/form-field.md` (147 occurrences)
- `molecules/breadcrumb.md` (SEO critical)
- `molecules/pagination.md`
- `molecules/alert.md` (13 occurrences)
- `molecules/menu-item.md` (139 occurrences)
- `molecules/modal.md`

**Organisms** :
- `organisms/hero.md`
- `organisms/search-form.md`
- `organisms/card-grid.md`
- `organisms/filter-panel.md` (6 occurrences)
- `organisms/map-view.md` (198 occurrences)
- `organisms/main-menu.md`

**Templates** :
- `templates/two-column.md`
- `templates/content-sidebar.md`
- `templates/block.md`
- `templates/grid-layout.md`

**Pages** :
- `pages/user-account.md`

**Estimation** : 35 fichiers × 25min = **14h de travail**

### Phase 3 : MOYENNE PRIORITÉ
Composants d'enrichissement UX

**Atoms** :
- `atoms/radio.md`
- `atoms/toggle.md`
- `atoms/badge.md` (27 occurrences)
- `atoms/avatar.md`
- `atoms/eyebrow.md`
- `atoms/flag.md`
- `atoms/spinner.md`

**Molecules** :
- `molecules/tooltip.md` (2 occurrences)
- `molecules/tabs.md`
- `molecules/accordion.md`
- `molecules/language-selector.md`
- `molecules/toast.md`
- `molecules/table.md`
- `molecules/tag-list.md` (27 occurrences)

**Organisms** :
- `organisms/feature-section.md`
- `organisms/calculator.md` (2 occurrences)
- `organisms/article-list.md`
- `organisms/pre-footer.md` (23 occurrences)

**Templates** :
- `templates/full-width.md`
- `templates/hero-layout.md`
- `templates/article-layout.md`

**Pages** :
- `pages/contact.md`
- `pages/blog-listing.md`
- `pages/blog-article.md`

**Estimation** : 24 fichiers × 20min = **8h de travail**

### Phase 4 : BASSE PRIORITÉ
Composants optionnels ou rares

**Atoms** :
- `atoms/skip-link.md` (a11y)
- `atoms/divider.md`
- `atoms/progress-bar.md`

**Molecules** :
- `molecules/stepper.md`
- `molecules/video.md`
- `molecules/carousel.md`
- `molecules/skeleton.md`

**Pages** :
- `pages/about.md`

**Estimation** : 8 fichiers × 15min = **2h de travail**

---

## ⏱️ Estimation totale

| Phase | Fichiers | Temps estimé | Priorité |
|-------|----------|--------------|----------|
| Phase 1 (Critique) | 8 | 4h | 🔴 |
| Phase 2 (Haute) | 35 | 14h | 🟠 |
| Phase 3 (Moyenne) | 24 | 8h | 🟡 |
| Phase 4 (Basse) | 8 | 2h | 🟢 |
| **TOTAL** | **75** | **28h** | |

---

## 🛠️ Guide de génération

### Template de base (copier depuis `atoms/button.md`)

Chaque composant doit contenir :

1. **Header** : Nom, niveau, statut, version
2. **Description** : Rôle et occurrences Figma
3. **Aperçu visuel** : ASCII art ou schéma
4. **Structure BEM** : Classes complètes
5. **Props** : Component YAML complet
6. **Variants** : Toutes les déclinaisons
7. **Design Tokens** : Valeurs spécifiques
8. **Template Twig** : Code complet
9. **Styles SCSS** : Code complet
10. **Accessibilité** : Conformité WCAG 2.2 AA
11. **Responsive** : Comportements mobile
12. **Exemples** : Usage Drupal/Twig

### Commande de génération (pour IA ou script)

```bash
# Pour générer un composant :
# 1. Copier la structure de atoms/button.md
# 2. Remplacer les sections selon COMPONENT_MANIFEST.yml
# 3. Adapter les props, variants, et tokens
# 4. Ajouter les dépendances (libraryOverrides)
```

### Checklist par composant

- [ ] Nom et métadonnées corrects
- [ ] Occurrences Figma documentées
- [ ] Structure BEM complète
- [ ] Props YAML typés et validés
- [ ] Tous les variants documentés
- [ ] Design tokens référencés
- [ ] Template Twig fonctionnel
- [ ] SCSS avec tous les états
- [ ] Accessibilité WCAG 2.2 AA
- [ ] Exemples d'usage Drupal

---

## 🔗 Références rapides

### Design Tokens

- **Couleurs** : `tokens/colors.yml` - 40+ couleurs + WCAG
- **Typographie** : `tokens/typography.yml` - BNPP Sans + 20 presets
- **Espacement** : `tokens/spacing.yml` - Système 4px (26 valeurs)
- **Bordures** : `tokens/borders.yml` - Width + radius + presets
- **Layout** : `tokens/layout.yml` - Grid 12 cols + containers
- **Ombres** : `tokens/shadows.yml` - 7 niveaux d'élévation
- **Transitions** : `tokens/transitions.yml` - Durées + easing

### Composants clés

- **Button** : `atoms/button.md` ✅ - 298 instances, 4 variants
- **Icon** : `atoms/icon.md` ⏳ - 2000+ instances, 50+ icônes
- **Card** : `molecules/card.md` ⏳ - 47 instances, 3 variants
- **Header** : `organisms/header.md` ⏳ - 43 instances
- **Footer** : `organisms/footer.md` ⏳ - 23 instances

### Drupal Integration

- **Single Directory Components** : Drupal 10+
- **Component YAML** : `*.component.yml`
- **Libraries** : `*.libraries.yml`
- **Templates** : `*.twig`
- **Attach library** : `{{ attach_library('ps_theme/ps-button') }}`

---

## 📝 Notes importantes

### Composants détectés vs recommandés

- **Détectés dans Figma** : 57 composants
- **Ajouts recommandés** : 30 composants
- **Total design system** : 87 composants

### Composants manquants critiques

Ces composants n'ont pas été détectés dans Figma mais sont **essentiels** :

1. **Skip Link** (a11y obligatoire)
2. **Breadcrumb** (SEO critical)
3. **Pagination** (UX listings)
4. **Modal** (interactions)
5. **Tabs** (organisation contenu)
6. **Accordion** (FAQ, mobile)
7. **Radio** (formulaires)
8. **Toggle** (paramètres)

### Design System PS

- **Préfixe BEM** : `ps-`
- **Font** : BNPP Sans (Regular 400, Bold 700)
- **Couleurs primaires** : Green #00915A, Purple #BA3075
- **Design** : Carré (border-radius: 0)
- **Bordures** : 2px par défaut
- **Accessibilité** : WCAG 2.2 AA obligatoire

---

## 🚀 Prochaines étapes

1. ✅ Compléter les design tokens (7/7 fait)
2. ✅ Créer la documentation principale (fait)
3. ✅ Générer le premier atom exemple (Button - fait)
4. ⏳ Générer Phase 1 : Composants critiques (8 restants)
5. ⏳ Générer Phase 2 : Composants haute priorité (35 fichiers)
6. ⏳ Générer Phase 3 : Composants moyenne priorité (24 fichiers)
7. ⏳ Générer Phase 4 : Composants basse priorité (8 fichiers)
8. ⏳ Créer les fichiers Drupal (.component.yml, .twig)
9. ⏳ Générer les libraries.yml
10. ⏳ Tests d'accessibilité automatisés

---

**Progression actuelle** : 14% (11/77 fichiers)  
**Temps estimé restant** : 26 heures de génération  
**Priorité immédiate** : Phase 1 (4h pour 8 composants critiques)

---

**Dernière mise à jour** : 28 novembre 2025  
**Version** : 1.0.0
