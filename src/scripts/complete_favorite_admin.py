#!/usr/bin/env python3
"""Fill ps_favorite admin catalog entries that only have French."""

from __future__ import annotations

import json
from pathlib import Path

CATALOG = Path(__file__).resolve().parent / "translation_catalog" / "ps_favorite.json"
LANGS = ("de", "es", "it", "lb", "nl", "pl")

ADMIN: dict[str, dict[str, str]] = {
    "No preview available": {
        "de": "Keine Vorschau verfügbar", "es": "Sin vista previa disponible", "it": "Nessuna anteprima disponibile",
        "lb": "Keng Virschau disponibel", "nl": "Geen preview beschikbaar", "pl": "Brak podglądu",
    },
    "Favorites settings": {
        "de": "Favoriten-Einstellungen", "es": "Ajustes de favoritos", "it": "Impostazioni preferiti",
        "lb": "Favoritten-Astellungen", "nl": "Favorieteninstellingen", "pl": "Ustawienia ulubionych",
    },
    "Current configuration summary": {
        "de": "Zusammenfassung der aktuellen Konfiguration", "es": "Resumen de la configuración actual",
        "it": "Riepilogo configurazione attuale", "lb": "Resumé vun der aktueller Konfiguratioun",
        "nl": "Samenvatting huidige configuratie", "pl": "Podsumowanie bieżącej konfiguracji",
    },
    "Configured favorite targets": {
        "de": "Konfigurierte Favoritenziele", "es": "Objetivos de favoritos configurados",
        "it": "Target preferiti configurati", "lb": "Konfiguréiert Favoritten-Ziler",
        "nl": "Geconfigureerde favorietdoelen", "pl": "Skonfigurowane cele ulubionych",
    },
    "Configured card view modes": {
        "de": "Konfigurierte Karten-Ansichtsmodi", "es": "Modos de vista de tarjeta configurados",
        "it": "Modalità visualizzazione card configurati", "lb": "Konfiguréiert Kaart-Uweisungsmodi",
        "nl": "Geconfigureerde kaartweergavemodi", "pl": "Skonfigurowane tryby widoku kart",
    },
    "No target configured. Favorite buttons remain hidden until at least one target is declared.": {
        "de": "Kein Ziel konfiguriert. Favoriten-Schaltflächen bleiben verborgen, bis mindestens ein Ziel definiert ist.",
        "es": "No hay objetivo configurado. Los botones de favoritos permanecen ocultos hasta declarar al menos un objetivo.",
        "it": "Nessun target configurato. I pulsanti preferiti restano nascosti finché non viene dichiarato almeno un target.",
        "lb": "Kee Zil konfiguréiert. Favoritten-Buttons bleiwen verstoppt.",
        "nl": "Geen doel geconfigureerd. Favorietknoppen blijven verborgen tot minstens één doel is gedefinieerd.",
        "pl": "Brak skonfigurowanego celu. Przyciski ulubionych pozostają ukryte do zadeklarowania co najmniej jednego celu.",
    },
    "No custom view mode configured. The module will try teaser, then fall back to a title link.": {
        "de": "Kein benutzerdefinierter Ansichtsmodus. Das Modul versucht teaser, dann einen Titellink.",
        "es": "Sin modo de vista personalizado. El módulo intentará teaser y luego un enlace al título.",
        "it": "Nessuna modalità personalizzata. Il modulo prova teaser, poi un link sul titolo.",
        "lb": "Kee personaliséierten Uweisungsmodus.", "nl": "Geen aangepaste weergavemodus.", "pl": "Brak niestandardowego trybu widoku.",
    },
    "Favorite-enabled entity targets and limits": {
        "de": "Favoritenfähige Entitätsziele und Limits", "es": "Objetivos de entidad con favoritos y límites",
        "it": "Target entità con preferiti e limiti", "lb": "Favoritten-Entitéitsziler a Limiten",
        "nl": "Favoriet-doelen en limieten", "pl": "Cele encji z ulubionymi i limity",
    },
    "Use one rule per line. Only configured targets are favoritable.": {
        "de": "Eine Regel pro Zeile. Nur konfigurierte Ziele können favorisiert werden.",
        "es": "Una regla por línea. Solo los objetivos configurados pueden marcarse como favoritos.",
        "it": "Una regola per riga. Solo i target configurati possono essere preferiti.",
        "lb": "Eng Regel pro Zeil.", "nl": "Eén regel per regel. Alleen geconfigureerde doelen.", "pl": "Jedna reguła na wiersz.",
    },
    "Expected format": {
        "de": "Erwartetes Format", "es": "Formato esperado", "it": "Formato previsto",
        "lb": "Erwäert Format", "nl": "Verwacht formaat", "pl": "Oczekiwany format",
    },
    "Examples": {
        "de": "Beispiele", "es": "Ejemplos", "it": "Esempi", "lb": "Beispiller", "nl": "Voorbeelden", "pl": "Przykłady",
    },
    "<code>node.offer:0</code> enables favorites for all offer nodes with no limit.": {
        "de": "<code>node.offer:0</code> aktiviert Favoriten für alle Offer-Nodes ohne Limit.",
        "es": "<code>node.offer:0</code> activa favoritos para todos los nodos offer sin límite.",
        "it": "<code>node.offer:0</code> abilita preferiti per tutti i nodi offer senza limite.",
        "lb": "<code>node.offer:0</code> aktivéiert Favoritten ouni Limit.", "nl": "<code>node.offer:0</code> schakelt favorieten in zonder limiet.", "pl": "<code>node.offer:0</code> włącza ulubione bez limitu.",
    },
    "<code>node.article:10</code> enables favorites for article nodes with a limit of 10 items.": {
        "de": "<code>node.article:10</code> aktiviert Favoriten für Artikel-Nodes mit Limit 10.",
        "es": "<code>node.article:10</code> activa favoritos para nodos article con límite de 10.",
        "it": "<code>node.article:10</code> abilita preferiti per nodi article con limite 10.",
        "lb": "<code>node.article:10</code> mat Limit 10.", "nl": "<code>node.article:10</code> met limiet 10.", "pl": "<code>node.article:10</code> z limitem 10.",
    },
    "<code>media.*:5</code> enables favorites for all media bundles with a shared limit of 5 items per bundle rule.": {
        "de": "<code>media.*:5</code> aktiviert Favoriten für alle Media-Bundles mit gemeinsamem Limit 5.",
        "es": "<code>media.*:5</code> activa favoritos para todos los bundles media con límite compartido de 5.",
        "it": "<code>media.*:5</code> abilita preferiti per tutti i bundle media con limite condiviso 5.",
        "lb": "<code>media.*:5</code> mat gemeinsamem Limit 5.", "nl": "<code>media.*:5</code> met gedeelde limiet 5.", "pl": "<code>media.*:5</code> ze wspólnym limitem 5.",
    },
    "Use one rule per line, for example: <code>node.offer:20</code>. Format: <code>entity_type.bundle:max</code>. You may also use <code>entity_type.*:max</code>. Only configured targets are favoritable. Use <code>:0</code> for no limit.": {
        "de": "Eine Regel pro Zeile, z. B. <code>node.offer:20</code>. Format: <code>entity_type.bundle:max</code>. Auch <code>entity_type.*:max</code>. Nur konfigurierte Ziele. <code>:0</code> = kein Limit.",
        "es": "Una regla por línea, ej. <code>node.offer:20</code>. Formato: <code>entity_type.bundle:max</code>. También <code>entity_type.*:max</code>. Solo objetivos configurados. <code>:0</code> sin límite.",
        "it": "Una regola per riga, es. <code>node.offer:20</code>. Formato: <code>entity_type.bundle:max</code>. Anche <code>entity_type.*:max</code>. Solo target configurati. <code>:0</code> senza limite.",
        "lb": "Eng Regel pro Zeil.", "nl": "Eén regel per regel.", "pl": "Jedna reguła na wiersz.",
    },
    "Favorite card view modes": {
        "de": "Favoriten-Karten-Ansichtsmodi", "es": "Modos de vista de tarjeta favorito",
        "it": "Modalità visualizzazione card preferiti", "lb": "Favoritten-Kaart-Uweisungsmodi",
        "nl": "Favorietkaart-weergavemodi", "pl": "Tryby widoku kart ulubionych",
    },
    "Optional. Use one rule per line to force a preferred view mode for favorite cards.": {
        "de": "Optional. Eine Regel pro Zeile für bevorzugten Ansichtsmodus der Favoritenkarten.",
        "es": "Opcional. Una regla por línea para forzar el modo de vista preferido de las tarjetas favorito.",
        "it": "Opzionale. Una regola per riga per forzare la modalità preferita delle card preferiti.",
        "lb": "Optional. Eng Regel pro Zeil.", "nl": "Optioneel. Eén regel per regel.", "pl": "Opcjonalnie. Jedna reguła na wiersz.",
    },
    "Optional. Use one rule per line, for example: <code>node.offer:favorite_card</code>. Format: <code>entity_type.bundle:view_mode</code>. You may also use <code>entity_type.*:view_mode</code>. If no configured view mode exists, the module tries <code>teaser</code>, then falls back to a title link.": {
        "de": "Optional. Eine Regel pro Zeile, z. B. <code>node.offer:favorite_card</code>. Format: <code>entity_type.bundle:view_mode</code>. Fallback: <code>teaser</code>, dann Titellink.",
        "es": "Opcional. Una regla por línea, ej. <code>node.offer:favorite_card</code>. Formato: <code>entity_type.bundle:view_mode</code>. Si no hay modo configurado, intenta <code>teaser</code>, luego enlace al título.",
        "it": "Opzionale. Una regola per riga, es. <code>node.offer:favorite_card</code>. Formato: <code>entity_type.bundle:view_mode</code>. Prova <code>teaser</code>, poi link titolo.",
        "lb": "Optional.", "nl": "Optioneel.", "pl": "Opcjonalnie.",
    },
    "Line @line has an invalid format: @value": {
        "de": "Zeile @line hat ungültiges Format: @value", "es": "La línea @line tiene formato no válido: @value",
        "it": "La riga @line ha formato non valido: @value", "lb": "Zeil @line ongülteg Format: @value",
        "nl": "Regel @line heeft ongeldig formaat: @value", "pl": "Wiersz @line ma nieprawidłowy format: @value",
    },
    "Line @line duplicates an existing target rule: @value": {
        "de": "Zeile @line dupliziert bestehende Zielregel: @value", "es": "La línea @line duplica una regla existente: @value",
        "it": "La riga @line duplica una regola esistente: @value", "lb": "Zeil @line duplizéiert Regel: @value",
        "nl": "Regel @line dupliceert bestaande regel: @value", "pl": "Wiersz @line duplikuje regułę: @value",
    },
    "Line @line has an invalid view mode format: @value": {
        "de": "Zeile @line hat ungültiges Ansichtsmodus-Format: @value", "es": "La línea @line tiene formato de modo de vista no válido: @value",
        "it": "La riga @line ha formato modalità non valido: @value", "lb": "Zeil @line ongülteg Format: @value",
        "nl": "Regel @line heeft ongeldig weergavemodus-formaat: @value", "pl": "Wiersz @line ma nieprawidłowy format trybu widoku: @value",
    },
    "Line @line duplicates an existing view mode rule: @value": {
        "de": "Zeile @line dupliziert bestehende Ansichtsmodus-Regel: @value", "es": "La línea @line duplica una regla de modo de vista: @value",
        "it": "La riga @line duplica una regola di modalità: @value", "lb": "Zeil @line duplizéiert Regel: @value",
        "nl": "Regel @line dupliceert weergavemodus-regel: @value", "pl": "Wiersz @line duplikuje regułę trybu widoku: @value",
    },
    "@target: enabled with no limit": {
        "de": "@target: aktiviert ohne Limit", "es": "@target: activado sin límite", "it": "@target: abilitato senza limite",
        "lb": "@target: aktivéiert ouni Limit", "nl": "@target: ingeschakeld zonder limiet", "pl": "@target: włączony bez limitu",
    },
    "@target: enabled with limit @limit": {
        "de": "@target: aktiviert mit Limit @limit", "es": "@target: activado con límite @limit", "it": "@target: abilitato con limite @limit",
        "lb": "@target: aktivéiert mat Limit @limit", "nl": "@target: ingeschakeld met limiet @limit", "pl": "@target: włączony z limitem @limit",
    },
    "@target: @view_mode": {
        "de": "@target: @view_mode", "es": "@target: @view_mode", "it": "@target: @view_mode",
        "lb": "@target: @view_mode", "nl": "@target: @view_mode", "pl": "@target: @view_mode",
    },
    "Saved 1 favorite target rule.": {
        "de": "1 Favoritenziel-Regel gespeichert.", "es": "1 regla de objetivo favorito guardada.", "it": "1 regola target preferiti salvata.",
        "lb": "1 Favoritten-Zil-Regel gespäichert.", "nl": "1 favorietdoelregel opgeslagen.", "pl": "Zapisano 1 regułę celu ulubionych.",
    },
    "Saved @count favorite target rules.": {
        "de": "@count Favoritenziel-Regeln gespeichert.", "es": "@count reglas de objetivo favorito guardadas.", "it": "@count regole target preferiti salvate.",
        "lb": "@count Favoritten-Zil-Regele gespäichert.", "nl": "@count favorietdoelregels opgeslagen.", "pl": "Zapisano @count reguł celu ulubionych.",
    },
    "Saved 1 view mode rule.": {
        "de": "1 Ansichtsmodus-Regel gespeichert.", "es": "1 regla de modo de vista guardada.", "it": "1 regola modalità salvata.",
        "lb": "1 Uweisungsmodus-Regel gespäichert.", "nl": "1 weergavemodusregel opgeslagen.", "pl": "Zapisano 1 regułę trybu widoku.",
    },
    "Saved @count view mode rules.": {
        "de": "@count Ansichtsmodus-Regeln gespeichert.", "es": "@count reglas de modo de vista guardadas.", "it": "@count regole modalità salvate.",
        "lb": "@count Uweisungsmodus-Regele gespäichert.", "nl": "@count weergavemodusregels opgeslagen.", "pl": "Zapisano @count reguł trybu widoku.",
    },
    "Configure favorites behavior.": {
        "de": "Favoriten-Verhalten konfigurieren.", "es": "Configurar el comportamiento de favoritos.", "it": "Configura comportamento preferiti.",
        "lb": "Favoritten-Verhalen konfiguréieren.", "nl": "Favorietgedrag configureren.", "pl": "Skonfiguruj zachowanie ulubionych.",
    },
}


def main() -> None:
    catalog = json.loads(CATALOG.read_text(encoding="utf-8"))
    applied = 0
    for msgid, langs in ADMIN.items():
        if msgid not in catalog:
            continue
        for lang, text in langs.items():
            catalog[msgid][lang] = text
        if "fr" not in catalog[msgid]:
            catalog[msgid]["fr"] = msgid
        applied += 1
    CATALOG.write_text(json.dumps(catalog, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"ps_favorite admin: applied {applied} patches")


if __name__ == "__main__":
    main()
