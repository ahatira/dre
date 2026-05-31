# Analyse du module Flag pour le besoin favoris

## Résumé exécutif

Le module contrib Flag est **compatible Drupal 10.3 / 11** et fournit un socle robuste pour des marqueurs génériques sur entités. Il couvre correctement le coeur métier `flag/unflag`, l'intégration Views, un modèle `flag` + `flagging`, un service central et des liens lazy-buildés.

En revanche, pour le besoin PS Project, il ne répond pas totalement au cadrage produit et UX demandé sans surcouche significative.

### Recommandation finale

- `1. Utilisation directe de Flag` : **non recommandée**
- `2. Flag comme base technique partielle` : **possible conceptuellement**, mais le coût d'adaptation reste élevé
- `3. Développement custom complet` : **recommandé**
- `4. Approche hybride` : **possible uniquement comme inspiration architecturale**, pas comme dépendance runtime nécessaire

La recommandation retenue est donc : **module custom `ps_favorite`, inspiré de certains patterns de Flag, sans dépendance fonctionnelle à Flag**.

## Sources techniques vérifiées

- `flag.info.yml` : `core_version_requirement: ^10.3 || ^11`
- `src/FlagService.php` : service central `flag`
- `flag.services.yml` : services, access checks, link builder, count manager
- `flag.module` : intégration entity view, lazy builders, contextual links, cleanup, actions

## Architecture du module Flag

Flag repose sur deux concepts principaux :

- une config entity `flag` qui décrit un marqueur (bookmark, follow, like, etc.) ;
- une content entity `flagging` qui représente l'action d'un utilisateur sur une entité.

Le service `Drupal\flag\FlagService` centralise :

- la récupération des flags applicables ;
- la lecture des flaggings existants ;
- les opérations `flag()` et `unflag()` ;
- la gestion du cas anonyme via `session_id`.

Le module ajoute ensuite plusieurs couches d'intégration :

- plugins de type de flag ;
- plugins de type de lien ;
- lazy builders pour les liens de flag ;
- hooks d'intégration sur `entity_view`, `node_links_alter`, contextual links ;
- compatibilité Views et actions Drupal.

## Compatibilité Drupal 10/11

Point positif majeur : la branche actuelle déclare explicitement `^10.3 || ^11`. Sur ce point, Flag est aligné avec l'exigence projet.

## Gestion utilisateurs connectés

Pour les utilisateurs connectés, Flag est solide :

- persistance native en base via `flagging` ;
- lecture et suppression efficaces ;
- compatibilité avec accès, cleanup et entités supprimées ;
- modèle générique réutilisable pour plusieurs cas d'usage.

## Gestion utilisateurs anonymes

Flag gère les anonymes via un `session_id` stocké en session Drupal.

Avantages :

- solution native côté serveur ;
- pas besoin de cookie métier custom ;
- déduplication simple via couple `uid/session_id + entity`.

Limites pour notre besoin :

- la persistance dépend de la session, pas d'un cookie métier durable ;
- pas de fusion produit out-of-the-box entre favoris anonymes et compte connecté après login ;
- le besoin métier demandait explicitement session **ou cookies sécurisés**, avec migration/unification automatique et sans doublons.

Sur ce point, Flag couvre **partiellement** le besoin, mais pas le workflow UX cible sans développement complémentaire.

## Support AJAX

Flag supporte bien l'AJAX via ses link builders et son intégration de rendu. Il sait afficher des liens dynamiques et lazy-buildés.

Limite : le module ne livre pas directement l'expérience produit demandée ici :

- badge compteur header temps réel ;
- Offcanvas métier latérale ;
- synchronisation instantanée bouton + compteur + listing ;
- cartes favoris sur page dédiée.

Il faut donc ajouter une couche frontend/UX spécifique même en partant de Flag.

## Cache Drupal et BigPipe

Flag a de bons signaux côté cache :

- usage de lazy builders ;
- placeholders ;
- merge explicite des cache tags / contexts ;
- compatibilité avec les affichages dynamiques dans le rendu entité.

Point important : ces patterns sont utiles et ont inspiré l'implémentation `ps_favorite`.

Cependant, pour les anonymes avec état par session, la personnalisation du rendu reste délicate dès qu'on veut un compteur et un listing réellement individualisés dans des pages fortement cachées.

## Système de favoris natif

Flag sait créer un flag de type bookmark/favorite au sens générique, mais il ne fournit pas le produit complet demandé :

- pas de page métier `/favorites` prête à l'emploi selon notre design ;
- pas d'Offcanvas dédiée ;
- pas de header badge métier ;
- pas de fusion anonyme -> connecté ;
- pas de stratégie cookie signée ;
- pas de composant Twig/cartes aligné PS Project.

## Extensibilité

Flag est extensible :

- plugins ;
- hooks ;
- Views ;
- Rules/actions.

Mais cette extensibilité est orientée framework générique de flags, pas produit favoris opinionated. Pour notre besoin, cette flexibilité devient aussi un coût de configuration et de surcharge.

## Performances

Points forts :

- modèle éprouvé ;
- requêtes ciblées sur l'entité `flagging` ;
- lazy builders pour limiter l'impact du rendu.

Points faibles pour notre cas :

- surcouche générique plus lourde que nécessaire pour un seul cas métier ;
- coût de config + entités + plugins alors que nous avons un besoin unique `offer favorites` ;
- complexité supplémentaire pour produire compteur, panel et page métier cohérente.

Pour un besoin mono-domaine, une table dédiée et un manager simple sont plus directs et plus prévisibles.

## Limitations fonctionnelles

- fusion automatique anonyme -> connecté non native ;
- expérience favoris métier non fournie ;
- pas de stratégie dédiée d'unification sans doublons après login ;
- pas de composant header + badge + Offcanvas packagé.

## Limitations UX

- modèle de lien générique, pas une UX header/panel/page prête à intégrer ;
- surcharge frontend nécessaire pour obtenir un parcours moderne et cohérent ;
- expérience anonyme plus proche d'un état de session que d'un vrai panier/favoris cross-navigation durable.

## Limitations techniques

- dépendance contrib supplémentaire à maintenir pour un seul besoin métier ;
- coupling à la config des flags/bookmarks ;
- adaptation importante pour faire correspondre stockage anonyme, merge login, badge et Offcanvas.

## Possibilités de surcharge / customisation

Oui, Flag est surchargeable :

- theming des liens ;
- plugins custom ;
- contrôleurs/routes complémentaires ;
- intégration Views.

Mais une fois toutes les surcharges nécessaires listées, on se rapproche d'un sous-produit complet au-dessus de Flag. À ce stade, la dépendance ne simplifie plus réellement le delivery.

## Avantages de Flag

- module mature et connu ;
- compatible Drupal 10/11 ;
- bon support du rendu dynamique ;
- bon modèle générique de flagging ;
- extensibilité importante.

## Inconvénients de Flag pour PS Project

- trop générique pour un besoin unique `favorites` ;
- gestion anonyme par session, pas par cookie signé durable ;
- pas de merge login natif ;
- besoin fort de surcharge UX/frontend ;
- coût de configuration/contrib non négligeable pour un bénéfice partiel.

## Risques projet si Flag était retenu directement

- sous-estimation du travail d'intégration UX ;
- dette de customisation autour d'un module générique ;
- comportement anonyme non aligné avec le besoin métier ;
- complexité cache/UI accrue sur le compteur et le panel.

## Limitation spécifique à notre besoin métier

Le besoin PS Project n'est pas seulement "pouvoir flagger une offre". Il exige un **produit favoris complet** :

- bouton sur cartes/pages ;
- header badge dynamique ;
- Offcanvas ;
- page dédiée ;
- persistance anonyme durable ;
- fusion post-login ;
- cohérence cache ;
- intégration thème simple.

Flag aide sur le premier point, mais pas suffisamment sur les suivants.

## Architecture retenue dans `ps_favorite`

Le module `ps_favorite` retient donc :

- table dédiée `ps_favorite_item` pour les utilisateurs connectés ;
- cookie signé `ps_favorites` pour les anonymes ;
- merge automatique au login ;
- manager + repository + cookie abstraction ;
- rendu Twig / block / JS métier ;
- routes dédiées pour page, Offcanvas et toggle AJAX.

## Conclusion

Flag est une bonne référence technique et un module contrib crédible. Pour PS Project, il est **pertinent comme source d'inspiration**, mais **pas comme solution directe**.

Le meilleur compromis coût / maîtrise / UX / cache est un module custom dédié `ps_favorite`, ce qui a été implémenté ici.
