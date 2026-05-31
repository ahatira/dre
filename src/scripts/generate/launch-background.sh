#!/usr/bin/env bash
#
# Lance le scraping et traduction en arrière-plan avec nohup
#
# Usage: ./launch-background.sh
#

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SCRAPE_SCRIPT="${SCRIPT_DIR}/scrape-and-translate-all.sh"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/../../.." && pwd)"
NOHUP_LOG="${PROJECT_ROOT}/logs/nohup_$(date +%Y%m%d_%H%M%S).log"

# Créer le dossier logs
mkdir -p "$(dirname "$NOHUP_LOG")"

echo ""
echo "╔══════════════════════════════════════════════════════════════════╗"
echo "║     LANCEMENT EN ARRIÈRE-PLAN - SCRAPING + TRADUCTION            ║"
echo "╚══════════════════════════════════════════════════════════════════╝"
echo ""
echo "📋 Configuration:"
echo "   Script: $SCRAPE_SCRIPT"
echo "   Logs nohup: $NOHUP_LOG"
echo ""
echo "⏱️  Durée estimée: ~20 heures"
echo ""
echo "🔍 Commandes de suivi:"
echo ""
echo "   # Suivre l'avancement en temps réel"
echo "   tail -f $NOHUP_LOG"
echo ""
echo "   # Suivre le log détaillé"
echo "   tail -f ${PROJECT_ROOT}/logs/scraping_*.log"
echo ""
echo "   # Vérifier le processus"
echo "   ps aux | grep scrape-and-translate-all"
echo ""
echo "   # Arrêter le processus si besoin"
echo "   pkill -f scrape-and-translate-all"
echo ""
echo "══════════════════════════════════════════════════════════════════"
echo ""
read -p "Lancer le processus en arrière-plan maintenant ? [O/n] " -r
echo ""

if [[ ! $REPLY =~ ^[Nn]$ ]]; then
    echo "🚀 Lancement du processus en arrière-plan..."
    
    nohup "$SCRAPE_SCRIPT" > "$NOHUP_LOG" 2>&1 &
    
    PID=$!
    
    echo ""
    echo "✅ Processus lancé!"
    echo "   PID: $PID"
    echo "   Logs: $NOHUP_LOG"
    echo ""
    echo "💡 Pour suivre l'avancement:"
    echo "   tail -f $NOHUP_LOG"
    echo ""
    echo "🔍 Pour vérifier si le processus tourne:"
    echo "   ps -p $PID"
    echo ""
    echo "🛑 Pour arrêter le processus:"
    echo "   kill $PID"
    echo ""
    
    # Attendre 3 secondes et afficher les premières lignes
    sleep 3
    
    echo "📝 Premières lignes du log:"
    echo "────────────────────────────────────────────────────────────────"
    head -20 "$NOHUP_LOG" 2>/dev/null || echo "Logs pas encore générés..."
    echo "────────────────────────────────────────────────────────────────"
    echo ""
    echo "✅ Le processus tourne en arrière-plan."
    echo "   Vous pouvez fermer ce terminal sans problème."
    echo ""
else
    echo "❌ Annulé"
    exit 0
fi
