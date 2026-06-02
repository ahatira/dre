# Générateur d'offres BNPPRE

Script Python unifié pour scraper et générer des offres XML depuis le site bnppre.fr.

## Architecture simplifiée

```
src/scripts/generate/
├── generate.py          # Script principal (3 modes: scrape, sample, translate)
├── validate.py          # Validation des règles métier
├── bnppre.sh            # Wrapper bash (optionnel)
├── README.md            # Cette documentation
└── templates/
    └── bnppre_offer_template.xml
```

---

## Mode 1: Scraper le site

Scrape le site bnppre.fr et génère un fichier XML avec les données FR uniquement.

### Python

```bash
python3 generate.py scrape \
  --limit 100 \
  --output data/xml/bnppre_offers.xml
```

**Options:**
- `--limit N` : Nombre maximum d'offres à scraper
- `--output FILE` : Fichier XML de sortie
- `--mode {explicit|all}` : Mode de découverte (défaut: all)
- `--asset TYPE` : Filtrer par type d'actif (bureau, commerce, etc.)
- `--operation {LOC|VEN}` : Filtrer par opération (location/vente)

### Wrapper bash

```bash
./bnppre.sh scrape 100
```

---

## Mode 2: Créer un échantillon avec traductions

Prend un fichier XML FR existant, sélectionne un échantillon d'offres, et traduit FR→EN en local.

### Python

```bash
# Échantillon de 50 offres par combinaison TYPE×OPERATION
python3 generate.py sample \
  --source data/xml/bnppre_offers.xml \
  --per-type 50 \
  --translate \
  --output data/xml/bnppre_sample_50.xml
```

**Options:**
- `--source FILE` : Fichier XML source (FR uniquement)
- `--output FILE` : Fichier XML de sortie (FR+EN si --translate)
- `--per-type N` : N offres par combinaison asset×transaction
- `--num-offers N` : N offres au total (si --per-type non spécifié)
- `--translate` : Activer la traduction FR→EN locale

### Wrapper bash

```bash
./bnppre.sh sample data/xml/bnppre_offers.xml 50
```

---

## Mode 3: Traduire un fichier existant

Traduit tous les champs FR→EN d'un fichier XML existant.

### Python

```bash
python3 generate.py translate \
  --source data/xml/bnppre_offers.xml \
  --output data/xml/bnppre_offers_FR_EN.xml
```

**Options:**
- `--source FILE` : Fichier XML source (FR uniquement)
- `--output FILE` : Fichier XML de sortie (FR+EN)

---

## Validation

Valide les règles métier d'un fichier XML généré.

### Python

```bash
python3 validate.py data/xml/bnppre_offers.xml
```

### Wrapper bash

```bash
./bnppre.sh validate data/xml/bnppre_offers.xml
```

**Règles validées:**
- `BUSINESS_ID` : Doit être un entier positif
- `CIVILITY` : MR, MRS ou vide
- `AVATAR_URL` : Pas de fallback générique (broker-homme.png, etc.)
- Offres VEN : Doivent avoir `IS_DIVISIBLE="false"`

---

## Installation

### Prérequis

```bash
# Python 3.10+
python3 --version

# Installer argostranslate pour la traduction locale (optionnel)
pip install argostranslate
```

### Structure des dossiers

```bash
# Créer le dossier de sortie
mkdir -p data/xml
```

---

## Traduction locale

La traduction FR→EN est effectuée **en local** avec **Argos Translate**, sans appel API externe.

### Installation

```bash
pip install argostranslate
```

### Performances

- ~4 champs/seconde
- Pour 50 offres (10 combinaisons) : ~2 minutes
- Pour 500 offres : ~20 minutes

### Champs traduits

- `SUMMARY_DESCRIPTION` (FR → EN)
- `DESCRIPTION_2` (FR → EN)
- `FULL_DESCRIPTION` (FR → EN)
- `AVAILABILITY` (FR → EN)

---

## Workflow recommandé

```bash
# 1. Scraper 1000 offres du site
python3 generate.py scrape --limit 1000 --output data/xml/bnppre_all.xml

# 2. Créer un échantillon de 50 par type avec traductions
python3 generate.py sample \
  --source data/xml/bnppre_all.xml \
  --per-type 50 \
  --translate \
  --output data/xml/bnppre_sample_50.xml

# 3. Valider le résultat
python3 validate.py data/xml/bnppre_sample_50.xml
```

Ou en version simplifiée :

```bash
./bnppre.sh scrape 1000
./bnppre.sh sample data/xml/bnppre_offers.xml 50
./bnppre.sh validate data/xml/bnppre_sample_50_per_type.xml
```

---

## Combinaisons asset × transaction

Le mode `--per-type` sélectionne N offres de chaque combinaison :

| TYPE_CODE | OPERATION_CODE | Exemple |
|-----------|----------------|---------|
| BUR       | LOC            | Bureaux à louer |
| BUR       | VEN            | Bureaux à vendre |
| COM       | LOC            | Commerces à louer |
| COM       | VEN            | Commerces à vendre |
| ACT       | LOC            | Locaux d'activité à louer |
| ACT       | VEN            | Locaux d'activité à vendre |
| ENT       | LOC            | Entrepôts à louer |
| ENT       | VEN            | Entrepôts à vendre |
| TER       | LOC            | Terrains à louer |
| TER       | VEN            | Terrains à vendre |

---

## Dépannage

### Erreur: "argostranslate non installé"

```bash
pip install argostranslate
```

### Erreur: "Fichier source introuvable"

Vérifiez que le chemin est correct :

```bash
ls -lh data/xml/bnppre_offers.xml
```

### Traduction trop lente

La traduction locale est lente (~4 champs/s). Pour accélérer :
- Réduisez le nombre d'offres (`--per-type 10`)
- Utilisez `--num-offers 20` au lieu de `--per-type`

---

## Support

Pour toute question sur les scripts de génération, consultez :
- Ce README
- Le code source de generate.py
- Le validateur validate.py
