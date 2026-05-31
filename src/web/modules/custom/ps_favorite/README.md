# Module `ps_favorite`

> Statut : Stable

Système de favoris Drupal générique pour entités configurées, compatible utilisateurs connectés et anonymes, avec page dédiée, bloc header, AJAX et architecture de stockage extensible.

## Responsabilité

`ps_favorite` fournit le parcours complet favoris côté frontend : ajout/retrait depuis toute entité configurée, page `/favorites`, panneau latéral Offcanvas, compteur dynamique et fusion automatique des favoris anonymes après connexion.

## Fonctionnalités livrées

- bouton favoris sur les entités configurées avec toggle AJAX ;
- persistance en base pour les utilisateurs connectés ;
- persistance par cookie signé pour les utilisateurs anonymes ;
- fusion automatique cookie -> base au login ;
- page dédiée `/favorites` ;
- route compte `/user/favorites` + lien menu compte + onglet ;
- bloc `FavoritesHeaderBlock` prêt à placer dans le header ;
- liste de favoris en cartes Twig génériques ;
- suppression rapide depuis la page et l'Offcanvas ;
- endpoint JSON `/favorites/count` pour rafraîchissement périodique optionnel du badge ;
- configuration BO par cible d'entité via des config entities `ps_favorite_target` ;
- limite max et display mode stockés par cible, avec compatibilité legacy en fallback ;
- cache tags explicites + compatibilité Dynamic Page Cache / BigPipe.

## Architecture

### Stockage

- table SQL `ps_favorite_item` pour les utilisateurs authentifiés ;
- cookie signé `ps_favorites` pour les anonymes ;
- service métier `FavoriteManager` ;
- repository `FavoriteRepository` ;
- état de réponse `FavoriteCookieState` + subscriber `FavoriteResponseSubscriber` pour écrire/vider le cookie proprement.

### Rendu

- `FavoriteLazyBuilder` : construit les boutons favoris et le badge compteur ;
- `FavoritePageBuilder` : construit la page dédiée et le contenu Offcanvas ;
- rendu carte : display mode configuré, sinon `teaser`, sinon fallback titre + lien ;
- templates Twig : bouton, carte, liste, bloc header ;
- JS Vanilla Drupal behaviors : toggle AJAX, update DOM, announcement screen reader.

### Intégration Drupal

- block plugin : `ps_favorite_header_block` ;
- routes : `/favorites`, `/user/favorites`, `/favorites/offcanvas`, `/favorites/toggle/{entity_type_id}/{entity_id}` ;
- route JSON : `/favorites/count` ;
- route BO : `/admin/ps/config/favorites` ;
- menu compte et local task utilisateur ;
- hooks Drupal en OOP pour `theme`, `entity_predelete` et `user_login` ;
- fallback procédural ciblé sur `entity_view` dans `ps_favorite.module` pour garantir l'injection du bouton dans ce runtime Drupal.

## Services principaux

| Service | Rôle |
|---|---|
| `ps_favorite.manager` | API métier `add/remove/toggle/get/count/merge` |
| `ps_favorite.repository` | Persistance SQL authentifiée |
| `ps_favorite.cookie_storage` | Lecture/écriture du cookie signé |
| `ps_favorite.cookie_state` | État de cookie pending par requête |
| `ps_favorite.lazy_builder` | Construction des boutons et du badge |
| `ps_favorite.page_builder` | Construction page/Offcanvas |

## Intégration thème

- Placer le block `Favorites header block` dans le header.
- Le bouton favoris est injecté automatiquement sur les entités configurées rendues côté front.
- Les templates suivants sont surchargeables côté thème :
  - `ps-favorite-button.html.twig`
  - `ps-favorite-card.html.twig`
  - `ps-favorite-list.html.twig`
  - `ps-favorite-header-block.html.twig`

## Validation effectuée

- vérification statique sans erreur sur le module ;
- `drush cr` ;
- vérification HTTP authentifiée de `/favorites/count` ;
- vérification HTTP authentifiée de la présence du markup `ps-favorite-button` sur `/node/3` ;
- vérification HTTP authentifiée de `/favorites` avec rendu de `ps-favorite-card`.

## Tests automatisés

- Kernel : `tests/src/Kernel/FavoriteManagerKernelTest.php`
  - fusion anonyme -> connecté ;
  - limite max par type de contenu.
- Functional Javascript : `tests/src/FunctionalJavascript/FavoriteFlowTest.php`
  - toggle AJAX ;
  - endpoint compteur ;
  - persistance après login.

Notes:
- le test Functional Javascript nécessite un serveur Selenium/WebDriver disponible (ex: `http://localhost:4444`) ;
- le test Kernel doit être exécuté avec un `SIMPLETEST_DB` configuré dans l'environnement PHPUnit.

## Configuration

- Chaque cible favorite est stockée dans une config entity `ps_favorite_target`.
- Le nom de config suit le pattern `ps_favorite.target.{entity_type}.{bundle}`.
- Le module conserve une compatibilité de lecture avec `ps_favorite.settings`, mais ce n'est plus la source de vérité.

## Documentation technique

Voir `docs/FLAG_ANALYSIS.md` pour l'analyse du module contrib Flag et la recommandation d'architecture retenue.
