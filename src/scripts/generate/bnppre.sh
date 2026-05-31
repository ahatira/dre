#!/usr/bin/env bash
#
# Wrapper BNPPRE - Générateur simplifié d'offres
# Usage: ./bnppre.sh [scrape|sample|validate] [options]
#

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
GENERATOR="${SCRIPT_DIR}/generate.py"
VALIDATOR="${SCRIPT_DIR}/validate.py"
OUTPUT_DIR="data/xml"

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonctions d'affichage
info() { echo -e "${BLUE}ℹ${NC} $*"; }
success() { echo -e "${GREEN}✓${NC} $*"; }
error() { echo -e "${RED}✗${NC} $*"; exit 1; }
warning() { echo -e "${YELLOW}⚠${NC} $*"; }

# Afficher l'aide
show_help() {
    cat << EOF
Usage: ./bnppre.sh COMMANDE [OPTIONS]

COMMANDES:
  scrape [LIMIT]         Scraper N offres du site bnppre.fr
  sample FILE [N]        Créer un échantillon de N offres par type (avec traductions)
  validate FILE          Valider un fichier XML

EXEMPLES:
  # Scraper 100 offres du site
  ./bnppre.sh scrape 100

  # Créer un échantillon de 50 par type avec traductions
  ./bnppre.sh sample data/xml/offers.xml 50

  # Valider un fichier
  ./bnppre.sh validate data/xml/offers.xml

EOF
}

# Commande: scrape
cmd_scrape() {
    local limit="${1:-100}"
    local output="${OUTPUT_DIR}/bnppre_offers.xml"
    
    info "Scraping de ${limit} offres depuis bnppre.fr..."
    
    python3 "$GENERATOR" scrape \
        --limit "$limit" \
        --output "$output"
    
    success "Fichier généré: $output"
}

# Commande: sample
cmd_sample() {
    local source="${1:-}"
    local per_type="${2:-50}"
    
    if [[ -z "$source" ]]; then
        error "Fichier source requis. Usage: ./bnppre.sh sample FILE [N]"
    fi
    
    if [[ ! -f "$source" ]]; then
        error "Fichier introuvable: $source"
    fi
    
    local output="${OUTPUT_DIR}/bnppre_sample_${per_type}_per_type.xml"
    
    info "Création d'un échantillon de ${per_type} offres par type..."
    
    python3 "$GENERATOR" sample \
        --source "$source" \
        --per-type "$per_type" \
        --translate \
        --output "$output"
    
    success "Fichier généré: $output"
}

# Commande: validate
cmd_validate() {
    local file="${1:-}"
    
    if [[ -z "$file" ]]; then
        error "Fichier requis. Usage: ./bnppre.sh validate FILE"
    fi
    
    if [[ ! -f "$file" ]]; then
        error "Fichier introuvable: $file"
    fi
    
    info "Validation de $file..."
    
    python3 "$VALIDATOR" "$file"
    
    success "Validation terminée"
}

# Main
main() {
    if [[ $# -eq 0 ]]; then
        show_help
        exit 0
    fi
    
    local command="$1"
    shift
    
    case "$command" in
        scrape)
            cmd_scrape "$@"
            ;;
        sample)
            cmd_sample "$@"
            ;;
        validate)
            cmd_validate "$@"
            ;;
        -h|--help|help)
            show_help
            ;;
        *)
            error "Commande inconnue: $command\n$(show_help)"
            ;;
    esac
}

main "$@"
