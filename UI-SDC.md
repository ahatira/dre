# UI SDC Ruleset (Draft v2 - Full Theme)

Source analyzed:
- `web/themes/contrib/ui_suite_bootstrap/components` (40 composants)
- `web/themes/contrib/ui_suite_bootstrap/src`
- `web/themes/contrib/ui_suite_bootstrap/templates`
- `web/themes/contrib/ui_suite_bootstrap/*.yml`
- `web/themes/contrib/ui_suite_bootstrap/starterkits`

Objectif: deduire les regles pour que `ui_suite_bnppre` reste un theme Drupal generique, compatible SDC / UI Patterns / Layout Builder, et sans references a des entites specifiques hors entites Drupal par defaut.

## 1) Doctrine de genericite Drupal

## 1.1 Entites autorisees au niveau theme

### MUST
- Le theme doit rester agnostique metier: pas de references a des entites ou bundles projet-specifiques.
- Les references admises sont uniquement celles du socle Drupal (ex: node, taxonomy term, user, media, comment, block, views) dans une logique de rendu generique.
- Le theme ne doit jamais coder en dur un bundle metier (`article`, `event`, `offer`, etc.) ni un champ metier (`field_offer_*`, `field_project_*`, etc.).

### SHOULD
- Utiliser des templates generiques (`node.html.twig`, `taxonomy-term.html.twig`, etc.) plutot que des suggestions bundle-specifiques.
- Utiliser Display Modes / View Modes pour la variation par contenu, pas des conditions metier dans Twig.

### MAY
- Ajouter une integration module-specifique en couche optionnelle si elle est clairement isolee et documentee (sans imposer de couplage metier).

## 1.2 Interdits stricts

### MUST NOT
- Pas de `if bundle == ...` en Twig/PHP preprocess.
- Pas de `content.field_*` cible metier dans les composants.
- Pas d'acces a des routes metier custom hardcodees dans le theme.
- Pas de logique metier d'entite dans `src/Hook/*` (le theme fait du rendu, pas de business logic).

## 2) Contrat SDC compatible UI Patterns

## 2.1 Structure et nommage

### MUST
- Un composant vit dans `components/<component_name>/`.
- Nom de composant en `snake_case`.
- Fichiers obligatoires:
  - `<component_name>.component.yml`
  - `<component_name>.twig`

### SHOULD
- Un dossier par composant, sans melange de responsabilites.
- Sous-dossiers optionnels limites a `js/`, `styles/`, `stories/`.

## 2.2 YAML (`*.component.yml`)

### MUST
- Declarer `name`, `description`, `group`, `links`, `props`.
- `props` doit etre un objet (`type: object`) avec `properties` explicites.
- Utiliser les refs UI Patterns quand applicable:
  - `ui-patterns://attributes`
  - `ui-patterns://identifier`
  - `ui-patterns://links`
  - `ui-patterns://url`
- Exposer les booleens en `type: boolean`.
- Exposer les enums via `enum` (+ `meta:enum` quand necessaire).
- Declarer `libraryOverrides` (au minimum `css: {}`) pour maintenir la capacite d'override sous-theme.

### SHOULD
- Garder un variant `default` quand le composant a des variantes.
- Exposer des props d'attributs granulaire (`*_attributes`) pour les sous-zones internes.
- Declarer des valeurs `default` utiles au rendu Twig.

### MUST NOT
- Pas de props metier (ex: `node_bundle`, `product_type`, `field_machine_name`).
- Pas de dependance au schema d'une entite particuliere.

## 2.3 Slots

### MUST
- Tous les slots utilises en Twig doivent etre declares en YAML.
- Les slots doivent rester semantiques et generiques (`content`, `items`, `header`, `footer`, `title`).

### SHOULD
- Pour les slots de collection, decrire clairement le type attendu (liste d'items/slides/rows).

## 3) Conventions Twig generiques

## 3.1 Attributs et variantes

### MUST
- Manipuler les attributs via API Drupal/Twig:
  - `attributes.addClass(...)`
  - `attributes.setAttribute(...)`
- Initialiser les attributs imbriques avec `create_attribute(...)` avant mutation.
- Appliquer les classes de variante de facon deterministe (mapping Bootstrap explicite).

### SHOULD
- Ignorer explicitement la variante neutre (`variant == 'default'`).
- Garder Twig centre sur la structure/rendu, pas sur la logique de donnees.

### MAY
- Utiliser du style inline seulement pour valeurs dynamiques strictement presentationnelles.

## 3.2 Includes et composition

### MUST
- Inclure les composants en contexte isole:
  - `include('ui_suite_bootstrap:<component>', {...}, with_context = false)`
- Passer explicitement les variables d'interface du composant inclus.

### SHOULD
- Normaliser les inputs de collection (single item vs list) avant boucle.
- Factoriser les fragments repetes en composants utilitaires (ex: bouton de fermeture).

## 4) Compatibilite UI Patterns

### MUST
- Les composants doivent pouvoir etre instancies via render arrays `#type: component`.
- Les props doivent rester editables par les outils de site building (UI Patterns/UI Styles).
- Les slots doivent accepter du render array et du markup sans hypothese d'entite.

### SHOULD
- Exposer des interfaces de composant stables (props/slots) pour eviter la casse dans Layout Builder et les templates parents.

### MUST NOT
- Ne jamais imposer un champ d'entite precis pour alimenter un slot.

## 5) Compatibilite Layout Builder

### MUST
- Les composants doivent etre neutres vis-a-vis du contexte Layout Builder (pas de detection d'entite dans les composants).
- Les adaptations Layout Builder doivent rester dans la couche hooks/element/prerender du theme.
- Les customisations LB doivent toucher l'UI editoriale (classes, wrappers, labels) et pas la logique metier.

### SHOULD
- Cibler les formulaires Layout Builder via IDs/form patterns techniques, pas via type de contenu.
- Preserver le comportement natif Drupal (actions, controles, nesting).

## 6) Compatibilite libraries et dependances

### MUST
- Declarer les dependances structurelles du theme en `*.info.yml` (ex: UI Patterns, UI Styles).
- Preferer `libraries-extend`/`libraries-override` pour integration plutot que hacks dans Twig.
- Pour chaque comportement JS, declarer les dependances explicites (core/drupal, core/once, etc.).

### SHOULD
- Garder les overrides documentes et minimaux pour reduire les regressions.

## 7) Accessibilite et i18n

### MUST
- Poser explicitement les roles/attributs ARIA pour composants interactifs.
- Garder les labels visibles/non visibles traduisibles (`|t`).
- Respecter les patterns Bootstrap a11y (`visually-hidden`, `aria-expanded`, `aria-controls`, etc.).

### SHOULD
- Exposer `heading_level` (2..6) quand un composant gere un titre.

## 8) Anti-patterns a bannir dans ui_suite_bnppre

### MUST NOT
- `node--article.html.twig`, `node--offer.html.twig`, `taxonomy-term--product_category.html.twig` comme base de theming global.
- Conditions Twig/PHP du type: `if node.bundle == 'x'`.
- Hardcode de champs metier (`content.field_offer_price`, `entity.field_project_status`, etc.).
- Coder des composants qui attendent une entite specifique au lieu de props/slots generiques.

### SHOULD NOT
- Mettre des comportements metier dans le theme quand ils relevent d'un module custom.

## 9) Checklist de revue avant merge (ui_suite_bnppre)

1. Chercher toute reference bundle/champ metier dans `src`, `templates`, `components`.
2. Verifier que les composants n'exigent que des props/slots generiques.
3. Verifier que les hooks alterent structure/styling et pas la logique metier.
4. Verifier compatibilite SDC/UI Patterns (`#type: component`, props stables, slots robustes).
5. Verifier compatibilite Layout Builder (pas de couplage entite, uniquement couche editoriale).
6. Verifier a11y (ARIA/roles) et traduction des labels UI.

## 10) Evidence (theme contrib)

- Dependances SDC/UI Patterns declarees:
  - `web/themes/contrib/ui_suite_bootstrap/ui_suite_bootstrap.info.yml`
- Hooks LB bases sur form IDs techniques, non metier:
  - `web/themes/contrib/ui_suite_bootstrap/src/Hook/FormAlter.php`
- Views exposees stylees sans bundle metier:
  - `web/themes/contrib/ui_suite_bootstrap/src/Hook/Views.php`
- Templates d'entites core en rendu generique:
  - `web/themes/contrib/ui_suite_bootstrap/templates/node/node.html.twig`
  - `web/themes/contrib/ui_suite_bootstrap/templates/taxonomy/taxonomy-term.html.twig`

## 11) Portee

Ce document est le baseline de reference pour ecrire les futures instructions de `ui_suite_bnppre` en mode "Drupal generic theme".

## 12) Regles de rendu des slots (basees sur le formatage des fields)

Cette section formalise les regles reelles observees dans `ui_suite_bootstrap` pour alimenter les slots depuis des render arrays Drupal (fields, tables, views, links, item lists).

## 12.1 Taxonomie des slots par type de payload

### MUST
- Slot texte simple: payload `string` (ou markup simple), rendu direct `{{ slot }}`.
- Slot render array: payload renderable Drupal (array), rendu direct `{{ slot }}` (Drupal execute le rendu).
- Slot sequence: payload array d'elements renderables (souvent array de composants), rendu via boucle ou rendu direct de la sequence.
- Slot structurel imbrique: payload compose de sous-composants `#type: component`.
- Structure de liens: utiliser le schema `ui-patterns://links` (objet liens normalises) quand le composant attend des liens structurels.

### MUST NOT
- Ne pas melanger, dans un meme slot, string brute + render arrays de facon non controlee.
- Ne pas passer un render array complexe dans un slot explicitement "plain text".

## 12.2 Regles par famille de composants

## 12.2.1 Famille Table (`table`, `table_row`, `table_cell`)

### MUST
- `table.header` doit contenir une sequence de composants `table_cell`.
- `table.rows` doit contenir une sequence de composants `table_row`.
- `table_row.cells` doit contenir une sequence de composants `table_cell`.
- `table_cell.content` porte le contenu final de cellule (markup/render array).
- Chaque ligne doit avoir une cardinalite de cellules coherente avec l'entete.

### SHOULD
- Renseigner `header_columns` quand un etat `empty` peut etre affiche.
- Garder les metadonnees de cellule dans `#props` (`attributes`, `tag`, `active`) et le contenu dans `#slots.content`.

Evidence implementation:
- `templates/system/table.html.twig`
- `templates/views/views-view-table.html.twig`
- `components/table/table.twig`

## 12.2.2 Famille List / List Group (`list`, `list_group`, `list_group_item`)

### MUST
- `list.items` attend une sequence d'elements renderables.
- `list_group.items` attend preferentiellement une sequence de composants `list_group_item`.
- `list_group_item.content` accepte texte, markup ou render array.

### SHOULD
- Transformer les structures Drupal (`item_list`, `links`) en `list_group_item` avant passage au `list_group` quand on veut un rendu uniforme Bootstrap.

Evidence implementation:
- `templates/system/item-list--layouts.html.twig`
- `templates/system/links--layout-builder-links.html.twig`
- `templates/views/views-view-summary.html.twig`

## 12.2.3 Famille Nav / Dropdown / Navbar (`nav`, `dropdown`, `navbar`, `navbar_nav`)

### MUST
- `nav.items` (prop) doit suivre le schema links normalise.
- `nav.tab_content` (slot) doit etre une sequence renderable alignee avec `nav.items`.
- Cardinalite stricte: nombre d'onglets == nombre de panes.
- `dropdown.title` est un slot texte; `dropdown.content` (prop) suit le schema links.

### SHOULD
- Normaliser les liens avant mapping vers composants (ex: preprocess de dropbutton).
- Conserver la logique de transformation de structure en preprocess PHP, pas dans Twig composant.

Evidence implementation:
- `src/Hook/Filter.php`
- `src/Hook/PreprocessLinksDropbutton.php`
- `components/nav/nav.twig`
- `components/dropdown/dropdown.twig`

## 12.2.4 Famille Dialog (`modal`, `offcanvas`, `toast`, `toast_container`)

### MUST
- `modal.title` / `offcanvas.title` doivent rester textuels pour un rendu heading/ARIA propre.
- `modal.body`, `modal.footer`, `offcanvas.body`, `toast.content` acceptent du renderable.
- `toast_container.items` attend une sequence de toasts renderables.

### SHOULD
- Garder les actions (boutons/links) dans `footer` ou `body`, pas dans les props textuelles.

Evidence implementation:
- `components/modal/modal.twig`
- `components/offcanvas/offcanvas.twig`
- `components/toast/toast.twig`

## 12.2.5 Famille Card / Grid

### MUST
- `card.content`, `card.header`, `card.footer`, `card.image` sont des slots renderables.
- `grid_row.content` est une sequence de contenus de colonnes.
- `grid_row_2/3/4` utilisent des slots fixes par colonne (`col_1_content`, etc.).

### SHOULD
- Preferer slots pour contenu de layout et props pour options de structure (classes, breakpoints, attributes).

Evidence implementation:
- `components/card/card.twig`
- `components/grid_row/grid_row.twig`
- `components/grid_row_2/grid_row_2.twig`

## 12.3 Pipeline "field formatting" -> slots

## 12.3.1 Champs Drupal generiques (`field.html.twig`)

### MUST
- Le formatter produit `item.content`; le theme ne doit pas supposer un type de champ metier.
- Les wrappers de champ (`field`, `field--items`, `field--item`) restent pilotables via variables preprocess.

### SHOULD
- Si conversion field -> composant est necessaire, faire la conversion en preprocess/hook dedie, puis passer un render array de composant.

Evidence implementation:
- `templates/system/field.html.twig`
- `src/Hook/Fences.php`

## 12.3.2 Tables Drupal (`table.html.twig`)

### MUST
- Transformer la structure Drupal (`header`, `rows`, `footer`) en composants `table_cell`/`table_row` avant rendu final `table`.
- Mettre le contenu reel de cellule dans `#slots.content`.

### SHOULD
- Conserver tri/etat actif en props (`active`, `attributes`) pour que le composant reste generique.

Evidence implementation:
- `templates/system/table.html.twig`

## 12.3.3 Tables Views (`views-view-table.html.twig` + preprocess)

### MUST
- Preparer `preparedContent` en preprocess puis l'injecter dans `#slots.content` de `table_cell`.
- Garder les classes Views (`views-field-*`) sur les attributs de cellule, pas dans le slot.

### SHOULD
- Maintenir separateurs/sort-indicators en preprocess pour ne pas complexifier Twig.

Evidence implementation:
- `src/Hook/PreprocessViewsViewTable.php`
- `templates/views/views-view-table.html.twig`

## 12.3.4 Liens Drupal (`links`, `dropbutton`, `filter tips`)

### MUST
- Normaliser les links avant injection dans props/slots des composants nav/dropdown.
- Pour tabs, construire `items` (prop) + `tab_content` (slot) ensemble dans le meme flux.

### SHOULD
- Garder la transformation structurelle en PHP (Hook), puis rendre Twig le plus declaratif possible.

Evidence implementation:
- `src/Hook/PreprocessLinksDropbutton.php`
- `src/Hook/Filter.php`

## 12.4 Contraintes strictes anti-bug

### MUST
- Cardinalite tabs: `count(items) == count(tab_content)`.
- Cardinalite table: pour chaque row, nombre de cells coherent avec l'entete.
- Contrat YAML/Twig stable: tout slot rendu dans Twig doit etre declare en `.component.yml`.
- Type discipline: slots textuels ne recoivent pas de payload structurel complexe.

### MUST NOT
- Renommer/supprimer un slot sans mise a jour simultanee YAML + Twig + points d'appel preprocess.
- Faire dependre le mapping slot d'un bundle ou d'un champ metier.

## 12.5 Guide de mapping pratique

1. Donnee Drupal brute (field/link/table/views row).
2. Normalisation preprocess (props structurelles + slots de contenu).
3. Construction render array `#type: component`.
4. Injection:
  - `#props` pour comportement/structure
  - `#slots` pour contenu renderable
5. Rendu Twig declaratif (impression/loop/include).

Cette separation est obligatoire pour garder les composants generiques, reutilisables et compatibles Layout Builder/UI Patterns.
