#!/usr/bin/env python3
"""Fill ps_search admin/UI catalog gaps."""

from __future__ import annotations

import json
from pathlib import Path

CATALOG = Path(__file__).resolve().parent / "translation_catalog" / "ps_search.json"

PATCHES: dict[str, dict[str, str]] = {
    "Header search (property)": {
        "fr": "Recherche header (bien)", "de": "Header-Suche (Immobilie)", "es": "Búsqueda en cabecera (inmueble)",
        "it": "Ricerca header (immobile)", "lb": "Header-Sich (Immobilie)", "nl": "Headerzoekopdracht (object)", "pl": "Wyszukiwanie w nagłówku (nieruchomość)",
    },
    "Location data": {
        "fr": "Données de localisation", "de": "Standortdaten", "es": "Datos de ubicación",
        "it": "Dati località", "lb": "Standuertdaten", "nl": "Locatiedata", "pl": "Dane lokalizacji",
    },
    "Location suggest": {
        "fr": "Suggestion de localisation", "de": "Standortvorschlag", "es": "Sugerencia de ubicación",
        "it": "Suggerimento località", "lb": "Standuertvirschlag", "nl": "Locatiesuggestie", "pl": "Sugestia lokalizacji",
    },
    "Map zone": {
        "fr": "Zone carte", "de": "Kartenzone", "es": "Zona del mapa",
        "it": "Zona mappa", "lb": "Kaartezon", "nl": "Kaartzone", "pl": "Strefa mapy",
    },
    "OpenRouteService": {
        "fr": "OpenRouteService", "de": "OpenRouteService", "es": "OpenRouteService",
        "it": "OpenRouteService", "lb": "OpenRouteService", "nl": "OpenRouteService", "pl": "OpenRouteService",
    },
    "Properties @in @locality": {
        "fr": "Biens @in @locality", "de": "Immobilien @in @locality", "es": "Inmuebles @in @locality",
        "it": "Immobili @in @locality", "lb": "Immobilie @in @locality", "nl": "Objecten @in @locality", "pl": "Nieruchomości @in @locality",
    },
    "SEO URLs": {
        "fr": "URLs SEO", "de": "SEO-URLs", "es": "URLs SEO",
        "it": "URL SEO", "lb": "SEO-URLs", "nl": "SEO-URL's", "pl": "Adresy URL SEO",
    },
    "Search hero (homepage)": {
        "fr": "Hero recherche (homepage)", "de": "Such-Hero (Homepage)", "es": "Hero de búsqueda (homepage)",
        "it": "Hero ricerca (homepage)", "lb": "Sich-Hero (Homepage)", "nl": "Zoekhero (homepage)", "pl": "Hero wyszukiwania (strona główna)",
    },
    "Feature filters": {
        "fr": "Filtres caractéristiques", "de": "Merkmalfilter", "es": "Filtros de características",
        "it": "Filtri caratteristiche", "lb": "Merkmalfilter", "nl": "Kenmerkfilters", "pl": "Filtry cech",
    },
    "Feature search filters": {
        "fr": "Filtres de recherche caractéristiques", "de": "Merkmal-Suchfilter", "es": "Filtros de búsqueda de características",
        "it": "Filtri ricerca caratteristiche", "lb": "Merkmal-Sichfilter", "nl": "Kenmerkzoekfilters", "pl": "Filtry wyszukiwania cech",
    },
    "Feature: @label": {
        "fr": "Caractéristique : @label", "de": "Merkmal: @label", "es": "Característica: @label",
        "it": "Caratteristica: @label", "lb": "Merkmal: @label", "nl": "Kenmerk: @label", "pl": "Cecha: @label",
    },
    "Ceiling height (m)": {
        "fr": "Hauteur sous plafond (m)", "de": "Deckenhöhe (m)", "es": "Altura bajo techo (m)",
        "it": "Altezza sottotrave (m)", "lb": "Deckenhéicht (m)", "nl": "Vrije hoogte (m)", "pl": "Wysokość pomieszczeń (m)",
    },
    "Distance zone isochrone": {
        "fr": "Zone de distance isochrone", "de": "Distanzzone-Isochrone", "es": "Zona de distancia isócrona",
        "it": "Zona distanza isocrona", "lb": "Distanzzone-Isochrone", "nl": "Afstandzone isochroon", "pl": "Strefa odległości izochronalnej",
    },
    "Divisible": {
        "fr": "Divisible", "de": "Teilbar", "es": "Divisible", "it": "Divisibile", "lb": "Deelbar", "nl": "Deelbaar", "pl": "Podzielny",
    },
}


def main() -> None:
    catalog = json.loads(CATALOG.read_text(encoding="utf-8"))
    applied = 0
    for msgid, langs in PATCHES.items():
        if msgid not in catalog:
            continue
        for lang, text in langs.items():
            catalog[msgid][lang] = text
        applied += 1
    CATALOG.write_text(json.dumps(catalog, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"ps_search admin: applied {applied} patches")


if __name__ == "__main__":
    main()
