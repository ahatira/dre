# Audit `libraries-override` — ui_suite_bnppre

> Scope : `ui_suite_bnppre.info.yml` → clé `libraries-override`
> Date : mars 2026 — Drupal 11, Bootstrap 5
> Outil : analyse statique + diff contre core Drupal

---

## Synthèse

| Catégorie | Entrées | État global |
|---|---|---|
| Core Drupal (toujours présent) | 10 | ✅ Valides |
| Contrib installé (`paragraphs`) | 1 | ✅ Valide |
| Contrib **non installé** (défensif) | 9 | ⚠️ Préventifs — à documenter |
| JS custom en remplacement de core | 2 | 🔍 Déltas documentés ci-dessous |

---

## 1 — Overrides Core Drupal

### 1.1 `core/drupal.active-link` — remplacement JS
```yaml
core/drupal.active-link:
  js:
    misc/active-link.js: assets/js/misc/active-link.js
```
**Action** : remplace `misc/active-link.js` de core par une version custom.

**Déltas vs core (3 modifications intentionnelles) :**
1. Passage ES6 arrow-functions / `const` (modernisation syntaxique).
2. Classe active renommée `active` au lieu de `is-active` — alignement Bootstrap 5 (Bootstrap utilise `.active`, pas `.is-active`).
3. Sélecteurs CSS simplifiés : suppression du double ciblage `[data-drupal-language]` + `li[lang]` — seule la variante `[hreflang]` est conservée (simplification valable si l'on n'utilise pas le sélecteur `data-drupal-language` sur des `<li>`).

**Complément** : un `libraries-extend` ajoute `drupal.active-link` (→ `active-link-trail.js`) sur la même librairie core.

**Risque** : ⚠️ Si Drupal core change sa logique d'active-link et pousse une mise à jour de sécurité, ce fichier custom masque le fix. À surveiller à chaque mise à jour de core.

---

### 1.2 `core/drupal.autocomplete` — neutralisation CSS
```yaml
core/drupal.autocomplete:
  css:
    component:
      misc/components/autocomplete-loading.module.css: false
```
**Action** : supprime le spinner CSS natif de core.

**Justification** : Bootstrap 5 fournit ses propres spinners via le composant `ui_suite_bnppre--spinner`. Un `libraries-extend` sur `core/drupal.autocomplete` ajoute `drupal.autocomplete` (→ `autocomplete.js` custom).

**État** : ✅ Correct — pas d'impact fonctionnel, spinner remplacé.

---

### 1.3 `core/drupal.dialog.off_canvas` — neutralisation CSS complète
```yaml
core/drupal.dialog.off_canvas:
  css:
    base:
      misc/dialog/off-canvas/css/reset.css: false
      misc/dialog/off-canvas/css/base.css: false
      misc/dialog/off-canvas/css/utility.css: false
    component:
      misc/dialog/off-canvas/css/button.css: false
      misc/dialog/off-canvas/css/drupal.css: false
      misc/dialog/off-canvas/css/form.css: false
      misc/dialog/off-canvas/css/table.css: false
      misc/dialog/off-canvas/css/details.css: false
      misc/dialog/off-canvas/css/messages.css: false
      misc/dialog/off-canvas/css/tabledrag.css: false
      misc/dialog/off-canvas/css/throbber.css: false
      misc/dialog/off-canvas/css/dropbutton.css: false
      misc/dialog/off-canvas/css/titlebar.css: false
      misc/dialog/off-canvas/css/wrapper.css: false
```
**Action** : supprime les 13 fichiers CSS de l'off-canvas de core.

**Justification** : le composant Bootstrap `Offcanvas` (via `components.ui_suite_bnppre--offcanvas`) remplace l'implémentation CSS de core. Un `libraries-extend` injecte `drupal.dialog.off_canvas` custom (→ `dialog.off-canvas.js` + `off-canvas.css`).

**État** : ✅ Correct — couverture complète par Bootstrap.

---

### 1.4 `core/drupal.dropbutton: false` — désactivation totale
```yaml
core/drupal.dropbutton: false
```
**Action** : désactive complètement la librairie dropbutton core (CSS + JS).

**Justification** : aucun style custom `dropbutton` n'existe dans `assets/scss/` ni `assets/css/`. Les dropbuttons admin sont soit inexistants dans ce contexte front, soit restyled via Bootstrap utilities directement dans les templates Twig.

**Risque** : 🔴 Si ce thème est activé comme thème admin (cas margin), les dropbuttons du toolbar Gin/Drupal admin seront sans style. À vérifier si le thème est utilisé en contexte admin.

---

### 1.5 `core/drupal.tableheader: false` — désactivation totale
```yaml
core/drupal.tableheader: false
```
**Action** : désactive le JS de sticky header sur les tableaux admin.

**Justification** : thème front-end — les en-têtes de tableau sticky sont une fonctionnalité admin non requise.

**État** : ✅ Acceptable — à documenter dans le README si ce thème n'est pas utilisé en context admin.

---

### 1.6 `core/drupal.tablesort: false` — désactivation totale
```yaml
core/drupal.tablesort: false
```
**Action** : désactive le JS de tri de colonnes des tableaux admin.

**Justification** : identique à 1.5 (tableheader).

**État** : ✅ Acceptable — même remarque.

---

### 1.7 `content_moderation/content_moderation: false`
```yaml
content_moderation/content_moderation: false
```
**Action** : désactive la librairie du module `content_moderation` (module core optionnel).

**Justification** : la librairie `content_moderation` injecte `content-moderation.css` pour styliser les transitions de workflow dans la barre d'administration — non pertinent pour un thème front.

**État** : ✅ Correct.

---

### 1.8 `layout_builder/drupal.layout_builder` — neutralisation CSS
```yaml
layout_builder/drupal.layout_builder:
  css:
    theme:
      css/layout-builder.css: false
      css/off-canvas.css: false
```
**Action** : supprime les deux CSS du Layout Builder core.

**Justification** : remplacés par `assets/css/layout-builder/layout-builder.css` custom, injecté via `libraries-extend`. Un second extend ajoute `drupal.layout_builder_block_filter` (JS de filtrage des blocs).

**État** : ✅ Correct.

---

### 1.9 `node/drupal.node.preview` — neutralisation CSS
```yaml
node/drupal.node.preview:
  css:
    theme:
      css/node.preview.css: false
```
**Action** : supprime le bandeau de prévisualisation de nœud de core.

**Justification** : le bandeau natif est opinionné (couleur, typo). À restyler dans Bootstrap si nécessaire, sinon sa suppression est acceptable.

**État** : ✅ Acceptable — pas de remplacement custom détecté. À surveiller si Preview est utilisé.

---

### 1.10 `system/base` — neutralisation CSS partielle
```yaml
system/base:
  css:
    component:
      css/components/clearfix.module.css: false
      css/components/container-inline.module.css: false
```
**Action** : supprime clearfix et container-inline de `system/base`.

**Justification** : Bootstrap 5 fournit `clearfix` via sa propre utility class. `container-inline` est un style Drupal admin sans équivalent nécessaire en front Bootstrap.

**État** : ✅ Correct.

---

### 1.11 `text/drupal.text` — remplacement de librairie complet
```yaml
text/drupal.text: ui_suite_bnppre/drupal.text
```
**Action** : remplace **l'intégralité** de la librairie `text/drupal.text` par `ui_suite_bnppre/drupal.text`.

**Déltas vs core (`text.js`) :**
1. Modernisation : passage `($, Drupal) =>` vers `($, Drupal, once)` + utilisation de `once()` pour l'attach (correctif Drupal 9+ requis).
2. **Bootstrap button** : le bouton "Edit summary" / "Hide summary" passe de `<button class="link link-edit-summary">` (vanilla) à `<button class="link link-edit-summary btn btn-outline-dark btn-sm float-end">` — restyling Bootstrap.
3. Retrait des parenthèses autour du bouton (` (<button>…</button>)` → `<button>…</button>`).
4. Logique de toggle réécriture légère (`.before()` → référence directe).

**État** : ✅ Justifié — override actif, asset présent, `once()` correctement ajouté aux dépendances.

**Risque** : identique à 1.1 — ce JS shadow une correction de sécurité éventuelle de core. À surveiller.

---

## 2 — Override Contrib Installé

### 2.1 `paragraphs/drupal.paragraphs.unpublished: false`
```yaml
paragraphs/drupal.paragraphs.unpublished: false
```
**Action** : désactive le CSS de mise en évidence des paragraphes non publiés.

**Module** : `paragraphs` ✅ installé dans `/web/modules/contrib/paragraphs/`.

**Justification** : le style core de `drupal.paragraphs.unpublished` (fond grisé/strié) entre en conflit visuel avec le design Bootstrap du thème.

**État** : ✅ Valide.

---

## 3 — Overrides Défensifs (modules non installés)

Ces overrides sont déclarés pour des modules **absents du codebase** à date. Leur présence est préventive : si ces modules sont installés ultérieurement, les overrides s'appliquent automatiquement.

| Entrée | Module | Modules manquants dans `/web/modules/contrib/` | Action déclarée |
|---|---|---|---|
| `clientside_validation_jquery/cv.jquery.ife: false` | `clientside_validation_jquery` | ❌ Non présent | Désactive la librairie IFE |
| `commerce_cart/cart_block: false` | `commerce` | ❌ Non présent | Désactive le CSS du cart block |
| `commerce_checkout/form: false` | `commerce` | ❌ Non présent | Désactive le CSS checkout form |
| `commerce_checkout/login_pane: false` | `commerce` | ❌ Non présent | Désactive le CSS login pane |
| `layout_builder_browser/browser: false` | `layout_builder_browser` | ❌ Non présent | Désactive la librairie browser |
| `layout_builder_browser/modal` | `layout_builder_browser` | ❌ Non présent (asset JS ✅ présent) | Remplace le JS modal |
| `media_library_edit/admin` | `media_library_edit` | ❌ Non présent | Supprime le CSS admin |
| `section_library/section_library` | `section_library` | ❌ Non présent | Supprime le CSS |

### Observations

**`layout_builder_browser/modal`** : cas particulier — le module n'est pas installé mais l'asset custom `assets/js/layout-builder-browser/layout-builder-browser.modal.js` existe. C'est une implémentation anticipée. L'override est fonctionnel dès que le module sera installé.

**Commerce** : trois overrides pour deux sous-modules. À conserver si Commerce est un objectif du projet.

**État global** : ⚠️ Ces entrées sont **silencieuses si le module est absent** (Drupal les ignore). Il n'y a pas de risque technique, mais elles augmentent la surface de maintenance. À reconsidérer si ces modules ne feront jamais partie du projet.

---

## 4 — Matrice de risque

| Override | Type | Asset présent | Risque mise à jour core | Priorité suivi |
|---|---|---|---|---|
| `core/drupal.active-link` (JS replace) | JS remplacement | ✅ | 🔴 Haut | **Réviser à chaque `composer update drupal/core`** |
| `text/drupal.text` (lib swap) | Lib remplacement | ✅ | 🔴 Haut | **Réviser à chaque `composer update drupal/core`** |
| `core/drupal.dropbutton: false` | Full disable | — | 🟡 Moyen | Vérifier si contexte admin actif |
| `core/drupal.dialog.off_canvas` (CSS) | CSS neutralisation | — | 🟢 Faible | OK |
| `core/drupal.autocomplete` (CSS) | CSS neutralisation | — | 🟢 Faible | OK |
| `layout_builder/drupal.layout_builder` (CSS) | CSS neutralisation | — | 🟢 Faible | OK |
| `layout_builder_browser/modal` (JS replace) | JS remplacement | ✅ | 🟡 Moyen | Tester à l'installation de `layout_builder_browser` |
| Défensifs Commerce/CSV/section_library | Full disable | — | 🟢 Nul | OK si modules prévus |
| Autres core CSS/full-disable | CSS/disable | — | 🟢 Faible | OK |

---

## 5 — Actions recommandées

1. **Déclencher une revue `active-link.js` + `text.js` à chaque mise à jour de core.**
   Ajouter une note dans `CHANGELOG.md` ou un ticket de suivi. Ces deux JS «shadow» le core.

2. **Documenter l'intention des trois `core/drupal.table*` disables.**
   Si ce thème n'est jamais utilisé comme thème admin → OK, ajouter un commentaire dans `info.yml`.

3. **Revalider les 8 overrides défensifs à chaque ajout de module.**
   Convention proposée : regrouper les overrides défensifs sous un commentaire `# [optional-modules]` dans `info.yml`.

4. **`core/drupal.dropbutton: false` — confirmer le scope.**
   Si Layout Builder admin est utilisé, les dropbuttons des sections/blocs seront cassés. Tester.

5. **`node/drupal.node.preview`** — si la prévisualisation nœud est utilisée, prévoir un remplacement CSS Bootstrap.

---

## 6 — Propositions de refactoring `info.yml`

Pour améliorer la lisibilité, regrouper les overrides par groupe logique avec des commentaires :

```yaml
libraries-override:

  # ── Core JS replacements (custom Bootstrap-aware versions) ─────────────────
  core/drupal.active-link:
    js:
      misc/active-link.js: assets/js/misc/active-link.js
  text/drupal.text: ui_suite_bnppre/drupal.text

  # ── Core CSS neutralization (replaced by Bootstrap components) ─────────────
  core/drupal.autocomplete:
    css:
      component:
        misc/components/autocomplete-loading.module.css: false
  core/drupal.dialog.off_canvas:
    css: { ... }
  layout_builder/drupal.layout_builder:
    css: { ... }
  node/drupal.node.preview:
    css: { ... }
  system/base:
    css: { ... }

  # ── Core libraries disabled (admin-only, not needed in front-end theme) ─────
  core/drupal.dropbutton: false
  core/drupal.tableheader: false
  core/drupal.tablesort: false

  # ── Core optional modules ───────────────────────────────────────────────────
  content_moderation/content_moderation: false

  # ── Contrib installed ───────────────────────────────────────────────────────
  paragraphs/drupal.paragraphs.unpublished: false

  # ── Contrib optional (apply automatically when module is installed) ─────────
  clientside_validation_jquery/cv.jquery.ife: false
  commerce_cart/cart_block: false
  commerce_checkout/form: false
  commerce_checkout/login_pane: false
  layout_builder_browser/browser: false
  layout_builder_browser/modal:
    js:
      js/layout_builder_browser.modal.js: assets/js/layout-builder-browser/layout-builder-browser.modal.js
  media_library_edit/admin:
    css:
      component:
        css/media_library_edit.admin.css: false
  section_library/section_library:
    css:
      theme:
        css/section-library.css: false
```
