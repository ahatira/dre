#!/bin/bash
# setup-os.sh - Configure Docker files for current OS
# Detects the operating system and copies appropriate configuration files.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DOCKER_DIR="${SCRIPT_DIR}/docker"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

show_help() {
    cat << 'EOF'
=====================================
setup-os.sh - Configuration Docker par OS
=====================================

DESCRIPTION:
    Détecte le système d'exploitation et applique les fichiers de configuration
    Docker optimisés (docker-compose.yml et zz-performance.ini).

UTILISATION:
    ./setup-os.sh [OPTION]

OPTIONS:
    -h, --help      Affiche cette aide et quitte
    --linux         Force la configuration Linux (Ubuntu/Debian)
    --windows       Force la configuration Windows
    --status        Affiche le système détecté sans modifier les fichiers

FICHIERS GERES:
    Source                    →  Destination
    ------------------------     ------------------------
    docker/docker-compose.linux.yml  →  docker/docker-compose.yml
    docker/docker-compose.windows.yml →  docker/docker-compose.yml
    docker/php/zz-performance.linux.ini → docker/php/zz-performance.ini
    docker/php/zz-performance.windows.ini → docker/php/zz-performance.ini

DIFFERENCES CONFIG:
    Linux (Ubuntu):
        - consistency: cached (meilleur I/O sur Linux)
        - opcache.revalidate_freq=0 (hot-reload rapide)
        - opcache.validate_timestamps=1 (dev)

    Windows:
        - consistency: delegated (optimisation Windows)
        - opcache.revalidate_freq=2 (moins réactif)
        - opcache.validate_timestamps=1 (dev)

EXEMPLES:
    ./setup-os.sh              # Auto-détection et application
    ./setup-os.sh --linux      # Forcer Linux
    ./setup-os.sh --status     # Voir le système détecté

EOF
}

detect_os() {
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        echo "linux"
    elif [[ "$OSTYPE" == "msys" ]] || [[ "$OSTYPE" == "cygwin" ]]; then
        echo "windows"
    else
        echo "unknown"
    fi
}

copy_files() {
    local os="$1"
    local compose_src="${DOCKER_DIR}/docker-compose.${os}.yml"
    local compose_dst="${DOCKER_DIR}/docker-compose.yml"
    local ini_src="${DOCKER_DIR}/php/zz-performance.${os}.ini"
    local ini_dst="${DOCKER_DIR}/php/zz-performance.ini"

    if [[ ! -f "$compose_src" ]]; then
        echo -e "${RED}ERREUR: Fichier source manquant: ${compose_src}${NC}"
        return 1
    fi

    if [[ ! -f "$ini_src" ]]; then
        echo -e "${RED}ERREUR: Fichier source manquant: ${ini_src}${NC}"
        return 1
    fi

    cp "$compose_src" "$compose_dst"
    cp "$ini_src" "$ini_dst"

    echo -e "${GREEN}✓ Configuration ${os} appliquée avec succès${NC}"
    echo -e "  - ${compose_src} → ${compose_dst}"
    echo -e "  - ${ini_src} → ${ini_dst}"
}

main() {
    local force_os=""

    while [[ $# -gt 0 ]]; do
        case $1 in
            -h|--help)
                show_help
                exit 0
                ;;
            --linux)
                force_os="linux"
                shift
                ;;
            --windows)
                force_os="windows"
                shift
                ;;
            --status)
                local detected=$(detect_os)
                echo "Système détecté: ${detected}"
                exit 0
                ;;
            *)
                echo -e "${RED}Option inconnue: $1${NC}"
                show_help
                exit 1
                ;;
        esac
    done

    if [[ -n "$force_os" ]]; then
        copy_files "$force_os"
    else
        local os=$(detect_os)
        if [[ "$os" == "unknown" ]]; then
            echo -e "${YELLOW}⚠ Système non détecté ($OSTYPE), utilisation de Linux par défaut${NC}"
            os="linux"
        fi
        echo -e "${GREEN}✓ Système détecté: ${os}${NC}"
        copy_files "$os"
    fi
}

main "$@"
