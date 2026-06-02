#!/usr/bin/env bash
#
# Script de scraping et traduction complète du site BNPPRE
# Durée estimée: ~20 heures
#
# Usage: ./scrape-and-translate-all.sh

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../../.." && pwd)"
GENERATOR="${SCRIPT_DIR}/generate.py"
OUTPUT_DIR="${PROJECT_ROOT}/data/xml"
LOG_FILE="${PROJECT_ROOT}/logs/scraping_$(date +%Y%m%d_%H%M%S).log"

# Créer les dossiers nécessaires
mkdir -p "$OUTPUT_DIR"
mkdir -p "$(dirname "$LOG_FILE")"

# Fonction de log
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*" | tee -a "$LOG_FILE"
}

log "════════════════════════════════════════════════════════════"
log "DÉBUT DU SCRAPING ET TRADUCTION COMPLÈTE"
log "════════════════════════════════════════════════════════════"
log "Logs: $LOG_FILE"
log ""

# ============================================================================
# ÉTAPE 1: SCRAPING COMPLET DU SITE (FR uniquement)
# ============================================================================

log "🔄 ÉTAPE 1/2: Scraping complet du site bnppre.fr..."
log "   Fichier de sortie: $OUTPUT_DIR/bnppre_all_fr.xml"
log ""

if python3 "$GENERATOR" scrape \
    --output "$OUTPUT_DIR/bnppre_all_fr.xml" \
    2>&1 | tee -a "$LOG_FILE"; then
    
    log ""
    log "✅ Étape 1 terminée: Scraping complet réussi"
    
    # Statistiques du fichier généré
    if [ -f "$OUTPUT_DIR/bnppre_all_fr.xml" ]; then
        SIZE=$(du -h "$OUTPUT_DIR/bnppre_all_fr.xml" | cut -f1)
        log "   Taille du fichier: $SIZE"
        
        # Compter les offres
        OFFER_COUNT=$(grep -c "<OFFER>" "$OUTPUT_DIR/bnppre_all_fr.xml" || echo "0")
        log "   Nombre d'offres: $OFFER_COUNT"
    fi
else
    log "❌ ERREUR: Le scraping a échoué"
    exit 1
fi

log ""
log "════════════════════════════════════════════════════════════"
log ""

# ============================================================================
# ÉTAPE 2: TRADUCTION COMPLÈTE FR→EN
# ============================================================================

log "🔄 ÉTAPE 2/2: Traduction FR→EN de toutes les offres..."
log "   Fichier source: $OUTPUT_DIR/bnppre_all_fr.xml"
log "   Fichier de sortie: $OUTPUT_DIR/bnppre_all_fr_en.xml"
log "   ⚠️  Cette étape peut prendre 12-16 heures"
log ""

if python3 "$GENERATOR" translate \
    --source "$OUTPUT_DIR/bnppre_all_fr.xml" \
    --output "$OUTPUT_DIR/bnppre_all_fr_en.xml" \
    2>&1 | tee -a "$LOG_FILE"; then
    
    log ""
    log "✅ Étape 2 terminée: Traduction complète réussie"
    
    # Statistiques du fichier traduit
    if [ -f "$OUTPUT_DIR/bnppre_all_fr_en.xml" ]; then
        SIZE=$(du -h "$OUTPUT_DIR/bnppre_all_fr_en.xml" | cut -f1)
        log "   Taille du fichier: $SIZE"
    fi
else
    log "❌ ERREUR: La traduction a échoué"
    log "   Le fichier FR est disponible: $OUTPUT_DIR/bnppre_all_fr.xml"
    exit 1
fi

log ""
log "════════════════════════════════════════════════════════════"
log "✅ TERMINÉ - SCRAPING ET TRADUCTION COMPLÈTE"
log "════════════════════════════════════════════════════════════"
log ""
log "📁 Fichiers générés:"
log "   • FR uniquement: $OUTPUT_DIR/bnppre_all_fr.xml"
log "   • FR + EN:       $OUTPUT_DIR/bnppre_all_fr_en.xml"
log ""
log "📝 Logs complets: $LOG_FILE"
log ""
