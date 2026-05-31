# Analyse transverse du systeme Features et strategie de migration

> Module cible: `ps_migrate`
> Domaine cible: `ps_feature`
> Statut: document de reference / architecture cible
> Version de travail: 1.0

## Objectif

Ce document formalise l'analyse complete du sous-systeme `ps_feature` et la strategie enterprise-ready pour industrialiser sa migration depuis les XML CRM.

Le but n'est pas seulement de "copier" des donnees. L'objectif est de definir un pipeline robuste qui:

- garde `ps_feature` comme moteur runtime des features;
- utilise le XML CRM comme source d'entree operationnelle;
- preserve les traductions, les groupes, les contraintes par type d'actif et les defaults;
- reste idempotent, observable, rollback-friendly et non bloquant pour les offres.

## Resume executif

Le systeme actuel est deja structure:

- `ps_feature` porte le modele de donnees, les widgets BO, les renderers et les drivers de types;
- `ps_migrate` orchestre l'import XML des offres et des dictionnaires;
- le XML contient une structure technique exploitable pour les features, via `TECHNICAL_ELEMENTS_LIST/TECHNICAL_ELEMENT`;
- les dictionnaires sont deja geres de facon autonome, donc la migration des features ne doit pas reintroduire de couplage fort avec les anciens imports CSV.

La bonne architecture cible consiste a separer clairement:

1. la normalisation du XML;
2. la resolution du catalogue `ps_feature`;
3. la persistence des definitions;
4. la persistence des valeurs sur les offres;
5. le reporting et la qualite de donnees.

## Source of truth fonctionnelle

### Sources de verite

- XML CRM: source operationnelle des donnees features.
- `ps_dictionary`: source des entrées de dictionnaires.
- `ps_feature`: schema runtime des features, groupes, widgets, formatters et traductions.
- `ps_migrate`: orchestration, validation, idempotence, journaux et reprise.

### Principe cle

Le XML ne doit jamais etre interprete comme un simple dump brut. Il doit etre traite comme un contrat semi-structure qui alimente des objets metier stables.

## Analyse du module `ps_feature`

### Couche de donnees

Le module expose trois briques de modelisation:

- `FeatureGroup` (`fb_feature_group`): regroupement logique, avec poids, description et types d'actifs;
- `FeatureDefinition` (`fb_feature_definition`): definition canonique d'une feature, avec code, groupe, type driver, defaults et restrictions;
- `OfferFeature` (`entity_offer_feature`): entite de contenu revisionnable pour des workflows autonomes ou avances.

Le champ de runtime `feature` stocke quant a lui:

- `feature_definition_id`;
- `payload` JSON.

### Couche plugin

Le systeme de type drivers repose sur un plugin manager dedie:

- manager: `ps_feature.type_manager` / alias `plugin.manager.feature_type`;
- contrat: `FeatureTypeInterface`;
- base commune: `FeatureTypeBase`.

Les drivers actuellement exposes dans le code sont:

- `flag`
- `yes_no`
- `numeric`
- `range`
- `text`
- `dictionary`
- `list`
- `date`
- `taxonomy`

Point important: la documentation historique mentionne parfois 8 drivers. Le code doit etre considere comme source de verite, et il en expose actuellement 9 avec `taxonomy` inclus.

### Couche saisie et rendu

Deux widgets BO existent:

- `feature_default`: formulaire classique avec selecteur + champs dynamiques;
- `feature_builder`: widget JS catalogue + state JSON + navigation par catalogue.

Trois formatters existent:

- `feature_default`;
- `feature_label_only`;
- `feature_value_only`.

Le formatter principal sait gerer:

- `default`;
- `compact`;
- `detailed`;
- `grouped`.

### Services structurants

- `ps_feature.state_builder`: reconstruit l'etat initial du widget JS depuis les items du champ;
- `ps_feature.catalogue_builder`: reconstruit le catalogue groupes + definitions en tenant compte du type d'actif;
- `ps_feature.translation_route_subscriber`: remplace les routes de traduction quand il faut gerer des champs imbriques;
- `ps_feature.presave_subscriber`: garantit la normalisation et la validation en amont de la persistence.

## Analyse XML

### Structure cible

La structure d'interet pour les features est:

```xml
<TECHNICAL_ELEMENTS_LIST>
  <TECHNICAL_ELEMENT>
    <CODE_GROUP>...</CODE_GROUP>
    <CODE_ELEMENT>...</CODE_ELEMENT>
    <ML_LABEL>...</ML_LABEL>
    <VALUE>...</VALUE>
    <UNIT>...</UNIT>
    <ML_COMPLEMENT>...</ML_COMPLEMENT>
  </TECHNICAL_ELEMENT>
</TECHNICAL_ELEMENTS_LIST>
```

### Lecture semantique

- `CODE_GROUP`: identifiant logique du groupe metier.
- `CODE_ELEMENT`: code technique stable de la feature.
- `ML_LABEL`: libelle multilingue.
- `VALUE`: valeur principale.
- `UNIT`: unite ou contexte de mesure.
- `ML_COMPLEMENT`: information complementaire traduisible.

### Exigences de normalisation

Le XML doit etre normalise avant toute persistence:

- ignorer les elements incomplets plutot que casser l'import entier;
- separer le mapping business du texte d'affichage;
- inferer le type driver a partir de la forme du payload, pas a partir du libelle;
- conserver le code source comme cle primaire metier de la migration.

### Regle de conception

Le `CODE_ELEMENT` est la cle de correspondance. Le label est un attribut de presentation. Cette distinction doit rester intacte dans toute logique de migration.

## Analyse fonctionnelle des types

### Payloads typiques

| Type driver | Forme de payload attendue | Usage metier |
|---|---|---|
| `flag` | booleen ou presence/absence | caracteristique binaire |
| `yes_no` | booleen | reponse simple |
| `numeric` | valeur + unite | surface, montant, longueur |
| `range` | min + max + unite | intervalle |
| `text` | texte libre | commentaire ou precision |
| `dictionary` | code dictionnaire | valeur normalisee |
| `list` | liste de codes ou valeurs | selection multiple |
| `date` | date ISO | evenement ou reference temporelle |
| `taxonomy` | identifiant ou reference taxonomy | ancrage taxonomique |

### Point de vigilance

Les defaults traduisibles sont un vrai sujet d'architecture. `ps_feature` les gere deja dans `payload_defaults`, notamment pour les champs imbriques. La migration doit donc:

- respecter les traductions existantes;
- ne pas ecraser les overrides de langue;
- produire des valeurs coherentes meme quand un XML ne fournit pas tous les champs.

## Lecture du code existant

### Ce qui existe deja et doit etre preserve

- le champ `feature` est deja le contrat runtime pour les entites consommatrices;
- le widget JS `feature_builder` a deja un state management explicite et des garde-fous de taille / JSON / ID;
- le catalogue filtre deja par type d'actif;
- le formatter gere un ordre de groupe configurable;
- les tests kernel et e2e couvrent extraction, securite et round-trip.

### Ambiguite a lever

Deux representations coexistent:

- le champ `feature` sur une offre;
- l'entite autonome `entity_offer_feature`.

La strategie de migration doit clarifier le role de chacune:

- pour l'offre, le champ `feature` doit rester la representation canonique du runtime editorial;
- `entity_offer_feature` doit rester reservee aux workflows ou besoins autonomes qui justifient une entite revisionnable distincte.

## Architecture cible dans `ps_migrate`

### Pipeline propose

1. Extraction XML.
2. Normalisation en objet intermediaire.
3. Resolution du groupe et de la definition cible.
4. Validation du type driver et du payload.
5. Persistence ou mise a jour idempotente.
6. Journalisation et signalement des anomalies.
7. Publication vers les offres consommatrices.

### Couches logiques recommandees

- Source plugin XML specialise pour `TECHNICAL_ELEMENTS_LIST`;
- normalizer dedie qui produit un DTO ou tableau intermediaire;
- resolver qui mappe `CODE_GROUP` et `CODE_ELEMENT` vers `fb_feature_group` et `fb_feature_definition`;
- writer qui gere create/update et correspondance stable;
- validator qui remonte erreurs bloquantes et warnings non bloquants;
- reporter qui expose le taux de couverture et les ecarts.

### Idempotence

La meme entree XML doit produire le meme resultat fonctionnel sans duplication. Pour cela:

- le code source doit etre la cle fonctionnelle;
- les updates doivent etre bases sur des identifiants stables;
- les valeurs derivees doivent etre recalculables;
- les groupes et definitions supprimes du XML doivent etre traites explicitement, pas implicitement.

## Workflows metier

### 1. Migration initiale

- creation des groupes;
- creation des definitions;
- reconstruction des defaults traduisibles;
- validation des codes dictionnaire;
- journalisation de la couverture.

### 2. Reprise incrementale

- detecter les nouveaux codes;
- mettre a jour les valeurs changees;
- conserver les donnees editoriales non presentes dans le XML de reprise si le mode le permet;
- signaler les divergences sans stopper tout le lot.

### 3. Cas de definition manquante

- si le groupe existe mais la definition manque, generer une anomalie exploitable;
- si le code dictionnaire est absent, appliquer une strategie de fallback controlee;
- si le type driver est incompatible, bloquer uniquement l'enregistrement de l'objet fautif.

### 4. Cas de conflits

- doublon de `CODE_ELEMENT` dans un meme groupe: erreur bloquante;
- `CODE_ELEMENT` present avec changement de groupe: deplacement explicite ou alerte selon le mode;
- mismatch entre XML et config existante: rapporter le delta avant ecrasement.

### 5. Cas de suppression

Une suppression XML ne doit jamais devenir une suppression silencieuse en base sans regle de gouvernance. Il faut choisir entre:

- mode strict: desactivation de la definition;
- mode souple: conservation historique avec warning;
- mode de nettoyage: suppression admin controlee apres verification.

## BO, administration et gouvernance

### Administration attendue

Le BO doit permettre de:

- visualiser les groupes;
- visualiser les definitions;
- comprendre quels codes sont deja rattaches;
- detecter les definitions sans correspondance XML;
- filtrer par type d'actif;
- verifier les traductions;
- reordonner les groupes et les definitions.

### Context Matrix

La matrice de contexte doit exposer au minimum:

- groupe;
- code;
- type driver;
- type(s) d'actif;
- source XML;
- etat de traduction;
- etat d'indexation/reprise;
- status runtime.

Cette matrice est utile pour les operations et pour la QA fonctionnelle.

### Interne vs externe

Le document acte une separation nette:

- `ps_feature` = interne, runtime, schema metier;
- `ps_migrate` = ingestion, orchestration, controle qualite;
- XML = interface d'entree externe.

Cette separation est importante pour eviter le couplage direct entre les formats d'import et le modele de rendu.

## Traduction et i18n

### Principes

- les labels de groupes et de definitions sont traduisibles;
- les defaults imbriques doivent conserver leur logique de traduction;
- le pipeline de migration ne doit pas ecraser les versions traduites a chaque import de masse.

### Cas importants

- `numeric` / `range`: traduction de l'unite;
- `list`: traduction des options;
- `dictionary`: conserver le code, traduire le label d'affichage;
- `text`: traduire le texte seulement si le contenu est editorial et non technique.

## Search, export et consommation aval

Les features doivent pouvoir alimenter:

- la recherche interne;
- les facettes ou indexes de l'environnement search;
- les exports de controle;
- les comparateurs de catalogues.

La migration doit donc produire des structures propres et stables, avec des identifiants exploitables en aval.

## Performance et cache

### Principes d'optimisation

- eviter les chargements repetes de groupes et definitions en boucle;
- privilegier les batchs et les paquets coherents;
- conserver un cache applicatif sur les catalogues derivees;
- ne pas recalculer le meme mapping XML plusieurs fois dans la meme execution.

### Points de charge

- chargement des dictionaries pour les types `dictionary` et `list`;
- resolution des groupes par poids;
- compilation du catalogue JS du widget builder;
- rendu grouped du formatter.

## Frontend et UX editoriale

Le widget JS `feature_builder` est le chemin prefere pour la saisie BO quand le volume ou la complexite augmente.

Le widget classique `feature_default` reste utile pour:

- debug;
- fallback;
- cas simples;
- environnements ou le JS n'est pas souhaite.

Le rendu doit rester lisible et stable dans les vues offre, avec un comportement coherent entre les styles `default`, `compact`, `detailed` et `grouped`.

## QA, validation et observabilite

### Tests attendus

- tests unitaires des resolveurs;
- tests kernel sur le stockage des definitions et la persistence du payload;
- tests d'integration sur le pipeline XML;
- tests e2e sur le BO et la reprise.

### Indicateurs de qualite

- nombre de definitions importees;
- nombre de warnings;
- nombre d'erreurs bloquantes;
- taux de couverture XML -> definition;
- taux de couverture definitions -> offres.

### Journalisation

Chaque run doit produire un journal exploitable avec:

- source;
- nombre d'entrees traitees;
- nombre de definitions creees / mises a jour;
- nombre de payloads rejetes;
- erreurs detaillees;
- statut final.

## Risques et cas limites

- XML vide ou partiel;
- doublons de codes dans un groupe;
- codes commencant a changer sans migration de reprise;
- payloads trop volumineux;
- types drivers non supportes;
- dictionaries absents;
- incoherence entre groupe et type d'actif;
- traduction partielle;
- regressions de tri ou d'ordre;
- suppressions involontaires.

## Strategie de rollback

Le rollback doit etre pense au niveau des couches, pas seulement au niveau du lot:

- rollback des definitions creees par run;
- rollback des payloads importes sur les offres;
- conservation du log technique;
- possibilite de rejouer un lot sans effets de bord.

## Ordre d'implementation recommande

1. Formaliser le source plugin XML et le normalizer.
2. Definir le mapping groupe / definition / code.
3. Implementer la persistence idempotente des definitions.
4. Ajouter la persistence des payloads sur les offres.
5. Exposer les warnings et le reporting BO.
6. Ajouter la couverture de test.
7. Brancher le pipeline dans `ps_migrate` sans bloquer le reste de l'import.

## Definition du done

La migration Features est consideree comme stable quand:

- les definitions sont rejouables sans duplication;
- les traductions restent coherentes;
- les offres continuent de s'importer meme si certaines features sont invalides;
- les ecarts XML / BO sont visibles;
- les tests montrent un round-trip stable sur le payload et le catalogue.

## Documents de reference

- [README du module ps_feature](../ps_feature/README.md)
- [Guide Field Type](../ps_feature/docs/FIELD_TYPE_GUIDE.md)
- [Guide traduction](../ps_feature/docs/TRANSLATION_GUIDE.md)
- [Exemple XML CRM](../../../../../../docs/xml/exemple.xml)
- [Conception CRM globale](../../../../../../docs/MIGRATION_CONCEPTION_SIMPLE.md)
- [README ps_migrate](../README.md)

## Conclusion

Le systeme cible doit rester simple dans ses principes et strict dans ses contrats:

- XML en entree;
- catalogue `ps_feature` comme schema runtime;
- `ps_migrate` comme orchestrateur;
- pas de mapping hardcode;
- pas de dependence implicite a un ancien import CSV;
- pas de blocage de l'offre quand une feature est partiellement invalide.

Cette base permet de construire une migration durable, administrable et suffisamment robuste pour un contexte enterprise.