# ANALYSE OFFER PRICE

## Objectif
Documenter le module ps_price, son fonctionnement reel, son impact sur la gestion d une offre Drupal via field_prices, la complexite actuelle, et le travail a faire pour un modele plus complet, plus logique et plus adapte.

## 1) Role de ps_price dans l architecture
ps_price est un module "domaine" generique qui fournit:
- Un type de champ compose: ps_price.
- Un widget de saisie: ps_price_default.
- Deux formatters: ps_price_full et ps_price_short.
- Un service de formatage: ps_price.formatter.
- Un service de normalisation: ps_price.normalizer.
- Une validation de base via dictionnaires: PriceDictionaryConstraint.
- Un ecran de config admin: /admin/ps/config/price.

Dependances principales:
- ps_dictionary (codes et labels)
- field/system (Drupal)

Conclusion:
ps_price gere la structure technique du prix.
La logique metier contextuelle (location, vente, regles HT/HC, etc.) doit etre geree dans ps_offer.

## 2) Donnees du champ ps_price (modele reel)
Le champ stocke actuellement:
- amount (numeric 15,2)
- currency_code (varchar 3)
- value_type_code (varchar 32)
- unit_code (varchar 64)
- period_code (varchar 32)
- is_on_request (bool)
- is_from (bool)
- is_vat_excluded (bool)
- is_charges_included (bool)

Indexes natifs:
- amount
- currency_code

Point important:
- is_on_request est maintenant supporte dans le schema, le widget, le formatter et le normalizer.

## 3) field_prices dans ps_offer (impact concret)
Configuration actuelle de field_prices:
- Type: ps_price
- Cardinalite: -1 (multi valeurs)
- Translatable: true
- Required: false
- Widget en edition: ps_price_default avec unit/period/value_type/flags affiches

Impact sur la gestion d offre:
- Une offre peut porter plusieurs prix (ex: prix principal + variantes).
- Le modele permet location et vente sans changer de schema.
- Le front et la recherche doivent choisir quel prix est "principal" quand il y a plusieurs valeurs.
- En display par defaut du node offer, field_prices est masque, donc l affichage passe probablement par un rendu custom (a confirmer selon templates/views).

## 4) Logique fonctionnelle actuelle
### 4.1 Saisie
Le widget propose:
- amount + currency
- unit (SUR/GLO/OTH)
- period (ANN/MEN/TRI/SEM)
- value_type (MIN/MAX)
- flags (from, HT, CC)

### 4.2 Validation
Validation technique existante:
- amount >= 0
- currency obligatoire
- unit/period/value_type valides dans les dictionnaires

Validation metier absente dans ps_price (normal):
- Pas de regle transaction -> unit/period
- Pas de regle "prix principal unique"
- Pas de regle "location = SUR+ANN" par defaut

### 4.3 Formatage
PriceFormatter:
- format complet (montant, devise, unite, periode, flags)
- format court (montant + devise)
- option d affichage code vs label (via config)

### 4.4 Normalisation
PriceNormalizer convertit vers une unite de reference de comparaison:
- Conversion periode via metadata dictionnaire (multiplier)
- Conversion unite:
  - SUR: conserve
  - GLO: divise par surface
  - OTH: conserve
- Si surface=0 sur GLO: comportement configurable (null/zero/original)

## 5) Complexite et points sensibles
Complexite structurelle: moyenne
- Champ compose multi valeur + dependance dictionnaires + rendu conditionnel.

Complexite metier: elevee
- Meme structure pour plusieurs cas metier (location, vente, "sur demande", fourchette, etc.).
- Besoin d arbitrer quel prix est "affichable", "filtrable", "comparable".

Complexite technique transverse: moyenne a elevee
- Couplage fort a ps_dictionary.
- Couplage indirect a la surface pour normalisation GLO.
- Risque d ecart entre config, code, front et import.

## 6) Ecarts identifies (important)
1. Ecart doc vs code sur on_request (corrige)
- is_on_request est desormais disponible dans le field type et le schema SQL.
- Le cas "sur demande" est traite nativement en affichage et en normalisation.

2. Incoherences dans le schema de config
- config/schema/ps_price.schema.yml contient encore des cles anciennes (ex: value, ht, cc) qui ne matchent pas le modele reel (amount, is_vat_excluded, is_charges_included).
- Certaines definitions formatter schema ne correspondent pas clairement aux plugins actifs.

3. Cas "loyer sur demande" (corrige)
- Le modele natif existe via is_on_request.
- Le travail restant est surtout de fiabiliser son usage cote Search API et front listing.

4. Strategie multi prix non explicitee
- Pas de convention technique claire pour marquer le prix principal.
- Impact direct sur listing, tri, SEO, facettes et import.

## 7) Travail a faire (priorise)
### Priorite 1 - Stabiliser le contrat technique
- Aligner documentation, schema config et code.
- Corriger config/schema/ps_price.schema.yml selon les vrais sous champs.
- Decider officiellement si is_on_request existe ou non, puis harmoniser partout.

### Priorite 2 - Completer le modele metier minimum
- Ajouter un vrai support "on request" (champ explicite recommande).
- Definir une regle "prix principal" (ex: premier item, ou flag is_primary).
- Definir une regle de coherence transaction -> unit -> period dans ps_offer.

### Priorite 3 - Fiabiliser normalisation et recherche
- Definir quelle valeur normalisee est indexee quand plusieurs prix existent.
- Rendre explicite la gestion surface manquante pour GLO (null prefere pour eviter faux comparatifs).
- Ajouter tests d integration ps_offer + ps_price sur cas listing/facettes.

### Priorite 4 - Ameliorer UX editeur
- Clarifier labels UI (HT, HC/CC, from, min/max).
- Ajouter aide contextuelle selon transaction (location/vente).
- Eviter les combinaisons incoherentes des la saisie.

## 8) Proposition cible (simple et adaptee)
Conserver ps_price generique, mais poser un contrat metier explicite dans ps_offer:
- Donnees techniques: ps_price continue a stocker les composantes.
- Donnees metier: ps_offer impose les regles de coherence et la selection du prix principal.
- Cas reel "sur demande": support natif explicite, sans contournement.
- Recherche: indexer un "price_normalized_main" derive, stable et documente.

Resultat attendu:
- Moins d ambiguite pour les equipes import, front et contenu.
- Plus de coherence entre fiche offre, listing, comparaison et facettes.
- Evolution plus simple vers de nouveaux cas (coworking, fourchettes, multi devises).

## 9) Search API - gestion des prix des maintenant
Objectif Search API:
- Avoir une valeur de prix unique, comparable et triable dans l index, meme si field_prices est multi valeurs.
- Eviter les faux resultats lorsque les donnees sont incompletes (surface absente, prix global, etc.).

Strategie d indexation recommandee (MVP robuste):
1. Introduire 3 champs indexes derives pour l offre:
- price_display_main: texte (affichage listing, ex: "A partir de 390 EUR HT/HC/m2/an" ou "Loyer sur demande").
- price_amount_main: decimal (montant brut principal, pour tri simple fallback).
- price_normalized_main: decimal (valeur normalisee comparable pour tri/facettes).

2. Definir une regle unique de selection du "main price":
- Regle transitoire immediate: premier item valide de field_prices.
- Regle cible: item marque is_primary (ou equivalent metier).

3. Normalisation pour price_normalized_main:
- Utiliser ps_price.normalizer avec reference_period de config.
- Si unit=GLO et surface absente/0: renvoyer NULL (pas 0) pour eviter classement trompeur.
- Si plusieurs prix: normaliser uniquement le main price pour coherence de tri.

4. Gestion du cas "sur demande":
- Le support natif est disponible dans le champ (is_on_request).
- Dans Search API:
  - price_display_main = "Loyer sur demande"
  - price_amount_main = NULL
  - price_normalized_main = NULL

Facettes et tri a mettre en place:
- Tri principal: price_normalized_main ASC puis DESC.
- Fallback tri: price_amount_main si price_normalized_main est NULL.
- Facettes range: uniquement sur price_normalized_main (pas sur le texte d affichage).
- Filtre "sur demande": derive d un bool de statut prix (a creer) ou d une convention de mapping stable.

Plan implementation immediate (sans refonte lourde):
- Etape 1: creer un Processor Search API custom dans ps_offer (ou module search dedie) pour alimenter les 3 champs derives.
- Etape 2: brancher la selection main price + normalizer ps_price.
- Etape 3: reindexer, verifier tri/facettes sur jeux de cas (SUR, GLO, multi prix, surface 0, sur demande).
- Etape 4: ajouter tests Kernel d indexation pour figer le contrat.

Risques a anticiper:
- Incoherence si front n utilise pas la meme regle "main price" que l index.
- Regressions de tri si NULL mal gere dans Solr/DB backend.
- Effets de bord si dictionnaires periode/unite changent sans reindex.

## 10) Plan d execution step by step (sans update hooks)
Etape 1 - Assainir le contrat technique ps_price
- Aligner code + schema config + validation autour du modele reel du champ.
- Stabiliser le cas sur demande au niveau field type, widget, formatter, normalizer et validator.

Etape 2 - Reinstaller proprement les modules
- Desinstaller d abord ps_offer.
- Liberer et supprimer field_prices si residuel.
- Desinstaller ps_price.
- Reinstaller ps_price puis ps_offer.
- Faire drush cr.

Etape 3 - Valider via drush eval (minimum viable de non-regression)
- Cas 1: on_request sans montant -> 0 violation sur field_prices.
- Cas 2: montant sans devise -> violation attendue.
- Cas 3: format full/short d un prix standard.
- Cas 4: format full/short d un prix on_request.
- Cas 5: normalisation GLO+MEN avec surface > 0 (conversion attendue).
- Cas 6: normalisation on_request -> NULL.
- Cas 7: verification SQL de la colonne field_prices_is_on_request.

Etape 4 - Search API des maintenant
- Ajouter 3 champs derives indexes: price_display_main, price_amount_main, price_normalized_main.
- Definir la regle main price (transitoire: premier prix valide; cible: is_primary).
- Utiliser ps_price.normalizer pour price_normalized_main.
- Definir le traitement des NULL pour tri/facettes.
- Reindexer et valider tri + facettes sur jeux de cas reels.

Etape 5 - Durcir la qualite
- Ajouter tests Kernel sur validation et indexation.
- Ajouter tests Unit sur formatter/normalizer incluant on_request.
- Figer la convention import -> field_prices (ordre, principal, sur demande).

## 11) Resume executif
Le socle ps_price est solide pour structurer les prix, mais il manque un contrat metier ferme avec ps_offer.
La priorite est d harmoniser le modele (schema/doc/code), puis de couvrir les vrais cas business critiques: sur demande, prix principal, et normalisation multi prix.

## 12) Etat actuel
Fait:
- is_on_request implemente dans ps_price (schema, widget, formatter, normalizer, validation).
- Resolver de valeurs Search API cree dans ps_offer.
- Processor Search API ps_offer_price_main cree avec 4 proprietes derivees:
- ps_offer_price_display_main
- ps_offer_price_amount_main
- ps_offer_price_normalized_main
- ps_offer_price_on_request

Reste a finaliser pour cloture complete:
- Ajouter ces proprietes dans l index Search API cible et reindexer en environnement reel.
- Verifier tri/facettes sur index reel (backend DB/Solr selon projet).
- Aligner le front listing sur la meme regle de "main price" que le processor.
