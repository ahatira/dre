# ANALYSE OFFER SURFACE DIVISION

## Objectif
Documenter clairement ps_surface et ps_division, leur fonctionnement reel, leur impact sur la gestion d une offre (field_surfaces, field_divisions), la complexite actuelle, et le plan pour les rendre plus coherents et plus robustes.

## 1) Positionnement dans le modele offre
Dans ps_offer:
- field_surfaces est un champ compose ps_surface (cardinalite illimitee).
- field_divisions est une reference vers des entites ps_division (cardinalite illimitee).

Lecture metier:
- ps_surface porte la mesure (surface, unite, type, nature, qualification).
- ps_division porte la structure de subdivision (lot/floor/type/nature/surfaces) et sert a representer les lots divisibles.

## 2) ps_surface - fonctionnement reel
### Modele technique
Le field type ps_surface stocke:
- value (numeric)
- unit (code dictionnaire surface_unit)
- type (code dictionnaire surface_type)
- nature (code dictionnaire surface_nature)
- qualification (code dictionnaire surface_qualification)

### Validation
- SurfaceDictionaryConstraintValidator valide les codes dictionnaires.
- SurfaceCompletenessConstraint enforce au minimum value + unit.
- Les regles require_type/require_nature/require_qualification sont pilotables par ps_surface.settings.

### Services
- SurfaceValidator: validation reutilisable.
- SurfaceAggregator: somme des valeurs valides.

### Point sensible
L aggregation actuelle additionne les valeurs sans conversion d unite.
Donc si des unites heterogenes coexistent (ex: M2 + FT2), le total est mathematiquement faux.

## 3) ps_division - fonctionnement reel
### Modele technique
L entite ps_division contient notamment:
- building_name (obligatoire)
- floor (ps_dictionary)
- division_type (ps_dictionary, type configurable)
- nature (ps_dictionary, type configurable)
- lot (string)
- availability (string)
- surfaces (champ ps_surface, multi)

### Services et hooks
- DivisionManager valide des surfaces et genere un resume simple.
- DivisionHooks invalide le tag cache ps_division_list sur insert/update/delete.

### Point sensible
La logique metier annoncee dans README (parent entity, getByParent, calculateTotalSurface, caches par parent) n est pas alignee avec le code actuel du service/interface.

## 4) Ecarts identifies (important)
1. Ecart documentation vs implementation (ps_division)
- README decrit des methodes et concepts non presents dans l interface/service actuel.
- Exemple: getByParent/calculateTotalSurface/entity_id/cache parent non exposes dans DivisionManagerInterface.

2. Couplage DI non optimal dans l entite Division
- Division::baseFieldDefinitions lit la config via appel statique \Drupal::config().
- C est fonctionnel mais moins testable et moins propre architecturellement.

3. Risque d incoherence translatability sur surfaces offre
- field.storage.node.field_surfaces est translatable=true.
- field.field.node.offer.field_surfaces est translatable=false.
- Ce n est pas forcement bloquant, mais doit etre clarifie explicitement comme choix fonctionnel.

4. Agregation sans normalisation d unite
- Sur ps_surface et ps_division, les totaux ne tiennent pas compte d une conversion d unite.
- Risque metier fort si import multi-unites.

5. Duplication partielle de validation
- ps_surface valide deja les codes.
- DivisionManager revalide manuellement ces memes points.
- Risque de divergence future des regles.

## 5) Complexite actuelle
Complexite structurelle: moyenne
- 2 briques (field type + content entity) avec dictionnaires et cardinalite multiple.

Complexite metier: elevee
- Surface globale vs surfaces par division vs divisibilite.
- Besoin d un contrat clair pour l affichage, le filtrage et la comparaison.

Complexite technique transverse: elevee
- Coherence de docs, imports, front, Search API et aggregation.

## 6) Impact direct sur l offre
Sur une offre, il faut figer 3 decisions:
1. Quelle surface est la surface principale affichee (main surface).
2. Quelle relation entre field_surfaces (global) et sommes de field_divisions[*].surfaces.
3. Quelle regle de priorite en cas d ecart (source CRM globale vs somme des lots).

Sans ces regles, risque de mismatch entre:
- fiche offre,
- listing,
- facettes Search API,
- exports/imports.

## 7) Recommandation de cible (simple et robuste)
Conserver la separation actuelle:
- ps_surface = brique de mesure generique.
- ps_division = brique de subdivision.

Mais ajouter un contrat metier explicite cote offre:
- main_surface_value
- main_surface_unit
- total_surface_divisions (derive)
- surface_consistency_status (ok/warning/mismatch)

## 8) Plan de correction priorise
Priorite 1 - Contrat de donnees
- Definir officiellement le role de field_surfaces vs field_divisions.
- Definir la regle de calcul de surface principale pour front + Search API.

Priorite 2 - Alignement code/doc
- Mettre a jour README ps_division pour refleter le code reel, ou implementer les methodes manquantes annoncees.
- Clarifier le choix translatable true/false sur field_surfaces dans ps_offer.

Priorite 3 - Qualite de calcul
- Ajouter une normalisation d unite pour toute aggregation de surfaces.
- Interdire ou signaler les aggregations sur unites heterogenes sans conversion.

Priorite 4 - Refactor validation
- Centraliser la validation de surface pour eviter la duplication ps_surface vs DivisionManager.

Priorite 5 - Search API
- Exposer des champs derives surface main/total divisions/coherence.
- Indexer ces valeurs de maniere stable pour tri et facettes.

## 9) Resume executif
Le socle technique est solide, mais il manque un contrat metier fort sur la relation surface globale / divisions.
La priorite n est pas d ajouter beaucoup de code: c est d aligner les regles, la doc et l indexation pour eviter des incoherences visibles dans les offres.

## 10) Etat d avancement (execution)
Fait:
- Contrat de donnees implemente cote ps_offer via service OfferSurfaceDivisionSearchValueResolver.
- Processor Search API ajoute: ps_offer_surface_division.
- Proprietes derivees exposees:
	- ps_offer_surface_main_value
	- ps_offer_surface_main_unit
	- ps_offer_surface_total_divisions
	- ps_offer_surface_consistency_status
- Refactor de validation dans ps_division.manager:
	- delegation de la validation de surfaces vers ps_surface.validator
	- maintien des checks dictionnaires de niveau division
	- suppression de duplication de logique surface

Valide via drush eval:
- processor Search API detecte
- cas coherent (300 vs 300): status=ok
- cas sans divisions: status=warning

Reste a faire pour cloture complete:
- Ajouter les 4 champs dans l index Search API reel du projet et reindexer.
- Valider tri/facettes selon status et valeurs derivees (backend index reel).
- Aligner README ps_division avec le code reel (retirer les methodes non implementees annoncees).
- Clarifier et figer la politique translatable de field_surfaces dans ps_offer.
