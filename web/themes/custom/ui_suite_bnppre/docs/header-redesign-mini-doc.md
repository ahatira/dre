# Mini Documentation - Refonte Header BNP PRE

## Contexte et objectif

Le header custom actuel est temporairement retire du rendu principal pour permettre une reconstruction propre.

Objectif de cette mini documentation: definir une base de conception claire avant implementation.

Maquettes analysees:

- Desktop etat normal: navigation visible, actions a droite.
- Tablet etat compact: burger, logo, switch langue.
- Tablet etat ouvert: navigation et actions sur la meme bande.
- Mobile etat ferme: burger et logo.
- Mobile etat ouvert: menu vertical en panneau.
- Mobile etat ouvert avec utilisateur connecte: prenom utilisateur a la place du CTA login.

## 1) Disposition et regions

Modele maitre Desktop (normal): 4 grandes regions en grille 2 lignes x 2 colonnes.

- Haut gauche (HG): branding uniquement.
- Haut droite (HD): switch langue.
- Bas gauche (BG): navigation principale uniquement.
- Bas droite (BD): recherche rapide, lien utilitaire, compte, contact, icones utilitaires.

Container configurable depuis le BO:

- wrapper principal du header base sur la variable theme container.
- variantes attendues: container, container-fluid, container-xxl.

Mapping Drupal propose (intelligent et compatible avec l existant):

- header_brand -> HG.
- language_switcher -> HD.
- primary_menu -> BG.
- search_block -> BD.
- secondary_menu -> BD.
- account_block -> BD.
- contact_cta -> BD.
- icon_links -> BD.

Note technique:

- Si la region `header_actions` est conservee, elle est utilisee comme conteneur d agregation pour `search_block`, `secondary_menu`, `account_block`, `contact_cta`, `icon_links`.

Note de deduction fonctionnelle:

- La maquette desktop montre une separation visuelle nette:
  - ligne haute reservee au contexte de marque et de langue (identite + localisation).
  - ligne basse reservee au parcours (navigation + actions business).
- Cette separation doit rester la reference pour les autres breakpoints.

Schema ASCII (structure cible, regions visibles):

```text
DESKTOP (>= xl)
+---------------------------------------------------------------------------------------------------------------+
| HG: [LOGO]                                                                  | HD: [EN v]                    |
|--------------------------------------------------------------------------------+------------------------------|
| BG: [primary_menu]                                                            | BD: [search_block]            |
|                                                                                |     [secondary_menu] [account_block] [contact_cta] [icon_links] |
+---------------------------------------------------------------------------------------------------------------+

TABLET (>= md et < xl) - ferme
+---------------------------------------------------------------------------------------------------------------+
| [Menu] [LOGO]                                                                    [EN v]          |
+---------------------------------------------------------------------------------------------------------------+

TABLET (>= md et < xl) - ouvert
+---------------------------------------------------------------------------------------------------------------+
| [Close] [LOGO]                                                                   [EN v]          |
| BG: [primary_menu]                                                                                  |
| BD: [search_block] [secondary_menu] [account_block] [contact_cta] [icon_links]                    |
+---------------------------------------------------------------------------------------------------------------+

MOBILE (< md) - ferme
+----------------------------------------------+
| [Menu]  [LOGO]                               |
+----------------------------------------------+

MOBILE (< md) - ouvert
+----------------------------------------------+
| [X]  [LOGO]                                  |
| BG   [primary_menu]                          |
| HD   [language_switcher]                     |
| BD.1 [search_block]                          |
| BD.2 [secondary_menu]                        |
| BD.3 [account_block]                         |
| BD.4 [contact_cta]                           |
| BD.5 [icon_links]                            |
+----------------------------------------------+
```

## 2) Regles fonctionnelles et animations

Regles fonctionnelles:

- desktop: menu toujours visible, pas d offcanvas.
- tablet/mobile: burger ouvre un panneau navigation/actions.
- fermeture par bouton close, touche Esc et clic hors panneau sur tablet.
- etat anonyme: afficher CTA login.
- etat connecte: afficher icone compte + prenom utilisateur.
- switch langue toujours accessible.
- sur mobile, switch langue place dans le panneau.
- accessibilite: aria-expanded, aria-controls, labels explicites, ordre de tabulation coherent, focus visible.

Animations recommandees:

- ouverture panneau tablet/mobile: translation + fondu, 180ms, ease-out.
- changement burger vers close: 120ms.
- rotation chevrons sous-menu: 0deg vers 180deg, 120ms.
- hover desktop sur liens/boutons: transition couleur/fond 120ms.

## 3) Differences Desktop / Tablet / Mobile

Desktop >= xl:

- rendu strict en grille 2x2 (HG, HD, BG, BD).
- HD reste compact et aligne a droite.
- HG et HD forment une ligne haute de contexte (marque + langue).
- BG et BD forment une ligne basse de parcours (navigation + conversion).

Tablet >= md et < xl:

- etat ferme: une ligne top compacte (Menu + Logo + Switch).
- etat ouvert: BG et BD redeployes dans un panneau.
- HD reste sur la barre top pour garder l acces langue immediat.

Mobile < md:

- etat ferme minimal: Menu + Logo.
- etat ouvert vertical: BG puis HD puis BD.
- priorite a la lisibilite et a la zone tactile des CTA.

## 4) Contenu de chaque region

- HG
  - header_brand: bloc branding Drupal.
- HD
  - language_switcher: selecteur de langue.
- BG
  - primary_menu: menu principal niveau 1, niveau 2 via chevrons.
- BD
  - search_block: bloc recherche rapide.
  - secondary_menu: menu utilitaire secondaire.
  - account_block: login/signup (anonyme) ou profil (connecte).
  - contact_cta: CTA contact.
  - icon_links: liens icones (favoris, recherche, autres).

## 5) Aspects administrables (BO)

Objectif: rendre le header pilotable sans casser la structure 2x2 de reference.

Parametres administrables recommandes:

- layout_container
  - valeurs: container, container-fluid, container-xxl.
  - effet: largeur max du header.
- animation_preset
  - valeurs: none, fade, slide-down, slide-right.
  - effet: animation d ouverture du panneau tablet/mobile.
- animation_duration_ms
  - valeurs conseillees: 120, 180, 240.
  - effet: vitesse de transition.
- sticky_header
  - valeurs: on/off.
  - effet: header fixe au scroll.
- compact_on_scroll
  - valeurs: on/off.
  - effet: reduction de hauteur du header apres seuil.
- mobile_panel_mode
  - valeurs: overlay, push.
  - effet: comportement du panneau ouvert sur mobile.
- show_search_block
  - valeurs: on/off.
  - effet: afficher/masquer search_block.
- show_secondary_menu
  - valeurs: on/off.
  - effet: afficher/masquer secondary_menu.
- show_account_block
  - valeurs: on/off.
  - effet: afficher/masquer account_block.
- show_contact_cta
  - valeurs: on/off.
  - effet: afficher/masquer contact_cta.
- show_icon_links
  - valeurs: on/off.
  - effet: afficher/masquer les icones utilitaires.
- mobile_order_bd
  - valeurs: liste ordonnee des sous-elements BD.
  - effet: ordre vertical en etat mobile ouvert.

Aspects administrables de contenu:

- labels et URLs des CTA via les blocs Drupal.
- niveau de menu et profondeur via config de menu Drupal.
- affichage anonyme/connecte du account_block via visibilite de bloc.

Aspects non administrables (garde-fous de design):

- le modele desktop reste 4 regions HG/HD/BG/BD.
- HD reste reserve a language_switcher sur desktop.
- accessibilite obligatoire: aria-expanded, aria-controls, gestion clavier, focus visible.
- respect de prefers-reduced-motion: reduire ou neutraliser les animations.

## 6) Elements, components, templates a reutiliser

Templates et regions:

- [templates/system/page.html.twig](../templates/system/page.html.twig)
- [ui_suite_bnppre.info.yml](../ui_suite_bnppre.info.yml)

Composants UI Suite:

- [components/button/button.twig](../components/button/button.twig)
- [components/dropdown/dropdown.twig](../components/dropdown/dropdown.twig)
- [components/navbar/navbar.twig](../components/navbar/navbar.twig)
- [components/nav/nav.twig](../components/nav/nav.twig)

Blocs et menus deja thematises:

- [templates/block/block--system-branding-block.html.twig](../templates/block/block--system-branding-block.html.twig)
- [templates/block/block--ui-suite-bnppre-account-menu.html.twig](../templates/block/block--ui-suite-bnppre-account-menu.html.twig)
- [templates/menu/menu--account.html.twig](../templates/menu/menu--account.html.twig)

Sources a reutiliser partiellement:

- [components/header/js/header.js](../components/header/js/header.js)
- [assets/scss/components/_header.scss](../assets/scss/components/_header.scss)

## Decision de transition appliquee

- Le composant header custom n est plus injecte globalement.
- Le rendu des regions header est maintenu via un fallback simple dans la page.

Cette approche permet de continuer les integrations de blocs pendant la refonte complete du header.
